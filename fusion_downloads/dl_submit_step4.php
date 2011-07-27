<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(FUSION_SELF); }

opentable("Download fertiggestellt");
if (dl_access("S") && dl_access("F") && dl_access("R")) {
    $result = dbquery("UPDATE ".DL." SET dl_status='2', dl_approved='".$userdata['user_id']."' WHERE dl_id='".$_GET['dl_id']."'");
    echo "Vielen Dank f&uuml;r das Einsenden des Downloads. Der Download ist freigeschaltet.";
} else {
    $result = dbquery("UPDATE ".DL." SET dl_status='1' WHERE dl_id='".$_GET['dl_id']."'");
    echo "Vielen Dank f&uuml;r das Einsenden des Downloads. Sofern keine neue Version eingestellt wird, kann der Download jetzt von unserem Team gepr&uuml;ft werden.<br />";
    echo "<br \>Nach erfolgreicher Pr&uuml;fung wird der Download freigeschaltet. Bei Fragen wende dich direkt an das Team.";
}

closetable();
?>
