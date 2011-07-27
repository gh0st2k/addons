<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (!defined("DL")) {
	define("DL", DB_PREFIX."download_db");
}

if (!defined("DL_REJECT")) {
	define("DL_REJECT", DB_PREFIX."download_db_reject");
}

if (!defined("DL_CAT")) {
	define("DL_CAT", DB_PREFIX."download_db_cat");
}

if (!defined("DL_TYPE")) {
	define("DL_TYPE", DB_PREFIX."download_db_type");
}

if (!defined("DL_FUSION")) {
	define("DL_FUSION", DB_PREFIX."download_db_fusion");
}

if (!defined("DL_SCREEN")) {
	define("DL_SCREEN", DB_PREFIX."download_db_screen");
}

if (!defined("DL_ERROR")) {
	define("DL_ERROR", DB_PREFIX."download_db_error");
}

if (!defined("DL_MOD")) {
	define("DL_MOD", DB_PREFIX."download_db_mod");
}

if (!defined("DL_SETTINGS")) {
	define("DL_SETTINGS", DB_PREFIX."download_db_settings");
}
if (!defined("DL_ACCESS")) {
	define("DL_ACCESS", DB_PREFIX."download_db_access");
}
?>