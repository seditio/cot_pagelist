<?
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
* Pagelist Plugin / Resources (pagination)
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

// Redefine Pagination
$R['link_pagenav_main'] = '<li class="page-item"><a href="{$url}" class="page-link{$event}"{$rel}>{$num}</a></li>';
$R['link_pagenav_current'] = '<li class="page-item active"><a href="{$url}" class="page-link{$event}"{$rel}>{$num}</a></li>';
$R['link_pagenav_prev'] = '<li class="page-item previous"><a href="{$url}" class="page-link{$event}"{$rel}>'.$R['icon-chevron-left'].'</a></li>';
$R['link_pagenav_next'] = '<li class="page-item next"><a href="{$url}" class="page-link{$event}"{$rel}>'.$R['icon-chevron-right'].'</a></li>';
$R['link_pagenav_first'] = '<li class="page-item"><a href="{$url}" class="page-link{$event}"{$rel}>'.$R['icon-arrow-left'].'</a></li>';
$R['link_pagenav_last'] = '<li class="page-item"><a href="{$url}" class="page-link{$event}"{$rel}>'.$R['icon-arrow-right'].'</a></li>';
$R['link_pagenav_gap'] = '<li class="page-item gap"><span>...</span></li>';