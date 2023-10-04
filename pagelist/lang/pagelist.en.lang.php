<?php
/**
* Pagelist Plugin / EN Locale
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');

/**
 * Plugin Info
 */

$L['info_name'] = '[SEDBY] Pagelist';
$L['info_desc'] = 'Function to build Page Module widgets';

/**
 * Plugin Config
 */

$L['cfg_useajax'] = 'Use AJAX:';
$L['cfg_ajax'] = 'Use AJAX for pagination';
$L['cfg_ajax_hint'] = 'Works only with $ajax_block and $cfg[\'turnajax\']';
$L['cfg_encrypt_ajax_urls'] = 'Encode AJAX pagination URLs';
$L['cfg_encrypt_ajax_urls_hint'] = 'Works only with AJAX pagination, recommended for live projects and with $extra argument used with AJAX';
$L['cfg_encrypt_key'] = 'Secret key';
$L['cfg_encrypt_iv'] = 'Initialization vector';

$L['cfg_gentags'] = 'Generate TPL-tags:';
$L['cfg_usertags'] = 'Generate TPL-tags for the Users module';
$L['cfg_comments'] = 'Generate TPL-tags for the Comments plugin';
$L['cfg_ratings'] = 'Generate TPL-tags for the Star Ratings plugin';
$L['cfg_thanks'] = 'Generate TPL-tags for the Thanks plugin';

$L['cfg_misc'] = 'Misc';
$L['cfg_published_only'] = 'Output only published pages';
$L['cfg_published_only_hint'] = 'Otherwise specify page_state value in the $extra argument';

/**
 * Plugin Tools
 */

/**
 * Plugin Body
 */
