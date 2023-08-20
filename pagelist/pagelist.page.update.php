<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.add.done, page.edit.delete.done, page.edit.update.done
[END_COT_EXT]
==================== */

/**
* Pagelist Plugin / Page add / update / delete
*
* @package Pagelist
* @author Vladimir Sibirov / Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

Cot::$cache && Cot::$cache->clear_realm(SEDBY_PAGELIST_REALM, COT_CACHE_TYPE_ALL);
