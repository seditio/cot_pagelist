<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
* Pagelist Plugin / Ajax
*
* @package Pagelist
* @author Vladimir Sibirov / Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

$tpl = cot_import('tpl','G','TXT');
$items = cot_import('items','G','INT');
$order = cot_import('order','G','TXT');
$extra = cot_import('extra','G','TXT');
$mode = cot_import('mode','G','TXT');
$cats = cot_import('cats','G','TXT');
$subs = cot_import('subs','G','TXT');
$pagination = cot_import('pagination','G','TXT');
$noself = cot_import('noself','G','INT');
$offset = cot_import('offset','G','INT');
$ajax_block = cot_import('ajax_block','G','TXT');
$cache_name = cot_import('cache_name','G','TXT');
$cache_ttl = cot_import('cache_ttl','G','INT');

ob_clean();
echo cot_pagelist($tpl, $items, $order, $extra, $mode, $cats, $subs, $pagination, $noself, $offset, $ajax_block, $cache_name, $cache_ttl);
ob_flush();
exit;
