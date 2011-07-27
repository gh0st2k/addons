<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";

if (dl_access("B")) {
    opentable("Meldungen der User");
    admin_navigation();
    if (isset($_GET['mod']) && isnum($_GET['mod'])) {
        $result = dbquery("UPDATE ".DL_ERROR." SET dl_error_mod='".$userdata['user_id']."' WHERE dl_error_id='".$_GET['mod']."'");
        echo "<div id='close-message'><div class='admin-message'>Die Meldung wurde dir zugewiesen.</div></div>\n";
    } elseif (isset($_GET['mod_reverse']) && isnum($_GET['mod_reverse'])) {
        $result = dbquery("UPDATE ".DL_ERROR." SET dl_error_mod='0' WHERE dl_error_id='".$_GET['mod_reverse']."'");
        echo "<div id='close-message'><div class='admin-message'>Die Zuweisung wurde r&uuml;ckg&auml;ngig gemacht.</div></div>\n";
    } elseif (isset($_GET['del']) && isnum($_GET['del'])) {
        $result = dbquery("DELETE FROM ".DL_ERROR." WHERE dl_error_id='".$_GET['del']."'");
        echo "<div id='close-message'><div class='admin-message'>Die Meldung wurde gel&ouml;scht.</div></div>\n";
    } 
    if (isset($_GET['error_id']) && isnum($_GET['error_id'])) {
        if (isset($_GET['done'])) {
            // evtl. vorherige Überprüfung, dass ein Mod die Meldung übernommen hat
            // Sperrung der Optionen, wenn Fehler erledigt
            $result = dbquery("UPDATE ".DL_ERROR." SET dl_error_status='1' WHERE dl_error_id='".$_GET['error_id']."'");
        }
        $result = dbquery(" SELECT aa.dl_id, aa.dl_error_type, aa.dl_error_user_id, aa.dl_error_message, aa.dl_error_status, aa.dl_error_mod, aa.dl_error_timestamp,
                            ab.user_name AS users_name, ab.user_status AS users_status, ac.user_name AS mod_name, ac.user_status AS mod_status,
                            ad.dl_title
                            FROM ".DL_ERROR." aa
                            LEFT JOIN ".DB_USERS." ab ON aa.dl_error_user_id=ab.user_id
                            LEFT JOIN ".DB_USERS." ac ON aa.dl_error_mod=ac.user_id
                            LEFT JOIN ".DL." ad ON ad.dl_id=aa.dl_id
                            WHERE aa.dl_error_id='".$_GET['error_id']."'
                            ");
        if (dbrows($result) > 0) {
            $data = dbarray($result);
            
            echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='700px' align='center'>\n";
            echo "<tr>\n";
            echo "<td class='tbl1'>Art des Fehlers:</td>\n";
            echo "<td class='tbl1'>".dl_error_type($data['dl_error_type'])."</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>Titel des Downloads:</td>\n";
            echo "<td class='tbl1'><a href='".DL_DIR."dl_details.php?dl_id=".$data['dl_id']."' title='".$data['dl_title']."' target='_blank'>".$data['dl_title']."</a></td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>Status:</td>\n";
            echo "<td class='tbl1'>";
            if ($data['dl_error_status'] == "0") { echo "Offen"; } else { echo "Erledigt"; }
            echo "</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>User:</td>\n";
            echo "<td class='tbl1'>".profile_link($data['dl_error_user_id'], $data['users_name'], $data['users_status'])."</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>&Uuml;bernehmender Mod:</td>\n";
            echo "<td class='tbl1'>".profile_link($data['dl_error_mod'], $data['mod_name'], $data['mod_status'])."</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>Beschreibung:</td>\n";
            echo "<td class='tbl1'>".nl2br($data['dl_error_message'])."</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>Optionen:</td>\n";
            echo "<td class='tbl1'>";
            echo "<a href='".DL_DIR."admin/dl_error.php'>Zur&uuml;ck</a> \n";
            if ($data['dl_error_mod'] == "0") { echo " <a href='".FUSION_SELF."?mod=".$_GET['error_id']."&amp;error_id=".$_GET['error_id']."' title='Fehlerbehebung &uuml;bernehmen'>&Uuml;bernehmen</a> \n"; }
            else { echo " <a href='".FUSION_SELF."?mod_reverse=".$_GET['error_id']."&amp;error_id=".$_GET['error_id']."' title='Fehlerbehebung zur&uuml;ckziehen'>&Uuml;bernahme zur&uuml;cknehmen</a> \n"; }
            echo "<a href='".DL_DIR."admin/dl_error.php?error_id=".$_GET['error_id']."&amp;done' title=''>Fehler behoben</a> \n";
            echo "<a href='".DL_DIR."admin/dl_error.php?del=".$_GET['error_id']."' title='L&ouml;schen'>Fehler l&ouml;schen</a> \n";
            echo "</td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            closetable();
        } else {
            redirect(FUSION_SELF);
        }
    } else {
        $result = dbquery(" SELECT aa.dl_error_id, aa.dl_id, aa.dl_error_type, aa.dl_error_user_id, aa.dl_error_message, aa.dl_error_status, aa.dl_error_mod, aa.dl_error_timestamp,
                            ab.user_name AS user_name, ac.user_name AS mod_name
                            FROM ".DL_ERROR." aa
                            LEFT JOIN ".DB_USERS." ab ON aa.dl_error_user_id=ab.user_id
                            LEFT JOIN ".DB_USERS." ac ON aa.dl_error_mod=ac.user_id
                            ORDER BY dl_error_status DESC, dl_error_timestamp DESC");
        if (dbrows($result) > 0) {
            echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='700px' align='center'>\n";
            echo "<tr>\n";
            echo "<td class='tbl2'>ID</td>\n";
            echo "<td class='tbl2'>Fehlertyp</td>\n";
            echo "<td class='tbl2'>User</td>\n";
            echo "<td class='tbl2'>Mod</td>\n";
            echo "<td class='tbl2'>Zeitpunkt</td>\n";
            echo "<td class='tbl2'>Optionen</td>\n";
            echo "</tr>\n";
            while ($data = dbarray($result)) {
                echo "<tr>\n";
                echo "<td class='tbl1'>".$data['dl_error_id']."</td>\n";
                echo "<td class='tbl1'>".dl_error_type($data['dl_error_type'])."</td>\n";
                echo "<td class='tbl1'>".$data['user_name']."</td>\n";
                echo "<td class='tbl1'>".$data['mod_name']."</td>\n";
                echo "<td class='tbl1'>".date("d.m.Y", $data['dl_error_timestamp'])."</td>\n";
                echo "<td class='tbl1'>";
                echo "<a href='".FUSION_SELF."?error_id=".$data['dl_error_id']."'>Details</a>";
                if ($data['dl_error_mod'] == "0") { echo " <a href='".FUSION_SELF."?mod=".$data['dl_error_id']."'>&Uuml;bernehmen</a>"; }
                else { echo " <a href='".FUSION_SELF."?mod_reverse=".$data['dl_error_id']."'>&Uuml;bernahme zur&uuml;cknehmen</a>"; }
                echo " <a href='".FUSION_SELF."?del=".$data['dl_error_id']."'>L&ouml;schen</a>";
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        } else {
            echo "<center>Es sind zur Zeit keinen unbearbeiteten Fehler gemeldet.</center>\n";
        }
        closetable();
    }
} else {
    redirect(DL_DIR_ADMIN."dl_index.php?no_access");
}

require_once THEMES."templates/footer.php";
?>
