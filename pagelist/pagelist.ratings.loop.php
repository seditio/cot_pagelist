<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pagelist.loop
[END_COT_EXT]
==================== */

/**
* PageList Plugin / Ratings Support
*
* @package PageList
* @author Vladimir Sibirov / Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

global $cfg;
if ($cfg['plugin']['pagelist']['ratings'] && cot_plugin_active('ratings'))
{
	require_once cot_incfile('ratings', 'plug');
	list ($ratings_display, $ratings_average, $ratings_count) = cot_ratings_display('page', $row['page_id'], $row['page_cat'], true);
	$t->assign(array(
		'PAGE_ROW_RATINGS_DISPLAY' => $ratings_display,
		'PAGE_ROW_RATINGS_AVERAGE' => $ratings_average,
		'PAGE_ROW_RATINGS_COUNT'   => $ratings_count
	));
}
