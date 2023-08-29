<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
* Pagelist Plugin / AJAX
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

/* === Hook === */
foreach (array_merge(cot_getextplugins('pagelist.ajax.first')) as $pl) {
  include $pl;
}
/* ===== */

if (Cot::$cfg['plugin']['pagelist']['encrypt_ajax_urls'] == 1) {
  $params = cot_import('h', 'G', 'TXT');
  $params = cot_encrypt_decrypt('decrypt', $params, Cot::$cfg['plugin']['pagelist']['encrypt_key'], Cot::$cfg['plugin']['pagelist']['encrypt_iv']);
  $params = explode(',', $params);

  $tpl = $params[0];
  $items = $params[1];
  $order = $params[2];
  $extra = $params[3];
  $mode = $params[4];
  $cats = $params[5];
  $subs = $params[6];
  $noself = $params[7];
  $offset = $params[8];
  $pagination = $params[9];
  $ajax_block = $params[10];
  $cache_name = $params[11];
  $cache_ttl = $params[12];
}
else {
  $tpl = cot_import('tpl','G','TXT');
  $items = cot_import('items','G','INT');
  $order = cot_import('order','G','TXT');
  $extra = cot_import('extra','G','TXT');
  $mode = cot_import('mode','G','TXT');
  $cats = cot_import('cats','G','TXT');
  $subs = cot_import('subs','G','TXT');
  $noself = cot_import('noself','G','INT');
  $offset = cot_import('offset','G','INT');
  $pagination = cot_import('pagination','G','TXT');
  $ajax_block = cot_import('ajax_block','G','TXT');
  $cache_name = cot_import('cache_name','G','TXT');
  $cache_ttl = cot_import('cache_ttl','G','INT');
}

ob_clean();
echo cot_pagelist($tpl, $items, $order, $extra, $mode, $cats, $subs, $noself, $offset, $pagination, $ajax_block, $cache_name, $cache_ttl);
ob_flush();
exit;
