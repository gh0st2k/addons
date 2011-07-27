<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";

if (!dl_access("A")) { redirect(DL_DIR_ADMIN."dl_index.php?no_access"); }

if (isset($_GET['del']) && isnum($_GET['del'])) {
    $result = dbquery("DELETE FROM ".DL_ACCESS." WHERE dl_access_user='".$_GET['del']."'");
    echo "<div id='close-message'><div class='admin-message'>Die Rechte wurden gel&ouml;scht.</div></div>\n";
}

if (isset($_POST['save']) && isset($_POST['dl_access_user']) && isnum($_POST['dl_access_user'])) {
    $dl_access_user = $_POST['dl_access_user'];
    $rights_array = array();
    foreach ($_POST['dl_access_rights'] as $element) {
        $rights_array[] = trim(stripinput($element));
    }
    $rights = implode(".", $rights_array);
    if (isset($_GET['edit']) && isnum($_GET['edit'])) {
        $result = dbquery("UPDATE ".DL_ACCESS." SET dl_access_rights='".$rights."' WHERE dl_access_user='".$_GET['edit']."'");
        echo "<div id='close-message'><div class='admin-message'>Die Rechte wurden bearbeitet.</div></div>\n";
    } else {
        $result = dbquery("INSERT INTO ".DL_ACCESS." SET dl_access_user='".$dl_access_user."', dl_access_rights='".$rights."'");
        echo "<div id='close-message'><div class='admin-message'>Der User hat die Zugriffsrechte erhalten.</div></div>\n";
    }
}
$dl_access_user = "";
if (isset($_GET['edit']) && isnum($_GET['edit'])) {
    $data = dbarray(dbquery("SELECT dl_access_rights FROM ".DL_ACCESS." WHERE dl_access_user='".$_GET['edit']."' LIMIT 1"));
    $rights = explode(".", $data['dl_access_rights']);
    $dl_access_user = $_GET['edit'];
    $checkA = (in_array("A", $rights) ? "checked='checked'" : "");
    $checkS = (in_array("S", $rights) ? "checked='checked'" : "");
    $checkF = (in_array("F", $rights) ? "checked='checked'" : "");
    $checkR = (in_array("R", $rights) ? "checked='checked'" : "");
    $checkL = (in_array("L", $rights) ? "checked='checked'" : "");
    $checkB = (in_array("B", $rights) ? "checked='checked'" : "");
} else {
    $checkA = ""; $checkS = ""; $checkF = ""; $checkR = ""; $checkL = ""; $checkB = ""; $dl_access_user;
}

opentable("Bestehende Rechte");
admin_navigation();
$result = dbquery("SELECT dl_access_user, dl_access_rights, ab.user_name FROM ".DL_ACCESS." aa LEFT JOIN ".DB_USERS." ab ON aa.dl_access_user=ab.user_id");
if (dbrows($result)) {
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='500px' align='center' style='border-collapse:collapse'>\n";
    echo "<tr>\n";
    echo "<td class='tbl2' width='300px'>User</td>\n";
    echo "<td class='tbl2' width='100px'>Rechte</td>\n";
    echo "<td class='tbl2' width='100px'>Optionen</td>\n";
    echo "</tr>\n";
    $i = 0;
    while ($data = dbarray($result)) {
        $class = ($i % 2 ? "tbl2" : "tbl1");
        echo "<tr>\n";
        echo "<td width='50%' class='".$class."'>".profile_link($data['dl_access_user'], $data['user_name'], "1")."</td>\n";
        echo "<td width='20%' class='".$class."'>".str_replace(".", " ",$data['dl_access_rights'])."</td>\n";
        echo "<td width='30%' class='".$class."'><a href='".FUSION_SELF."?edit=".$data['dl_access_user']."'>Editieren</a> <a href='".FUSION_SELF."?del=".$data['dl_access_user']."'>L&ouml;schen</a></td>\n";
        echo "</tr>\n";
        $i++;
    }
    echo "</table>\n";
} else {
    echo "<center>Bisher wurden keine Rechte vergeben.</center><br \>";
}
closetable();

function all_users ($id) {
    $result = dbquery("SELECT user_id, user_name FROM ".DB_USERS." ORDER BY user_level DESC, user_name ASC");
    $dummy = "";
    while ($data = dbarray($result)) {
        $sel = ($id == $data['user_id'] ? "selected='selected'" : "");
        $dummy .= "<option value='".$data['user_id']."' ".$sel.">".$data['user_name']."</option>\n";
    }
    return $dummy;
}

opentable((isset($_GET['edit']) && isnum($_GET['edit']) ? "Zugriff bearbeiten" : "Neuen Zugriff festlegen"));
echo "<form action='".FUSION_SELF.(isset($_GET['edit']) && isnum($_GET['edit']) ? "?edit=".$_GET['edit'] : "")."' method='post'>";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center' style='border-collapse:collapse'>\n";
echo "<tr>\n";
echo "<td class='tbl1' width='40%'>User ausw&auml;hlen</td><td class='tbl1' width='10%'></td>";
echo "<td class='tbl1' width='40%'><select name='dl_access_user' size='1' class='textbox'>".all_users($dl_access_user)."</select></td><td class='tbl1' width='10%'></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'><b>A</b>dministration</td><td class='tbl1'><input type='checkbox' name='dl_access_rights[]' value='A' ".$checkA."></td>\n";
echo "<td class='tbl1'>Freischaltung <b>S</b>icherheit</td><td class='tbl1'><input type='checkbox' name='dl_access_rights[]' value='S' ".$checkS."></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>Freischaltung <b>F</b>unktion</td><td class='tbl1'><input type='checkbox' name='dl_access_rights[]' value='F' ".$checkF."></td>\n";
echo "<td class='tbl1'>Freischaltung <b>R</b>ichtlinien</td><td class='tbl1'><input type='checkbox' name='dl_access_rights[]' value='R' ".$checkR."></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'><b>L</b>&ouml;schen</td><td class='tbl1'><input type='checkbox' name='dl_access_rights[]' value='L' ".$checkL."></td>\n";
echo "<td class='tbl1'><b>B</b>earbeiten</td><td class='tbl1'><input type='checkbox' name='dl_access_rights[]' value='B' ".$checkB."></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'></td><td class='tbl1'></td><td class='tbl1'></td>";
echo "<td class='tbl1'><input type='submit' value=' Speichern' class='button' name='save' /></td>\n";
echo "</tr></table>\n";
echo "</form>";
closetable();

require_once THEMES."templates/footer.php";
?>
