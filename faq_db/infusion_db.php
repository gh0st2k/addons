<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion_db.php
| Author: INSERT NAME HERE
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (!defined("DB_FAQ_Q")) {
	define("DB_FAQ_Q", DB_PREFIX."faq_db_questions");
}
if (!defined("DB_FAQ_A")) {
	define("DB_FAQ_A", DB_PREFIX."faq_db_answers");
}
if (!defined("DB_FAQ_CAT")) {
	define("DB_FAQ_CAT", DB_PREFIX."faq_db_cat");
}
if (!defined("DB_FAQ_FUSION")) {
	define("DB_FAQ_FUSION", DB_PREFIX."faq_db_fusion");
}
?>
