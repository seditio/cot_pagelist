<?php
/* ====================
[BEGIN_COT_EXT]
Code=pagelist
Name=PageList Widget
Category=navigation-structure
Description=Generates custom page lists available via {PHP|pagelist} callback
Version=3.10
Date=2023-12-21
Author=Vladimir Sibirov / Dmitri Beliavski
Copyright=&copy; 2012-2023 Seditio.By
Notes=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=page
[END_COT_EXT]
[BEGIN_COT_EXT_CONFIG]
users=01:radio::0:Generate User tags
ratings=02:radio::0:Generate Ratings tags
[END_COT_EXT_CONFIG]
==================== */

/**
* PageList Plugin / Setup
*
* @package PageList
* @author Vladimir Sibirov / Dmitri Beliavski
* @copyright (c) 2012-2023 seditio.by
*/

defined('COT_CODE') or die('Wrong URL');
