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
require_once cot_incfile('cotlib', 'plug');

/**
* Generates PageList widget
* @param  string  $tpl        01. Template code
* @param  integer $items      02. Number of items to show. 0 - all items
* @param  string  $order      03. Sorting order (SQL)
* @param  string  $extra      04. Custom selection filter (SQL)
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
function sedby_pagelist($tpl = 'pagelist', $items = 0, $order = '', $extra = '', $mode = '', $cats = '', $subs = 0, $noself = 0, $offset = 0, $pagination = '', $ajax_block = '', $cache_name = '', $cache_ttl = '') {

  $enableAjax = $enableCache = $enablePagination = false;

  // Condition shortcut
  if (Cot::$cache && !empty($cache_name) && ((int)$cache_ttl > 0)) {
    $enableCache = true;
    $cache_name = str_replace(' ', '_', $cache_name);
  }

  if ($enableCache && Cot::$cache->db->exists($cache_name, SEDBY_PAGELIST_REALM)) {
    $output = Cot::$cache->db->get($cache_name, SEDBY_PAGELIST_REALM);
  } else {

    /* === Hook === */
    foreach (cot_getextplugins('pagelist.first') as $pl) {
      include $pl;
    }
    /* ===== */

    // Condition shortcuts
    if (!$enableCache && !empty($pagination) && ((int)$items > 0)) {
      $enablePagination = true;
    }

    if ((Cot::$cfg['turnajax']) && (Cot::$cfg['plugin']['pagelist']['ajax']) && !empty($ajax_block)) {
      $enableAjax = true;
    }

    // DB tables shortcuts
    $db_pages = Cot::$db->pages;

    // Display the items
    (!isset($tpl) || empty($tpl)) && $tpl = 'pagelist';
    $t = new XTemplate(cot_tplfile($tpl, 'plug'));

    // Get pagination if necessary
    if ($enablePagination) {
      list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);
    } else {
      $d = 0;
    }

    // Compile items number
    ((int)$offset <= 0) && $offset = 0;
    $d = $d + (int)$offset;
    $sql_limit = ($items > 0) ? "LIMIT $d, $items" : "";

    // Compile order
    $sql_order = empty($order) ? "" : " ORDER BY $order";

    // Compile all conditions
    $sql_state = Cot::$cfg['plugin']['pagelist']['published_only'] ? "page_state = 0" : "";
    $sql_cats = sedby_compilecats($mode, $cats, (bool)$subs);
    $sql_extra = (empty($extra)) ? "" : $extra;

    if (($noself == true) && defined('COT_PAGES') && !defined('COT_LIST')) {
      global $id;
      $sql_noself = "page_id != " . $id;
    }

    $sql_cond = sedby_build_where(array($sql_state, $sql_cats, $sql_extra, $sql_noself));

    $pagelist_join_columns = "";
    $pagelist_join_tables = "";

    // Users Module Support
    if (Cot::$cfg['plugin']['pagelist']['usertags']) {
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
    foreach (cot_getextplugins('pagelist.query') as $pl)
    {
      include $pl;
    }
    /* ===== */

    $query = "SELECT p.* $pagelist_join_columns FROM $db_pages AS p $pagelist_join_tables $sql_cond $sql_order $sql_limit";
    $res = Cot::$db->query($query);
    $jj = 1;

    /* === Hook - Part 1 === */
    $extp = cot_getextplugins('pagelist.loop');
    /* ===== */

    while ($row = $res->fetch()) {
      $t->assign(cot_generate_pagetags($row, 'PAGE_ROW_'));

      if (Cot::$cfg['plugin']['pagelist']['usertags']) {
        $t->assign(cot_generate_usertags($row, 'PAGE_ROW_USER_'));
      }

      $t->assign(array(
        'PAGE_ROW_NUM'        => $jj,
        'PAGE_ROW_ODDEVEN'    => cot_build_oddeven($jj),
        'PAGE_ROW_RAW'        => $row,

        'PAGE_ROW_TEXT_PLAIN' => strip_tags(cot_parse($row['page_text'])),
      ));

      /* === Hook - Part 2 === */
      foreach ($extp as $pl) {
        include $pl;
      }
      /* ===== */

      $t->parse("MAIN.PAGE_ROW");
      $jj++;
    }

    // Render pagination if needed
    if ($enablePagination) {
      $totalitems = Cot::$db->query("SELECT p.* FROM $db_pages AS p $sql_cond")->rowCount();

      $url_area = sedby_geturlarea();
      $url_params = sedby_geturlparams();
      $url_params[$pagination] = $durl;

      if ($enableAjax) {
        $ajax_mode = true;
        $ajax_plug = 'plug';
        if (Cot::$cfg['plugin']['pagelist']['encrypt_ajax_urls']) {
          $h = $tpl . ',' . $items . ',' . $order. ',' . $extra . ',' . $mode . ',' . $cats . ',' . $subs . ',' . $noself . ',' . $offset . ',' . $pagination . ',' . $ajax_block . ',' . $cache_name . ',' . $cache_ttl;
          $h = sedby_encrypt_decrypt('encrypt', $h, Cot::$cfg['plugin']['pagelist']['encrypt_key'], Cot::$cfg['plugin']['pagelist']['encrypt_iv']);
          $h = str_replace('=', '', $h);
          $ajax_plug_params = "r=pagelist&h=$h";
        } else {
          $ajax_plug_params = "r=pagelist&tpl=$tpl&items=$items&order=$order&extra=$extra&mode=$mode&cats=$cats&subs=$subs&noself=$noself&offset=$offset&pagination=$pagination&ajax_block=$ajax_block&cache_name=$cache_name&cache_ttl=$cache_ttl";
        }
      } else {
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

    // Assign service tags
    if ((!$enableCache) && (Cot::$usr['maingrp'] == 5)) {
      $t->assign(array(
        'PAGE_TOP_QUERY' => $query,
        'PAGE_TOP_RES' => $res,
      ));
    }

    ($jj==1) && $t->parse("MAIN.NONE");

    /* === Hook === */
    foreach (cot_getextplugins('pagelist.tags') as $pl)
    {
      include $pl;
    }
    /* ===== */

    $t->parse();
    $output = $t->text();

    if (($jj > 1) && $enableCache) {
      Cot::$cache->db->store($cache_name, $output, SEDBY_PAGELIST_REALM, (int)$cache_ttl);
    }
  }
  return $output;
}
