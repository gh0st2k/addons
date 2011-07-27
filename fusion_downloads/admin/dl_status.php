<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";
require_once INCLUDES."infusions_include.php";

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(DL_DIR."dl_index.php"); }
if (!isset($_GET['dl_status']) || !isnum($_GET['dl_status'])) { $_GET['dl_status'] = 2; }
$data = dbarray(dbquery("SELECT dl_title, dl_user_id FROM ".DL." WHERE dl_id='".$_GET['dl_id']."'"));

opentable("Status&auml;nderung des Downloads: ".$data['dl_title']);

if (isset($_POST['enter_status'])) {
    switch ($_GET['dl_status']) {
        case 0: 
            $result = dbquery("UPDATE ".DL." SET dl_status='0' WHERE dl_id='".$_GET['dl_id']."'");
            break;
        case 1: 
            $result = dbquery("UPDATE ".DL." SET dl_status='1' WHERE dl_id='".$_GET['dl_id']."'");
            break;
        case 2: 
            $result = dbquery("UPDATE ".DL." SET dl_status='2' WHERE dl_id='".$_GET['dl_id']."'");   
            $result = dbquery("DELETE FROM ".DL_REJECT." WHERE dl_id='".$_GET['dl_id']."'");
            $subject = "Dein Download ".$data['dl_title']." ist jetzt online.";
            $message = "";
            $sendpm = send_pm($data['dl_user_id'], $userdata['user_id'], $subject, $message);
			$count = dbcount("(dl_id)", DL, "dl_id='".$_GET['dl_id']."' AND dl_thread='0'");
			if ($count > 0) {
				CreateSupportThread($_GET['dl_id']);
			}
            break;
        case 3:
            if (!isset($_POST['reason_short']) || !isnum($_POST['reason_short'])) {
                redirect(FUSION_SELF."?".FUSION_QUERY."&amp;reason_short=no");
            }
            if ($_POST['reason_short'] == 5 && (!isset($_POST['reason_long']) || $_POST['reason_long'] == "")) {
                redirect(FUSION_SELF."?".FUSION_QUERY."&amp;reason_long=no");
            }
            switch ($_POST['reason_short']) {
                case 0: $reason = "AGPL nicht im Download enthalten"; break;
                case 1: $reason = "PHP-Fusion AGPL Hinweis nicht in mind. einer Datei vorhanden"; break;
                case 2: $reason = "Keine deutsche Readme enthalten"; break;
                case 3: $reason = "Ordnerstruktur entspricht nicht den Vorgaben"; break;
                case 4: $reason = "Sicherheitsbedenken"; break;
                case 5: $reason = "Sonstiges"; break;
            }
            $result = dbquery("UPDATE ".DL." SET dl_status='3' WHERE dl_id='".$_GET['dl_id']."'");
            $result = dbquery("INSERT INTO ".DL_REJECT." SET dl_id='".$_GET['dl_id']."', dl_reject_user_id='".$userdata['user_id']."',
                                                             dl_reject='".$reason.trim(stripinput($_POST['reason_long']))."', dl_reject_timestamp='".time()."'");
            $subject = "Dein Download wurde leider abgelehnt.";
            $reason = $reason." \n";
            $reason2 = ($_POST['reason_long'] != "" ? "Folgender Erg&auml;nzung wurde angegeben: 
                ".trim(stripinput($_POST['reason_long'])) : "");
$message = 
"Folgender Grund f&uuml;hrte zur Ablehnung deines Downloads: ".$data['dl_title'].":
Grund: ".$reason."
".$reason2."
                
Du kannst den Download jeder Zeit bearbeiten und erneut zur Pr&uuml;fung freigeben.";
            $sendpm = send_pm($data['dl_user_id'], $userdata['user_id'], $subject, $message);
            break;
        case 4: 
            if (!isset($_POST['reason_long']) || $_POST['reason_long'] == "") {
                redirect(FUSION_SELF."?".FUSION_QUERY."&amp;reason_long=no");
            }
            $result = dbquery("UPDATE ".DL." SET dl_status='4' WHERE dl_id='".$_GET['dl_id']."'");
            $result = dbquery("INSERT INTO ".DL_REJECT." SET dl_id='".$_GET['dl_id']."', dl_reject_user_id='".$userdata['user_id']."',
                                                             dl_reject='".trim(stripinput($_POST['reason_long']))."', dl_reject_timestamp='".time()."'");
            $subject = "Dein Download wurde leider abgelehnt deaktiviert.";
            $reason2 = trim(stripinput($_POST['reason_long']));
            $message = 
"Folgender Grund f&uuml;hrte zur Deaktivierung deines Downloads  ".$data['dl_title'].":
".$reason2."
 
Bei Fragen wende dich bitte an mich.";
            $sendpm = send_pm($data['dl_user_id'], $userdata['user_id'], $subject, $message);
            break;
    }
    redirect(DL_DIR_ADMIN."dl_all.php");
}

if (isset($_GET['reason_short'])) {
    echo "<div id='close-message'><div class='admin-message'>Bitte einen Grund ausw&auml;hlen.</div></div>\n";
}

if (isset($_GET['reason_long'])) {
    echo "<div id='close-message'><div class='admin-message'>Bitte einen Text angeben.</div></div>\n";
}


echo "<div align='center'>\n";
echo "<form action='".FUSION_SELF."' method='get'>\n";
echo "<input type='hidden' name='dl_id' value='".$_GET['dl_id']."' />\n";
echo "<select name='dl_status' size='1' class='textbox' onchange='submit()'>\n";
echo dl_status_options((isset($_GET['dl_status']) && isnum($_GET['dl_status']) ? $_GET['dl_status'] : "0"));
echo "</select>\n";
echo "</form>\n";
echo "<br />";
echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;dl_status=".$_GET['dl_status']."' method='post'>\n";
if (isset($_GET['dl_status'])) {
    switch ($_GET['dl_status']) {
        case 3:
            echo "<table cellpadding='0' cellspacing='0' width='600px' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td class='tbl' width='200px' valign='top'>Grund:</td>\n";
            echo "<td class='tbl' width='400px'>";
            echo "<input type='radio' name='reason_short' value='0'> AGPL nicht im Download enthalten <br />";
            echo "<input type='radio' name='reason_short' value='1'> PHP-Fusion AGPL Hinweis nicht in mind. einer Datei vorhanden <br />";
            echo "<input type='radio' name='reason_short' value='2'> Keine deutsche Readme enthalten <br />";
            echo "<input type='radio' name='reason_short' value='3'> Ordnerstruktur entspricht nicht den Vorgaben <br />";
            echo "<input type='radio' name='reason_short' value='4'> Sicherheitsbedenken <br />";
            echo "<input type='radio' name='reason_short' value='5'> Sonstiges";
            echo "</td>\n";
            echo "</tr>\n<tr>\n";
            echo "<td class='tbl' valign='top'>Weitere Infos:</td>\n";
            echo "<td class='tbl'><textarea name='reason_long' cols='90' rows='5' style='width:400px;' class='textbox'></textarea></td>\n";    
            echo "</tr>\n";
            echo "</table>\n";
            break;
        case 4:
            echo "<table cellpadding='0' cellspacing='0' width='600px' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td class='tbl' width='200px' valign='top'>Grund:</td>\n";
            echo "<td class='tbl'><textarea name='reason_long' cols='90' rows='5' style='width:400px;' class='textbox'></textarea></td>\n";    
            echo "</tr>\n";
            echo "</table>\n";
            break;
    }
}
echo "<input type='submit' name='enter_status' value=' Status absenden' class='button' />";
echo "</div>\n";
echo "<br />\n";
echo "<br />\n";
echo "</form>\n";
closetable();

require_once THEMES."templates/footer.php";
?>
