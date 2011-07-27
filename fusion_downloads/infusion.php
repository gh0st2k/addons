<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }
include INFUSIONS."fusion_downloads/infusion_db.php";

$inf_title = "Download DB";
$inf_description = "Download DB der Supportseite";
$inf_version = "1.0";
$inf_developer = "gh0st2k";
$inf_email = "";
$inf_weburl = "http://";
$inf_folder = "fusion_downloads"; // The folder in which the infusion resides.

$inf_newtable[1] = DL." (
    dl_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    dl_cat SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    dl_fusion TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    dl_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    dl_title VARCHAR(50) NOT NULL DEFAULT '',
    dl_description TEXT NOT NULL DEFAULT '',
    dl_copyright TEXT NOT NULL DEFAULT '',
    dl_licence VARCHAR(255) NOT NULL DEFAULT '',
    dl_author VARCHAR(50) NOT NULL DEFAULT '',
    dl_author_email VARCHAR(50) NOT NULL DEFAULT '',
    dl_author_www VARCHAR(50) NOT NULL DEFAULT '',
    dl_coauthor VARCHAR(50) NOT NULL DEFAULT '',
    dl_timestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
    dl_user_id MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
    dl_thread MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
    dl_folder VARCHAR(40) UNSIGNED NOT NULL DEFAULT '',
    dl_count SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (dl_id),
    KEY dl_cat (dl_cat),
    KEY dl_fusion (dl_fusion),
    KEY dl_status (dl_status)
) ENGINE=MyISAM";

$inf_newtable[2] = DL_REJECT." (
    dl_reject_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    dl_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    dl_reject_user_id MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
    dl_reject_short TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    dl_reject_long TEXT NOT NULL DEFAULT '',
    dl_reject_timestamp INT(10) NOT NULL DEFAULT '0',
    PRIMARY KEY  (dl_reject_id),
    KEY dl_id (dl_id),
    KEY dl_reject_user_id (dl_user_id)
) ENGINE=MyISAM";

$inf_newtable[3] = DL_CAT." (
   dl_cat_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
   dl_cat_name VARCHAR(50) NOT NULL DEFAULT '',
   dl_cat_order TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
   PRIMARY KEY  (dl_cat_id)
) ENGINE=MyISAM";

$inf_newtable[4] = DL_FUSION." (
    dl_fusion_id TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
    dl_fusion_name VARCHAR(50) NOT NULL DEFAULT '',
    dl_fusion_order TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY  (dl_fusion_id)
) ENGINE=MyISAM";

$inf_newtable[5] = DL_SCREEN." (
    dl_screen_id MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
    dl_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    dl_screen_file VARCHAR(50) NOT NULL DEFAULT '',
    dl_screen_thumb VARCHAR(50) NOT NULL DEFAULT '',
    dl_screen_order TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY  (dl_screen_id),
    KEY dl_id (dl_id)
) ENGINE=MyISAM";

$inf_newtable[6] = DL_ERROR." (
    dl_error_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    dl_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    dl_error_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    dl_error_user_id MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
    dl_error_message TEXT NOT NULL DEFAULT '',
    dl_error_status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    dl_error_mod MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
    dl_error_timestamp INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (dl_error_id),
    KEY dl_id (dl_id),
    KEY dl_error_user_id (dl_error_user_id),
    KEY dl_error_mod (dl_error_mod)
) ENGINE=MyISAM";

$inf_newtable[7] = DL_SETTINGS." (
    file_ext VARCHAR(255) NOT NULL DEFAULT '',
    file_size VARCHAR(20) NOT NULL DEFAULT '',
    screen_width SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    screen_height SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    screen_size VARCHAR(20) NOT NULL DEFAULT '',
    thumb_width SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    thumb_height SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    forum_id MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
    cat_sort TINYINT(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=MyISAM";

$inf_newtable[8] = DL_ACCESS." (
    dl_access_user MEDIUMINT(9) UNSIGNED NOT NULL DEFAULT '0',
    dl_access_rights VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY(dl_access_user)
) ENGINE=MyISAM";

$inf_insertdbrow[1] = DL_SETTINGS." (file_ext, file_size, screen_width, screen_height, screen_size, thumb_width, thumb_height, forum_id) VALUES('.zip|.rar|', '5242880', '1440', '990', '512000', '100', '100', '1')";

$inf_droptable[1] = DL;
$inf_droptable[2] = DL_VERSION;
$inf_droptable[3] = DL_CAT;
$inf_droptable[4] = DL_FUSION;
$inf_droptable[5] = DL_SCREEN;
$inf_droptable[6] = DL_ERROR;
$inf_droptable[7] = DL_SETTINGS;
$inf_droptable[8] = DL_ACCESS;


$inf_adminpanel[1] = array(
	"title" => "Download Index",
	"image" => "image.gif",
	"panel" => "admin/dl_index.php",
	"rights" => "DBI"
);

$inf_sitelink[1] = array(
	"title" => "Download DB",
	"url" => "dl_index.php",
	"visibility" => "0"
);

$inf_sitelink[2] = array(
	"title" => "Download einsenden",
	"url" => "dl_submit.php",
	"visibility" => "101"
);

$inf_sitelink[3] = array(
	"title" => "Meine Downloads",
	"url" => "dl_center.php",
	"visibility" => "101"
);
?>
