<?php
/**
* PageList Plugin / Functions
*
* @package PageList
* @author Vladimir Sibirov / Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

/**
 * Converts multidimensional array to string
 * @param		string	$glue Separator
 * @param		string	$array Array
 * @return	string	List of array(s) values
 */
function cot_implode_all($glue, $array){
	for ($i = 0; $i < count($array); $i++) {
		if (@is_array($array[$i]))
		$array[$i] = cot_implode_all ($glue, $array[$i]);
	}
	return implode($glue, $array);
}

/**
 * Returns condition as SQL string
 * @param		string	$cc_mode Selection mode: single, array, black or white
 * @param		string	$cc_cats Category (or categories in double quotes, comma separated)
 * @param		bool		$cc_subs Include subcategories
 * @return	string	Condition as SQL string
 */
function cot_compilecats($cc_mode, $cc_cats, $cc_subs) {

	if (!empty($cc_cats) && ($cc_mode == 'single' || $cc_mode == 'array' || $cc_mode == 'white' || $cc_mode == 'black')) {
		$cc_cats = str_replace(' ', '', $cc_cats);

		if ($cc_mode == 'single') {
			if ($cc_subs == false) {
				$cc_where = "AND page_cat = " . Cot::$db->quote($cc_cats);
			}
			else {
				$cc_cats = cot_structure_children('page', $cc_cats, $cc_subs);
				$cc_where = ($cc_cats > 1) ? "AND page_cat IN ('" . implode("','", $cc_cats) . "')" : "AND page_cat = " . Cot::$db->quote($cc_cats[0]);
			}
		}
    elseif ($cc_mode == 'array') {
      if ($cc_subs == false) {
				$cc_cats = '"'.implode('","', $cc_cats).'"';
	      $cc_where = " AND page_cat IN ($cc_cats)";
			}
			else {
				$tempcats = array();
				foreach ($cc_cats as $value) {
					$tempcats[] = cot_structure_children('page', $value, true);
				}
				$cc_where = "AND page_cat IN ('" . cot_implode_all("','", $tempcats) . "')";
			}
    }
		else {
			$what = ($cc_mode == 'black') ? "NOT" : "";
			$cc_cats = explode(';', $cc_cats);
			if ($cc_subs == false) {
				$cc_where = "AND page_cat " . $what . " IN ('" . implode("','", $cc_cats) . "')";
			}
			else {
				$tempcats = array();
				foreach ($cc_cats as $value) {
					$tempcats[] = cot_structure_children('page', $value, true);
				}
				$cc_where = "AND page_cat " . $what . " IN ('" . cot_implode_all("','", $tempcats) . "')";
			}
		}
	}
	else {
		$cc_where = '';
	}
	return $cc_where;
}

/**
 * Generates PageList widget
 * @param  string  $tpl        01. Template code
 * @param  integer $items      02. Number of items to show. 0 - all items
 * @param  string  $order      03. Sorting order (SQL)
 * @param  string  $condition  04. Custom selection filter (SQL)
 * @param  string  $mode       05. Ctegory selection mode (single, array, white, black)
 * @param  string  $cats       06. Category [list, semicolon separated]
 * @param  boolean $subs       07. Include subcategories TRUE/FALSE
 * @param  string  $pagination 08. Pagination parameter name for the URL, e.g. 'pld'. Make sure it does not conflict with other paginations.
 * @param  boolean $noself     09. Exclude the current page from the rowset for pages.
 * @param  int     $offset     10. Exclude specified number of records
 * @return string              Parsed HTML
 */
function cot_pagelist($tpl = 'pagelist', $items = 0, $order = '', $condition = '', $mode = '', $cats = '', $subs = FALSE, $pagination = NULL, $noself = FALSE, $offset = 0, $ajax_block = 'pagelist_ajax', $cache_name = '', $cache_ttl = '') {

	/* === Hook === */
	foreach (array_merge(cot_getextplugins('pagelist.first')) as $pl)
	{
		include $pl;
	}
	/* ===== */

	$where_cat = cot_compilecats($mode, $cats, (bool)$subs);
	$where_condition = (empty($condition)) ? '' : " AND $condition";

	if (($noself === TRUE) && defined('COT_PAGES') && !defined('COT_LIST')) {
		global $id;
		$where_condition .= " AND page_id != $id";
	}

	// Get pagination if necessary
	if (!is_null($pagination)) {
		list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
	}
	else {
		$d = 0;
	}

	// Display the items
	$t = new XTemplate(cot_tplfile($tpl, 'plug'));

	$pagelist_join_columns = '';
	$pagelist_join_tables = '';

	// Users Module Support
	if (Cot::$cfg['plugin']['pagelist']['users']) {
		$pagelist_join_columns .= ' , u.* ';
		$pagelist_join_tables .= ' LEFT JOIN '.Cot::$db->users.' AS u ON u.user_id = p.page_ownerid ';
	}

	// Add i18n features if installed
	if (cot_plugin_active('i18n')) {
		$pagelist_join_columns .= ' , i18n.* ';
		$pagelist_join_tables .= ' LEFT JOIN '.Cot::$db->i18n_pages.' AS i18n ON i18n.ipage_id=p.page_id AND i18n.ipage_locale="'.Cot::$db->i18n_locale.'" AND i18n.ipage_id IS NOT NULL ';
	}

	/* === Hook === */
	foreach (array_merge(cot_getextplugins('pagelist.query')) as $pl) {
		include $pl;
	}
	/* ===== */

	$sql_order = empty($order) ? '' : " ORDER BY $order";

	$d = $d + $offset;
	$sql_limit = ($items > 0) ? "LIMIT $d, $items" : '';

	$res = Cot::$db->query("SELECT p.* $pagelist_join_columns
		FROM " . Cot::$db->pages . "
		AS p
		$pagelist_join_tables
		WHERE page_state='0'
		$where_cat
		$where_condition
		$sql_order
		$sql_limit");

	$totalitems = Cot::$db->query("SELECT COUNT(*)
		FROM " . Cot::$db->pages . "
		AS p $pagelist_join_tables
		WHERE page_state='0'
		$where_cat
		$where_condition")->fetchColumn();

	$jj = 1;
	while ($row = $res->fetch()) {
		$t->assign(cot_generate_pagetags($row, 'PAGE_ROW_'));
		$t->assign(array(
			'PAGE_ROW_NUM'     => $jj,
			'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),
			'PAGE_ROW_RAW'     => $row
		));

		if (Cot::$cfg['plugin']['pagelist']['users']) {
			$t->assign(cot_generate_usertags($row, 'PAGE_ROW_OWNER_'));
		}

		/* === Hook === */
		foreach (cot_getextplugins('pagelist.loop') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse("MAIN.PAGE_ROW");
		$jj++;
	}

	// Render pagination if needed
	if (!is_null($pagination)) {
		$url_area = defined('COT_PLUG') ? 'plug' : Cot::$env['ext'];
		if (defined('COT_LIST')) {
			global $list_url_path;
			$url_params = $list_url_path;
		}
		elseif (defined('COT_PAGES')) {
			global $al, $id, $pag;
			$url_params = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) :  array('c' => $pag['page_cat'], 'al' => $al);
		}
		elseif (defined('COT_ADMIN')) {
      $url_area = 'admin';
      global $m, $p, $a;
			$url_params = array('m' => $m, 'p' => $p, 'a' => $a);
		}
		else {
			$url_params = array();
		}
		$url_params[$pagination] = $durl;
		$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination);

		// Assign pagination tags
		$t->assign(array(
			'PAGE_TOP_PAGINATION'  => $pagenav['main'],
			'PAGE_TOP_PAGEPREV'    => $pagenav['prev'],
			'PAGE_TOP_PAGENEXT'    => $pagenav['next'],
			'PAGE_TOP_FIRST'       => $pagenav['first'],
			'PAGE_TOP_LAST'        => $pagenav['last'],
			'PAGE_TOP_CURRENTPAGE' => $pagenav['current'],
			'PAGE_TOP_TOTALLINES'  => $totalitems,
			'PAGE_TOP_MAXPERPAGE'  => $items,
			'PAGE_TOP_TOTALPAGES'  => $pagenav['total']
		));
	}

	/* === Hook === */
	foreach (cot_getextplugins('pagelist.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse();
	return $t->text();
}
