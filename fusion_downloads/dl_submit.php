<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

if (!iADMIN && $dl_settings['maintenance'] == 1) { redirect(DL_DIR."dl_maintenance.php"); }

if (!iMEMBER) {
    echo "<center>Du musst dich registrieren oder einloggen um einen Download einzusenden.</center>\n";
} elseif (isset($_GET['step']) && isnum($_GET['step'])) {
    opentable("Fortschritt:");
    echo "<table width='600px' class='tbl-border' cellpadding='0' cellspacing='0' align='center'>\n";
    echo "<tr>\n";
    echo "<td class='".($_GET['step'] == 1 ? "tbl2" : "tbl1")."' style='width:120px; text-align:center;'>Downloaddetails</td>\n";
    echo "<td class='".($_GET['step'] == 2 ? "tbl2" : "tbl1")."' style='width:120px; text-align:center;'>Screenshots</td>\n";
    echo "<td class='".($_GET['step'] == 3 ? "tbl2" : "tbl1")."' style='width:120px; text-align:center;'>Supportthread</td>\n";
    echo "<td class='".($_GET['step'] == 4 ? "tbl2" : "tbl1")."' style='width:120px; text-align:center;'>Fertig</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<br />\n";
    echo "<center>Im Punkt \"Fertig\" wird der Download den Status \"Zur Pr&uuml;fung\" erhalten und wir werden ihn bearbeiten.</center>\n";
    if (isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
        $access = dbcount("(dl_id)", DL, "dl_id='".$_GET['dl_id']."' AND dl_user_id='".$userdata['user_id']."'");
        //if ($access == "0" && !dl_access("B")) { redirect(DL_DIR."dl_index.php"); }
        echo "<table width='600px' cellpadding='0' cellspacing='0' align='center' style='text-align:center;'>\n";
        echo "<tr>";
        if ($_GET['step'] > 1) {
            echo "<td class='tbl1'>\n";
            echo "<form action='".DL_DIR."dl_submit.php?step=".($_GET['step']-1)."&dl_id=".$_GET['dl_id']."' method='post'>\n";
            echo "<input type='submit' value=' Zur&uuml;ck zum vorherigen Schritt' class='button' />\n";
            echo "</form>\n";
            echo "</td>\n";
        }
        $dl_thread = dbcount("(dl_id)", DL, "dl_id='".$_GET['dl_id']."' AND dl_thread!='0'");
        if ($_GET['step'] != 4 && ($_GET['step'] != 3 || $dl_thread > 0)) {
            //if ($_GET['step'] != 4 || ($_GET['step'] == 4 && $dl_thread == "1")) {
                echo "<td class='tbl1'>\n";
                echo "<form action='".DL_DIR."dl_submit.php?step=".($_GET['step']+1)."&dl_id=".$_GET['dl_id']."' method='post'>\n";
                echo "<input type='submit' value=' Weiter zum n&auml;chsten Schritt' class='button' />\n";
                echo "</form>\n";
                echo "</td>\n";
            //}
        }
        if ($_GET['step'] == 4) {
            echo "<td class='tbl1'>\n";
            echo "<form action='".DL_DIR."dl_center.php' method='post'>\n";
            echo "<input type='submit' value=' Zu meinen Downloads' class='button' />\n";
            echo "</form>\n";
            echo "</td>\n";
        }
        echo "</tr></table>\n";
    }
    closetable();
    switch ($_GET['step']) {
        case 1: include "dl_submit_step1.php"; break;
        case 2: include "dl_submit_step2.php"; break;
        case 3: include "dl_submit_step3.php"; break;
        case 4: include "dl_submit_step4.php"; break;
        case 5: redirect(DL_DIR."dl_center.php".$id."&upload=ok"); break;
    }
} else {
    redirect(FUSION_SELF."?step=1");
}
require_once THEMES."templates/footer.php";
?>
