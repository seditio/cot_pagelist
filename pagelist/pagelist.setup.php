<?php
/* ====================
[BEGIN_COT_EXT]
Code=pagelist
Name=[SEDBY] PageList
Category=navigation-structure
Description=Generates custom page lists available via {PHP|pagelist} callback
Version=4.11b
Date=2023-09-06
Author=Vladimir Sibirov / Dmitri Beliavski
Copyright=&copy; 2012-2025 Seditio.By
Notes=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=page
Requires_plugins=cotlib
Recommends_modules=
Recommends_plugins=
[END_COT_EXT]
[BEGIN_COT_EXT_CONFIG]

useajax=00:separator:::
ajax=01:radio::0:Use AJAX
encrypt_ajax_urls=02:radio::0:Encrypt ajax URLs
encrypt_key=03:string::1234567890123456:Secret Key
encrypt_iv=04:string::1234567890123456:Initialization Vector

gentags=20:separator:::
usertags=21:radio::0:Generate User tags
comments=22:radio::0:Generate Comments tags
ratings=23:radio::0:Generate Ratings tags
thanks=23:radio::0:Generate Thanks tags

misc=30:separator:::
published_only=31:radio::1:Select only published pages

[END_COT_EXT_CONFIG]
==================== */

/**
* Pagelist Plugin / Setup
*
* @package pagelist
* @author Vladimir Sibirov & Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');
