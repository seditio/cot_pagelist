<?php
/**
* Pagelist Plugin / RU Locale
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
$L['info_desc'] = 'Функция создания виджетов для модуля Page';

/**
 * Plugin Config
 */

$L['cfg_useajax'] = 'Использование AJAX:';
$L['cfg_ajax'] = 'Использовать AJAX для паджинации';
$L['cfg_ajax_hint'] = 'Работает только при использовании аргумента $ajax_block и $cfg[\'turnajax\']';
$L['cfg_encrypt_ajax_urls'] = 'Шифровать URLы AJAX-паджинации';
$L['cfg_encrypt_ajax_urls_hint'] = 'Работает только при включенной AJAX-паджинации, рекомендуется для действующих сайтов в т. ч. при использовании аргумента $extra с AJAX';
$L['cfg_encrypt_key'] = 'Ключ шифрования';
$L['cfg_encrypt_iv'] = 'Вектор исполнения';

$L['cfg_gentags'] = 'Генерация тегов:';
$L['cfg_usertags'] = 'Создавать теги для модуля Users';
$L['cfg_comments'] = 'Создавать теги для плагина Comments';
$L['cfg_ratings'] = 'Создавать теги для плагина Star Ratings';

$L['cfg_misc'] = 'Разное';
$L['cfg_published_only'] = 'Выводить только опубликованные страницы';
$L['cfg_published_only_hint'] = 'Иначе в аргументе $extra указывать значение поля page_state';

/**
 * Plugin Tools
 */

/**
 * Plugin Body
 */
