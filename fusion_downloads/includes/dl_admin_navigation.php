<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }
function admin_navigation() {
    global $userdata, $rights_all;
    if (dl_access()) {
        echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='700px' align='center'>\n";
        echo "<tr>\n";
        $width = 700 / 7;
        echo "<td class='tbl1' width='".$width."' style='text-align:center;'><a href='".DL_DIR_ADMIN."dl_index.php'>&Uuml;bersicht</a></td>\n";
        echo "<td class='tbl1' width='".$width."' style='text-align:center;'><a href='".DL_DIR_ADMIN."dl_submit.php'>Einsendungen</a></td>";
        echo "<td class='tbl1' width='".$width."' style='text-align:center;'><a href='".DL_DIR_ADMIN."dl_all.php'>Downloads</a></td>";
        echo "<td class='tbl1' width='".$width."' style='text-align:center;'><a href='".DL_DIR_ADMIN."dl_error.php'>Fehlermeldungen</a></td>\n";
        echo "<td class='tbl1' width='".$width."' style='text-align:center;'><a href='".DL_DIR_ADMIN."dl_catversion.php'>Kategorien</a></td>\n";
        echo "<td class='tbl1' width='".$width."' style='text-align:center;'><a href='".DL_DIR_ADMIN."dl_access.php'>Zugriffsrechte</a></td>\n";
        echo "<td class='tbl1' width='".$width."' style='text-align:center;'><a href='".DL_DIR_ADMIN."dl_settings.php'>Einstellungen</a></td>\n";
        echo "</tr>\n";
        echo "</table>\n";
    } else {
        redirect(DL_DIR."index.php");
    }
}
?>
