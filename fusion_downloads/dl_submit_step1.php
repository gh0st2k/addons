<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
    $result = dbquery("SELECT dl_title, dl_version, dl_file, dl_changelog, dl_fusion, dl_cat, dl_description, dl_author, dl_author_email, dl_author_www, dl_coauthor, dl_copyright, dl_licence, dl_user_id FROM ".DL." WHERE dl_id='".$_GET['dl_id']."' LIMIT 1");
    if (dbrows($result) > 0) {
        $data = dbarray($result);
        if (!iADMIN && $data['dl_user_id'] != $userdata['user_id']) { redirect(FUSION_SELF); }
        $dl_title = $data['dl_title'];
        $dl_fusion = explode(".",$data['dl_fusion']);
        $dl_cat = $data['dl_cat'];
        $dl_description = $data['dl_description'];
        $dl_author = $data['dl_author'];
        $dl_author_email = $data['dl_author_email'];
        $dl_author_www = $data['dl_author_www'];
        $dl_coauthor = $data['dl_coauthor'];
        $dl_copyright = $data['dl_copyright'];
        $dl_version = $data['dl_version'];
        $dl_file = $data['dl_file'];
        $dl_changelog = $data['dl_changelog'];
        //$dl_licence = $data['dl_licence'];
        $get = "?dl_id=".$_GET['dl_id']."&step=1";
    } else {
        redirect(FUSION_SELF);
    }
} else {
    $get = "?step=1";
    $dl_title = ""; $dl_fusion = array(); $dl_cat = ""; $dl_description = ""; $dl_author = ""; $dl_author_email = ""; $dl_author_www = ""; $dl_coauthor = "";
    $dl_copyright = ""; $dl_file = ""; $dl_version = ""; $dl_changelog = ""; //$dl_licence = "";
    if (iMEMBER) {
        $dl_author = $userdata['user_name']; $dl_author_email = $userdata['user_email']; $dl_author_www = $userdata['user_web'];
    } else {
        $dl_author = ""; $dl_author_email = ""; $dl_author_www = "";
    }
}

if (isset($_POST['save'])) {
    $error = "";
    if (isset($_POST['dl_title']) && $_POST['dl_title'] != "") { $dl_title = stripinput(trim($_POST['dl_title'])); } else { $error = 1; }
    if (isset($_POST['dl_description']) && $_POST['dl_description'] != "") { $dl_description = $_POST['dl_description']; } else { $error = 1; }
    if (isset($_POST['dl_fusion'])) {
		$dl_fusion = stripinput(implode(".", $_POST['dl_fusion']));
	} else {
		$error = 1;
	}
    if (isset($_POST['dl_cat']) && isnum($_POST['dl_cat'])) { $dl_cat = $_POST['dl_cat']; } else { $error = 1; }
    if (isset($_POST['dl_author']) && $_POST['dl_author'] != "") { $dl_author = stripinput(trim($_POST['dl_author'])); } else { $error = 1; }
    if (isset($_POST['dl_copyright']) && $_POST['dl_copyright'] != "") { $dl_copyright = stripinput(trim($_POST['dl_copyright'])); } else { $error = 1; }
    //if (isset($_POST['dl_licence']) && $_POST['dl_licence'] != "") { $dl_licence = stripinput(trim($_POST['dl_licence'])); } else { $error = 1; }
    $dl_author_email = (isset($_POST['dl_author_email']) ? stripinput(trim($_POST['dl_author_email'])) : "");
    $dl_author_www = (isset($_POST['dl_author_www']) ? stripinput(trim($_POST['dl_author_www'])) : "");
    $dl_coauthor = (isset($_POST['dl_coauthor']) ? stripinput(trim($_POST['dl_coauthor'])) : "");
    $dl_changelog = (isset($_POST['dl_changelog']) ? stripinput(trim($_POST['dl_changelog'])) : "");
    if (isset($_POST['dl_version']) && $_POST['dl_version'] != "") { $dl_version = stripinput(trim($_POST['dl_version'])); } else { $error = 1; }
    if (!preg_match('/^(\d{1,2}).(\d{1,2})$/', $dl_version)) {
        if (!preg_match('/^(\d{1}).(\d{1,2}).(\d{1,2})$/', $dl_version)) {
            $error = 2;
        }
    }
    // Upload
    require_once INCLUDES."infusions_include.php";
    $upload = upload_file("dl_file", "", DL_FILES, $dl_settings['file_ext'], $dl_settings['file_size']);
    // Upload Ende
	if ($upload['error'] == 0 && strlen($upload['target_file']) > 40) { $error = 3; }
    if ($error == "" && (($upload['error'] == 0) || $dl_file != "")) {
        if ($upload['error'] == 0) { $dl_file = $upload['target_file']; }
        if (isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
            if (!iADMIN) { $status = ", dl_status='1'"; } else { $status = "d, dl_status='0'"; }
            $result = dbquery("UPDATE ".DL." SET dl_title='".$dl_title."', dl_version='".$dl_version."', dl_file='".$dl_file."', dl_description='".$dl_description."', dl_changelog='".$dl_changelog."', dl_fusion='".$dl_fusion."', dl_cat='".$dl_cat."', dl_author='".$dl_author."',
                                                 dl_author_email='".$dl_author_email."', dl_author_www='".$dl_author_www."', dl_coauthor='".$dl_coauthor."',
                                                 dl_copyright='".$dl_copyright."' ".$status."
                                                 WHERE dl_id='".$_GET['dl_id']."'");
            redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=1&update=ok");
        } else {
            $result = dbquery("INSERT INTO ".DL." SET dl_title='".$dl_title."', dl_version='".$dl_version."', dl_file='".$dl_file."', dl_description='".$dl_description."', dl_changelog='".$dl_changelog."', dl_fusion='".$dl_fusion."', dl_cat='".$dl_cat."', dl_author='".$dl_author."',
                                                      dl_author_email='".$dl_author_email."', dl_author_www='".$dl_author_www."', dl_coauthor='".$dl_coauthor."',
                                                      dl_copyright='".$dl_copyright."', dl_timestamp='".time()."', dl_user_id='".$userdata['user_id']."'");
            $id = mysql_insert_id();
            redirect(FUSION_SELF."?dl_id=".$id."&step=2");
        }
    } else {
        $message = "";
        if ($error != "") {
			if ($error == 3) {
				$message .= "Der Dateiname darf nicht mehr als 40 Zeichen haben.";
			} elseif ($error != 2) {
                $message .= "Es m&uuml;ssen alle Felder ohne * ausgef&uuml;t werden.<br />";
            } else {
                $message .= "Bitte Versionsnummer anpassen (erlaubt: 1.00, 10.11 oder 1.10.11).<br />";
            }
        }
        switch($upload['error']) {
            case 1: $message .= "Die Dateigr&ouml;&szlig;e &uuml;berschreitet das Limit.";
            case 2: $message .= "Dieser Dateityp ist nicht erlaubt.<br />";
            case 3: $message .= "";
            case 4: $message .= "Die Upload konnte nicht durchgef&uuml;hrt werden. Bitte &uuml;berpr&uuml;fe die Schreibrechte.<br />";
        }
        echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n";
    }
}

opentable("Allgemeine Informationen");
echo "<table width='625px' align='center'><tr><td>";
echo "<form action='".FUSION_SELF.$get."' method='post' enctype='multipart/form-data'>\n";
echo "<fieldset><legend>Allgemeine Informationen:</legend>";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl1' width='175px'>Titel:</td>\n";
echo "<td class='tbl1'><input type='text' name='dl_title' value='".$dl_title."' maxlength='40' class='textbox' style='width:425px;' /></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>PHP-Fusion Version:</td>\n";
echo "<td class='tbl1'>".dl_fusion_options_new($dl_fusion)."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>Kategorie:</td>\n";
echo "<td class='tbl1'>".dl_cat_options($dl_cat, "style='width:425px;'")."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1' valign='top'>Beschreibung:</td>\n";
echo "<td class='tbl1'><textarea name='dl_description' cols='80' rows='8' class='textbox' style='width:425px;'>".$dl_description."</textarea></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1' valign='top'>Changelog / Features*:</td>\n";
echo "<td class='tbl1'><textarea name='dl_changelog' cols='80' rows='8' class='textbox' style='width:425px;'>".$dl_changelog."</textarea></td>\n";
echo "</tr></table>\n";
echo "</fieldset>";
echo "<fieldset><legend>Autor:</legend>";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl1' width='175px'>Name:</td>\n";
echo "<td class='tbl1'><input type='text' name='dl_author' value='".$dl_author."' maxlength='50' class='textbox' style='width:425px;'' /></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>E-Mail*:</td>\n";
echo "<td class='tbl1'><input type='text' name='dl_author_email' value='".$dl_author_email."' maxlength='50' class='textbox' style='width:425px;' /></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>Homepage*:</td>\n";
echo "<td class='tbl1'><input type='text' name='dl_author_www' value='".$dl_author_www."' maxlength='50' class='textbox' style='width:425px;' /></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>Co-Autor*:</td>\n";
echo "<td class='tbl1'><input type='text' name='dl_coauthor' value='".$dl_coauthor."' maxlength='50' class='textbox' style='width:425px;' /></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'></td>\n";
echo "<td class='tbl1'>* Angabe freiwillig</td>\n";
echo "</tr></table>\n";
echo "</fieldset>";
echo "<fieldset><legend>Rechtliches:</legend>";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl1' width='175px' valign='top'>Copyright:</td>\n";
echo "<td class='tbl1'><textarea name='dl_copyright' cols='80' rows='5' class='textbox' style='width:425px;'>".$dl_copyright."</textarea></td>\n";
//echo "</tr><tr>\n";
//echo "<td class='tbl1' valign='top'>Lizenz:</td>\n";
//echo "<td class='tbl1'><textarea name='dl_licence' cols='80' rows='5' class='textbox' style='width:425px;'>".$dl_licence."</textarea></td>\n";
echo "</tr></table>";
echo "</fieldset>";
echo "<fieldset><legend>Datei / Version:</fieldset>";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl1' width='175px' valign='top'>Versionsnummer:</td>\n";
echo "<td class='tbl1'><input type='text' name='dl_version' maxlength='8' class='textbox' style='width:400px' value='".$dl_version."' /></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1' width='175px' valign='top'>Datei:</td>\n";
echo "<td class='tbl1'>";
echo "<input type='file' name='dl_file' class='textbox' style='width:200px' />";
echo "</td>\n";
if ($dl_file != "" && file_exists(DL_DIR."upload/files/".$dl_file)) {
    echo "</tr><tr>\n";
    echo "<td class='tbl1' width='175px' valign='top'>Vorhandene Datei:</td>\n";
    echo "<td class='tbl1'>".$data['dl_file']."</td>";
    echo "</tr><tr>\n";
    echo "<td class='tbl1' width='175px' valign='top'></td>\n";
    echo "<td class='tbl1'>Wird &uuml;berschrieben, wenn eine neue Datei hochgeladen wird.</td>";
}
echo "</tr>\n";
echo "</table>\n";
echo "</fieldset>\n";
echo "<br />";
if (isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
    echo "<span style='margin-left:15px;'><input type='submit' name='save' value='Speichern' class='button' />";
} else {
    echo "<span style='margin-left:15px;'><input type='submit' name='save' value='Speichern und weiter' class='button' />";
}
echo "</form>";
echo "</td></tr></table>";
closetable();
?>
