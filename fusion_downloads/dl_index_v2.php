<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

if (!iADMIN && $dl_settings['maintenance'] == 1) { redirect(DL_DIR."dl_maintenance.php"); }


?>
