<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";
require_once INCLUDES."infusions_include.php";

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(DL_DIR."dl_index.php"); }

if (isset($_POST['dl_livedemo']) && $_POST['dl_livedemo'] != "") {
    $result = dbquery("UPDATE ".DL." SET dl_folder='".trim(stripinput($_POST['dl_livedemo']))."' WHERE dl_id='".$_GET['dl_id']."'");
    redirect(DL_DIR_ADMIN."dl_all.php");
}

opentable("Livedemo hinzuf&uuml;gen");
echo "<div align='center'>\n";
echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."' method='post'>\n";
echo "Ordnername: <input type='text' name='dl_livedemo' class='textbox' style='width:300px;' />";
echo "<br /> <input type='submit' value=' Speichern' class='button' />";
echo "</form>\n";
echo "</div>\n";
closetable();

require_once THEMES."templates/footer.php";
?>
