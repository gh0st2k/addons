<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: gh0st2k
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

include INFUSIONS."faq_db/infusion_db.php";

// Infusion general information
$inf_title = "FAQ Datenbank";
$inf_description = "Erweiterte FAQ Datenbank";
$inf_version = "1.0";
$inf_developer = "gh0st2k";
$inf_email = "";
$inf_weburl = "http://www.phpfusion-support.de";

$inf_folder = "faq_db"; // The folder in which the infusion resides.

// Delete any items not required below.

$inf_newtable[1] = DB_FAQ_Q." (
faq_q_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
faq_q_cat_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
faq_q_user_id MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
faq_q_title VARCHAR(50) DEFAULT '' NOT NULL,
faq_q_timestamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
faq_q_a SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
faq_q_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
faq_q_counter SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (faq_q_id),
KEY faq_q_cat_id (faq_q_cat_id),
KEY faq_q_user_id (faq_q_user_id),
KEY faq_q_a (faq_q_a),
KEY faq_q_status (faq_q_status)
) TYPE=MyISAM;";

$inf_newtable[2] = DB_FAQ_A." (
faq_a_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
faq_q_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
faq_a_user_id MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
faq_a_fusion TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
faq_a_text TEXT DEFAULT '' NOT NULL,
faq_a_timestamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
faq_a_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
faq_a_counter SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (faq_a_id),
KEY faq_a_user_id (faq_a_user_id),
KEY faq_q_id (faq_q_id)
) TYPE=MyISAM;";

$inf_newtable[3] = DB_FAQ_CAT." (
faq_cat_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
faq_cat_title VARCHAR(50) DEFAULT '' NOT NULL,
faq_cat_description VARCHAR(255) DEFAULT '' NOT NULL,
faq_cat_lft TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL,
faq_cat_rgt TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL,
faq_cat_updated TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
PRIMARY KEY (faq_cat_id),
KEY faq_cat_lft (faq_cat_lft),
KEY faq_cat_rgt (faq_cat_rgt)
) TYPE=MyISAM;";

$inf_newtable[4] = DB_FAQ_FUSION." (
faq_fusion_id TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
faq_fusion_1st TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL,
faq_fusion_2nd TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL,
faq_fusion_order TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
PRIMARY KEY (faq_fusion_id)
) TYPE=MyISAM;";

$inf_insertdbrow[1] = DB_FAQ_CAT." (faq_cat_title, faq_cat_description, faq_cat_lft, faq_cat_rgt) VALUES('DO NOT DELETE', '', '1', '2')";
// $inf_altertable[1] = DB_INFUSION_TABLE." ADD etc";
//$inf_deldbrow[1] = "other_table";

$inf_droptable[1] = DB_FAQ_Q;
$inf_droptable[2] = DB_FAQ_A;
$inf_droptable[3] = DB_FAQ_CAT;
$inf_droptable[4] = DB_FAQ_FUSION;

$inf_adminpanel[1] = array(
	"title" => "FAQ Datenbank",
	"image" => "image.gif",
	"panel" => "admin/faq_admin_index.php",
	"rights" => "FAQ"
);

$inf_sitelink[1] = array(
	"title" => "FAQ Datenbank",
	"url" => "faq_db.php",
	"visibility" => "0"
);
?>