<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pagelist.loop
[END_COT_EXT]
==================== */

/**
* Pagelist Plugin / Loop for ratings
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['pagelist']['comments'] && cot_plugin_active('comments')) {
	require_once cot_incfile('comments', 'plug');
	$t->assign('PAGE_ROW_COMMENTS_COUNT', cot_comments_count('page', $row['page_id']));
}
