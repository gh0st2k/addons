<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";

if (!dl_access("A")) { redirect(DL_DIR_ADMIN."dl_index.php?no_access"); }

if (isset($_GET['error']) && isnum($_GET['error'])) {
    switch ($_GET['error']) {
        case 0: echo "<div id='close-message'><div class='admin-message'>Die &Auml;nderungen wurden erfolgreich gespeichert.</div></div>\n"; break;
        case 1: echo "<div id='close-message'><div class='admin-message'>Bitte gib mindestens eine Dateiendung an und trenne diese durch ein Komma.</div></div>\n"; break;
        case 2: echo "<div id='close-message'><div class='admin-message'>Bitte lege eine maximale Dateigr&ouml;&szlig;e fest.</div></div>\n"; break;
    }
}

if (isset($_POST['dl_submit'])) {
    $error = array();
    if (isset($_POST['file_ext']) && $_POST['file_ext'] != "" && strlen($_POST['file_ext'] <= 255)) { $file_ext = trim(stripinput($_POST['file_ext'])); } else { $error[] = 1; }
    if (isset($_POST['file_size']) && isnum($_POST['file_size'])) { $file_size = $_POST['file_size']; } else { $error[] = 2; }
    if (isset($_POST['screen_width']) && isnum($_POST['screen_width'])) { $screen_width = $_POST['screen_width']; } else { $error[] = 3; }
    if (isset($_POST['screen_height']) && isnum($_POST['screen_height'])) { $screen_height = $_POST['screen_height']; } else { $error[] = 4; }
    if (isset($_POST['screen_size']) && isnum($_POST['screen_size'])) { $screen_size = $_POST['screen_size']; } else { $error[] = 5; }
    if (isset($_POST['thumb_width']) && isnum($_POST['thumb_width'])) { $thumb_width = $_POST['thumb_width']; } else { $error[] = 6; }
    if (isset($_POST['thumb_height']) && isnum($_POST['thumb_height'])) { $thumb_height = $_POST['thumb_height']; } else { $error[] = 7; }
    if (isset($_POST['forum_id']) && isnum($_POST['forum_id'])) { $forum_id = $_POST['forum_id']; } else { $error[] = 8; }
    if (isset($_POST['cat_sort']) && isnum($_POST['cat_sort'])) { $cat_sort = $_POST['cat_sort']; } else { $cat_sort = "1"; }
    if (isset($_POST['maintenance']) && isnum($_POST['maintenance'])) { $maintenance = $_POST['maintenance']; } else { $maintenance = 0; }
    if (empty($error)) {
        $result = dbquery("UPDATE ".DL_SETTINGS." SET file_ext='".$file_ext."', file_size='".$file_size."', screen_width='".$screen_width."', screen_height='".$screen_height."',
                                                      screen_size='".$screen_size."', thumb_width='".$thumb_width."', thumb_height='".$thumb_height."', forum_id='".$forum_id."', cat_sort='".$cat_sort."',
                                                      maintenance='".$maintenance."'");
        redirect(FUSION_SELF."?ok");
    } else {
        $message = "";
        foreach ($error as $id) {
            switch ($id) {
                case 1: $message .= "<div id='close-message'><div class='admin-message'>Bitte gib mindestens eine Dateiendung an und trenne diese durch ein Komma.</div></div>\n<br />"; break;
                case 2: $message .= "<div id='close-message'><div class='admin-message'>Bitte lege eine maximale Dateigr&ouml;&szlig;e fest.</div></div>\n<br />"; break;
                case 3: $message .= "<div id='close-message'><div class='admin-message'>Bitte lege eine maximale Screenshotbreite fest.</div></div>\n<br />"; break;
                case 4: $message .= "<div id='close-message'><div class='admin-message'>Bitte lege eine maximale Screenshoth&ouml;he fest.</div></div>\n<br />"; break;
                case 5: $message .= "<div id='close-message'><div class='admin-message'>Bitte lege eine maximale Screenshotdateigr&ouml;&szlig;e fest.</div></div>\n<br />"; break;
                case 6: $message .= "<div id='close-message'><div class='admin-message'>Bitte lege eine maximale Thumbbreite fest.</div></div>\n<br />"; break;
                case 7: $message .= "<div id='close-message'><div class='admin-message'>Bitte lege eine maximale Thumbh&ouml;he fest.</div></div>\n<br />"; break;
                case 8: $message .= "<div id='close-message'><div class='admin-message'>Bitte lege ein Forum f&uuml;r Supportthreads fest.</div></div>\n<br />"; break;
            }
        }
        echo $message;
    }
}

if ($dl_settings['cat_sort'] == 0) { $sel0 = "selected='selected'"; $sel1 = ""; } else { $sel0 = ""; $sel1 = "selected='selected'"; }
if ($dl_settings['maintenance'] == 0) { $sel_m0 = "selected='selected'"; $sel_m1 = ""; } else { $sel_m0 = ""; $sel_m1 = "selected='selected'"; }

opentable("Download DB - Einstellungen");
admin_navigation();
if (isset($_GET['ok'])) {
    echo "<div id='close-message'><div class='admin-message'>Die &Auml;nderungen wurden erfolgreich gespeichert.</div></div>\n";
}
echo "<form action='".FUSION_SELF."' method='post'>\n";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl1' width='250px'>Erlaubte Dateiendungen (.zip,.rar usw.):</td>\n";
echo "<td class='tbl1'><input type='text' name='file_ext' class='textbox' style='width:300px' maxlength='255' value='".$dl_settings['file_ext']."' /></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Maximale Dateigr&ouml;&szlig;e:</td>\n";
echo "<td class='tbl1'><input type='text' name='file_size' class='textbox' style='width:300px' maxlength='20' value='".$dl_settings['file_size']."' /></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Max. Screenshotbreite:</td>\n";
echo "<td class='tbl1'><input type='text' name='screen_width' class='textbox' style='width:300px' maxlength='4' value='".$dl_settings['screen_width']."' /></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Max. Screenshoth&ouml;he:</td>\n";
echo "<td class='tbl1'><input type='text' name='screen_height' class='textbox' style='width:300px' maxlength='4' value='".$dl_settings['screen_height']."' /></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Max. Screenshotdateigr&ouml;&szlig;e:</td>\n";
echo "<td class='tbl1'><input type='text' name='screen_size' class='textbox' style='width:300px' maxlength='20' value='".$dl_settings['screen_size']."' /></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Max. Thumbbreite:</td>\n";
echo "<td class='tbl1'><input type='text' name='thumb_width' class='textbox' style='width:300px' maxlength='3' value='".$dl_settings['thumb_width']."' /></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Max. Thumbh&ouml;he:</td>\n";
echo "<td class='tbl1'><input type='text' name='thumb_height' class='textbox' style='width:300px' maxlength='3' value='".$dl_settings['thumb_height']."' /></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Forum f&uuml;r Supportthreads:</td>\n";
echo "<td class='tbl1'><input type='text' name='forum_id' class='textbox' style='width:300px' maxlength='5' value='".$dl_settings['forum_id']."' /></td>\n";
echo "<tr>\n";
echo "<td class='tbl1'>Kategorien sortieren:</td>\n";
echo "<td class='tbl1'><select name='cat_sort' size='1' class='textbox'><option value='0' ".$sel0.">wie angegeben</option><option value='1' ".$sel1.">Alphabetisch</option></select></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'>Wartungsmodus:</td>\n";
echo "<td class='tbl1'><select name='maintenance' size='1' class='textbox'><option value='0' ".$sel_m0.">Aus</option><option value='1' ".$sel_m1.">An</option></select></td>\n";
echo "</tr>";
echo "<tr>\n";
echo "<td class='tbl1'></td>\n";
echo "<td class='tbl1'><input type='submit' name='dl_submit' class='button' value='Daten speichern' /></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";
closetable();
require_once THEMES."templates/footer.php";
?>
