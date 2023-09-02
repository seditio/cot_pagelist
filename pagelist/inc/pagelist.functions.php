<?php
/**
* Pagelist Plugin / Functions
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

// define globals
define('SEDBY_PAGELIST_REALM', '[SEDBY] Pagelist');

require_once cot_incfile('page', 'module');

/**
 * Encrypts or decrypts string
 *
 * @param		string	$action	01.	Action (encrypt || decrypt)
 * @param		string	$string	02.	String to encrypt / decrypt
 * @param		string	$key		03. Secret key
 * @param		string	$iv			04. Initialization vector
 * @param		string	$method	05. Encryption method (optional)
 * @return	string					Encrypted / decrypted string
 */
 if (!function_exists('cot_encrypt_decrypt')) {
	function cot_encrypt_decrypt($action, $string, $key, $iv, $method = '') {
		$method = empty($method) ? 'AES-256-CBC' : $method;
		$key = hash('sha256', $key);
		$iv = substr(hash('sha256', $iv), 0, 16);

		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		elseif ($action == 'decrypt') {
			$output = base64_decode($string);
			$output = openssl_decrypt($output, $method, $key, 0, $iv);
		}
		return $output;
	}
 }

/**
 * Converts multidimensional array to string
 *
 * @param		string	$glue		01. Separator
 * @param		string	$array	02. Array
 * @return	string					List of array(s) values
 */
function cot_implode_all($glue, $array) {
	for ($i = 0; $i < count($array); $i++) {
		if (@is_array($array[$i]))
		  $array[$i] = cot_implode_all($glue, $array[$i]);
	}
	return implode($glue, $array);
}

/**
 * Returns condition as SQL string
 *
 * @param		string	$cc_mode	01. Selection mode: single, array, black or white
 * @param		string	$cc_cats	02. Category (or categories in double quotes, comma separated)
 * @param		bool		$cc_subs	03. Include subcategories
 * @return	string						Condition as SQL string
 */
function cot_compilecats($cc_mode, $cc_cats, $cc_subs) {

	if (!empty($cc_cats) && ($cc_mode == 'single' || $cc_mode == 'array_white' || $cc_mode == 'array_black' || $cc_mode == 'white' || $cc_mode == 'black')) {
		$cc_cats = str_replace(' ', '', $cc_cats);

		if ($cc_mode == 'single') {
			if ($cc_subs == false) {
				$cc_where = "page_cat = " . Cot::$db->quote($cc_cats);
			}
			else {
				$cc_cats = cot_structure_children('page', $cc_cats, $cc_subs);
				$cc_where = ($cc_cats > 1) ? "page_cat IN ('" . implode("','", $cc_cats) . "')" : "AND page_cat = " . Cot::$db->quote($cc_cats[0]);
			}
		}
    elseif (($cc_mode == 'array_white') || $cc_mode == 'array_black') {
      $what = ($cc_mode == 'array_black') ? "NOT" : "";
      if ($cc_subs == false) {
				$cc_cats = '"'.implode('","', $cc_cats).'"';
	      $cc_where = "page_cat " . $what . " IN ($cc_cats)";
			}
			else {
				$tempcats = array();
				foreach ($cc_cats as $value) {
					$tempcats[] = cot_structure_children('page', $value, true);
				}
				$cc_where = "page_cat " . $what . " IN ('" . cot_implode_all("','", $tempcats) . "')";
			}
    }
		elseif (($cc_mode == 'white') || $cc_mode == 'black') {
			$what = ($cc_mode == 'black') ? "NOT" : "";
			$cc_cats = explode(';', $cc_cats);
			if ($cc_subs == false) {
				$cc_where = "page_cat " . $what . " IN ('" . implode("','", $cc_cats) . "')";
			}
			else {
				$tempcats = array();
				foreach ($cc_cats as $value) {
					$tempcats[] = cot_structure_children('page', $value, true);
				}
				$cc_where = "page_cat " . $what . " IN ('" . cot_implode_all("','", $tempcats) . "')";
			}
		}
	}
	else
		$cc_where = '';

	return $cc_where;
}

/**
 * Generates PageList widget
 * @param  string  $tpl        01. Template code
 * @param  integer $items      02. Number of items to show. 0 - all items
 * @param  string  $order      03. Sorting order (SQL)
 * @param  string  $extra		   04. Custom selection filter (SQL)
 * @param  string  $mode       05. Category selection mode (single, array, white, black)
 * @param  string  $cats       06. Category (categories, semicolon separated)
 * @param  boolean $subs       07. Include subcategories TRUE/FALSE
 * @param  boolean $noself     08. Exclude the current page from the rowset for pages
 * @param  int     $offset     09. Exclude specified number of records starting from the beginning
 * @param  string  $pagination 10. Pagination parameter name for the URL, e.g. 'pld'. Make sure it does not conflict with other paginations
 * @param  string  $ajax_block 11. DOM block ID for ajax pagination
 * @param  string  $cache_name 12. Cache name
 * @param  string  $cache_ttl  13. Cache TTL
 * @return string              Parsed HTML
 */
function cot_pagelist($tpl = 'pagelist', $items = 0, $order = '', $extra = '', $mode = '', $cats = '', $subs = 0, $noself = 0, $offset = 0, $pagination = '', $ajax_block = '', $cache_name = '', $cache_ttl = '') {

	$cache_name = (!empty($cache_name)) ? str_replace(' ', '_', $cache_name) : '';

	if (Cot::$cache && !empty($cache_name) && Cot::$cache->db->exists($cache_name, SEDBY_PAGELIST_REALM))
		$output = Cot::$cache->db->get($cache_name, SEDBY_PAGELIST_REALM);
	else {

		/* === Hook === */
		foreach (array_merge(cot_getextplugins('pagelist.first')) as $pl) {
			include $pl;
		}
		/* ===== */

		if (Cot::$cfg['plugin']['pagelist']['encrypt_ajax_urls'] == 1) {
			$h = $tpl . ',' . $items . ',' . $order. ',' . $extra . ',' . $mode . ',' . $cats . ',' . $subs . ',' . $noself . ',' . $offset . ',' . $pagination . ',' . $ajax_block . ',' . $cache_name . ',' . $cache_ttl;
			$h = cot_encrypt_decrypt('encrypt', $h, Cot::$cfg['plugin']['pagelist']['encrypt_key'], Cot::$cfg['plugin']['pagelist']['encrypt_iv']);
      $h = str_replace('=', '', $h);
		}

    $db_pages = Cot::$db->pages;

		// Display the items
		$t = new XTemplate(cot_tplfile($tpl, 'plug'));

		// Get pagination if necessary
		if (!empty($pagination))
			list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
		else
			$d = 0;

    // Compile items number
		$d = $d + $offset;
		$sql_limit = ($items > 0) ? "LIMIT $d, $items" : "";

    // Compile order
		$sql_order = empty($order) ? "" : " ORDER BY $order";

    $sql_state = "WHERE page_state = 0";

		$sql_cond = cot_compilecats($mode, $cats, (bool)$subs);
		$sql_cond = empty($sql_cond) ? "" : " AND " . $sql_cond;

		$sql_cond .= (empty($extra)) ? "" : " AND $extra";

		if (($noself == true) && defined('COT_PAGES') && !defined('COT_LIST'))
		  $sql_cond .= " AND page_id != " . Cot::$id;

		$pagelist_join_columns = "";
		$pagelist_join_tables = "";

		// Users Module Support
		if (Cot::$cfg['plugin']['pagelist']['users'] == 1) {
      $db_users = Cot::$db->users;
			$pagelist_join_columns .= " , u.* ";
			$pagelist_join_tables .= " LEFT JOIN $db_users AS u ON u.user_id = p.page_ownerid ";
		}

		// Add i18n features if installed
		if (cot_plugin_active('i18n')) {
      $db_i18n_pages = Cot::$db->i18n_pages;
      $db_i18n_locale = Cot::$db->i18n_locale;
			$pagelist_join_columns .= " , i18n.* ";
			$pagelist_join_tables .= " LEFT JOIN $db_i18n_pages AS i18n ON i18n.ipage_id = p.page_id AND i18n.ipage_locale = $db_i18n_locale AND i18n.ipage_id IS NOT NULL ";
		}

		/* === Hook === */
		foreach (array_merge(cot_getextplugins('pagelist.query')) as $pl) {
			include $pl;
		}
		/* ===== */

		$query = "SELECT p.* $pagelist_join_columns FROM $db_pages AS p $pagelist_join_tables $sql_state $sql_cond $sql_order $sql_limit";
		$res = Cot::$db->query($query);
		$jj = 1;

		while ($row = $res->fetch()) {
		  $t->assign(cot_generate_pagetags($row, 'PAGE_ROW_'));

		  if (Cot::$cfg['plugin']['pagelist']['users'] == 1)
		    $t->assign(cot_generate_usertags($row, 'PAGE_ROW_OWNER_'));

			$t->assign(array(
				'PAGE_ROW_NUM'     => $jj,
				'PAGE_ROW_ODDEVEN' => cot_build_oddeven($jj),
				'PAGE_ROW_RAW'     => $row
			));

			/* === Hook === */
			foreach (cot_getextplugins('pagelist.loop') as $pl) {
				include $pl;
			}
			/* ===== */

		  $t->parse("MAIN.PAGE_ROW");
		  $jj++;
		}

		// Render pagination if needed
		if (!empty($pagination)) {
			$totalitems = Cot::$db->query("SELECT COUNT(*) FROM $db_pages $sql_state $sql_cond")->fetchColumn();

      $url_area = defined('COT_PLUG') ? 'plug' : Cot::$env['ext'];

			if (defined('COT_LIST')) {
				global $list_url_path;
				$url_params = $list_url_path;
			}
			elseif (defined('COT_PAGES')) {
				global $al, $id, $pag;
				$url_params = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) :  array('c' => $pag['page_cat'], 'al' => $al);
			}
			elseif(defined('COT_USERS')) {
				global $m;
				$url_params = empty($m) ? array() :  array('m' => $m);
			}
			elseif (defined('COT_ADMIN')) {
				$url_area = 'admin';
				global $m, $p, $a;
				$url_params = array('m' => $m, 'p' => $p, 'a' => $a);
			}
			else
				$url_params = array();

			$url_params[$pagination] = $durl;

			if ((Cot::$cfg['turnajax'] == 1) && (Cot::$cfg['plugin']['pagelist']['ajax'] == 1) && !empty($ajax_block)) {
				$ajax_mode = true;
				$ajax_plug = 'plug';
				if (Cot::$cfg['plugin']['pagelist']['encrypt_ajax_urls'] == 1)
					$ajax_plug_params = "r=pagelist&h=$h";
				else
					$ajax_plug_params = "r=pagelist&tpl=$tpl&items=$items&order=$order&extra=$extra&mode=$mode&cats=$cats&subs=$subs&noself=$noself&offset=$offset&pagination=$pagination&ajax_block=$ajax_block&cache_name=$cache_name&cache_ttl=$cache_ttl";
			}
			else {
				$ajax_mode = false;
				$ajax_plug = $ajax_plug_params = '';
			}

			$pagenav = cot_pagenav($url_area, $url_params, $d, $totalitems, $items, $pagination, '', $ajax_mode, $ajax_block, $ajax_plug, $ajax_plug_params);

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

		if ($jj==1)
			$t->parse("MAIN.NONE");

		/* === Hook === */
		foreach (cot_getextplugins('pagelist.tags') as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse();
		$output = $t->text();

		if (Cot::$cache && ($jj > 1) && empty($pagination) && !empty($cache_name) && !empty($cache_ttl) && ($cache_ttl > 0))
		Cot::$cache->db->store($cache_name, $output, SEDBY_PAGELIST_REALM, (int)$cache_ttl);
	}
	return $output;
}
