<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pagelist.loop
[END_COT_EXT]
==================== */

/**
* Pagelist Plugin / Loop for thanks
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['pagelist']['thanks'] && cot_plugin_active('thanks')) {
	require_once cot_incfile('thanks', 'plug', 'api');
	$t->assign(array(
		'PAGE_ROW_THANKS_COUNT' => thanks_count('page', $row['page_id']),
	));
}
