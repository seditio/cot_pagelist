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

$L['cfg_gentags'] = 'Генерация тегов:';
$L['cfg_users'] = 'Создавать теги для модуля Users';
$L['cfg_ratings'] = 'Создавать теги для плагина Star Ratings';

/**
 * Plugin Tools
 */

/**
 * Plugin Body
 */
