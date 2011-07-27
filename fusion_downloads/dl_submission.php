<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

if (!iADMIN && $dl_settings['maintenance'] == 1) { redirect(DL_DIR."dl_maintenance.php"); }

$id = (isset($_GET['dl_id']) && isnum($_GET['dl_id']) ? $_GET['dl_id'] : 0);

$pages = array(1, 2, 3, 4);
if (isset($_GET['page']) && isnum($_GET['page']) && $_GET['page'] > 1 && $_GET['page'] < 5 && $id > 0) {
	$page = $_GET['page'];
} else {
	$page = 1;
}

switch ($page) {
	case 1:
		require_once "dl_submit_infos.php";
		break;
	case 2:
		require_once "dl_submit_author.php";
		break;
	case 3:
		require_once "dl_submit_files.php";
		break;
	case 4:
		require_once "dl_submit_screenshots.php";
		break;
	case 5:
		require_once "dl_submit_thread.php";
		break;
	case 6:
		require_once "dl_submit_finish.php";
		break;
}

require_once THEMES."templates/footer.php";
?>
 
