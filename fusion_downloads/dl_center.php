<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

if (!iADMIN && $dl_settings['maintenance'] == 1) { redirect(DL_DIR."dl_maintenance.php"); }

if (!iMEMBER) { redirect(BASEDIR."index.php"); }

opentable("Meine Einsendungen");
$result = dbquery("SELECT aa.dl_id, aa.dl_title, aa.dl_version, aa.dl_status, aa.dl_count
                   FROM ".DL." aa
                   WHERE aa.dl_user_id='".$userdata['user_id']."'");
if (dbrows($result) > 0) {
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
    echo "<tr>\n";
    echo "<td class='tbl2'>Titel</td>\n";
    echo "<td class='tbl2' style='text-align:center;'>Version</td>\n";
    echo "<td class='tbl2' style='text-align:center;'>Status</td>\n";
    echo "<td class='tbl2' style='text-align:center;'>Downloads</td>\n";
    echo "<td class='tbl2'>Optionen</td>\n";
    echo "</tr>";
    $i = 0;
    while ($data = dbarray($result)) {
        $class = ($i % 2 ? "tbl2" : "tbl1");
        echo "<tr>\n";
        echo "<td class='".$class."'><a href='".DL_DIR."dl_details.php?dl_id=".$data['dl_id']."'>".$data['dl_title']."</a></td>\n";
        echo "<td class='".$class."' style='text-align:center;'>".$data['dl_version']."</td>\n";
        if ($data['dl_status'] == "3" || $data['dl_status'] == "4") {
            $data2 = dbarray(dbquery("SELECT dl_reject_user_id, dl_reject FROM ".DL_REJECT." WHERE dl_id='".$data['dl_id']."'"));
            $tooltip = phpentities(nl2br($data2['dl_reject']));
        } else {
            $tooltip = "";
        }
        echo "<td class='".$class."' style='text-align:center;'>".dl_status_link($data['dl_status'], $tooltip, FUSION_SELF)."</td>\n";
        echo "<td class='".$class."' style='text-align:center;'>".$data['dl_count']."</td>\n";
        echo "<td class='".$class."'>";
        echo "<a href='".DL_DIR."dl_submit.php?step=1&dl_id=".$data['dl_id']."'>Bearbeiten</a>";
        echo"</td>\n";
        $i++;
        echo "</tr>\n";
    }
    echo "</table>";
} else {
    echo "<center><div style='margin:20px;'>Bisher sind uns keine Einsendungen von dir bekannt, &auml;ndere das direkt hier: <a href='".DL_DIR."dl_requirements.php'>Download einsenden</a>!</div></center>";
}
closetable();
require_once THEMES."templates/footer.php";
?>