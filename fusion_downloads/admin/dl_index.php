<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";
if (dl_access() == FALSE) { redirect(DL_DIR."dl_index.php"); }

opentable("Download DB - &Uuml;bersicht");

admin_navigation();
if (isset($_GET['no_access'])) {
    echo "<div id='close-message'><div class='admin-message'>Du hast hier leider keinen Zugriff.</div></div>\n";
}
closetable();

require_once THEMES."templates/footer.php";
?>
