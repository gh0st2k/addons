<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(FUSION_SELF); }

if (isset($_GET['edit'])) {
    echo "<div id='close-message'><div class='admin-message'>Die &Auml;nderungen wurden gespeichert.</div></div>\n";
}
if (isset($_GET['save'])) {
    echo "<div id='close-message'><div class='admin-message'>Die Version wurde gespeichert.</div></div>\n";
}
if (isset($_GET['del'])) {
    echo "<div id='close-message'><div class='admin-message'>Die Version wurde gel&ouml;scht.</div></div>\n";
}
if (isset($_GET['back'])) {
    echo "<div id='close-message'><div class='admin-message'>Bitte erst mindestens eine Version erstellen, danach geht es weiter.</div></div>\n";
}
if (isset($_GET['action']) && isset($_GET['dl_version_id']) && isnum($_GET['dl_version_id'])) {
    if ($_GET['action'] == "edit") {
        $data = dbarray(dbquery("SELECT dl_version, dl_changelog, dl_file FROM ".DL_VERSION." WHERE dl_version_id='".$_GET['dl_version_id']."' LIMIT 1"));
        $dl_version = $data['dl_version'];
        $dl_changelog = $data['dl_changelog'];
        $dl_file = $data['dl_file'];
        $edit = "&amp;dl_version_id=".$_GET['dl_version_id']."&amp;action=edit";
    } elseif ($_GET['action'] == "delete") {
        $data = dbarray(dbquery("SELECT dl_version, dl_changelog, dl_file FROM ".DL_VERSION." WHERE dl_version_id='".$_GET['dl_version_id']."' LIMIT 1"));
        @unlink(DL_FILES.$data['dl_file']);
        $result = dbquery("DELETE FROM ".DL_VERSION." WHERE dl_version_id='".$_GET['dl_version_id']."'");
        redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2&amp;del=ok");
    } elseif (isset($_GET['up']) && isnum($_GET['up'])) {
        $db_upper = dbarray(dbquery("SELECT dl_version_id FROM ".DL_VERSION." WHERE dl_order='".$_GET['up']."'"));
        $result = dbquery("UPDATE ".DL_VERSION." SET dl_order='".($_GET['up']+1)."' WHERE dl_version_id='".$db_upper['dl_version_id']."'");
        $result = dbquery("UPDATE ".DL_VERSION." SET dl_order='".$_GET['up']."' WHERE dl_version_id='".$_GET['dl_version_id']."'");
        dl_reorder(DL_VERSION, "dl_version_id", "dl_order");
        redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2");
    } elseif (isset($_GET['down']) && isnum($_GET['down'])) {
        $db_upper = dbarray(dbquery("SELECT dl_version_id FROM ".DL_VERSION." WHERE dl_order='".$_GET['down']."'"));
        $result = dbquery("UPDATE ".DL_VERSION." SET dl_order='".($_GET['down']-1)."' WHERE dl_version_id='".$db_upper['dl_version_id']."'");
        $result = dbquery("UPDATE ".DL_VERSION." SET dl_order='".$_GET['down']."' WHERE dl_version_id='".$_GET['dl_version_id']."'");
        dl_reorder(DL_VERSION, "dl_version_id", "dl_order");
        redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2");
    }
} else {
    $dl_file = ""; $dl_changelog = ""; $dl_version = ""; $edit = "";
}

if (isset($_POST['save'])) {
    $error = "";
    if (isset($_POST['dl_version']) && $_POST['dl_version'] != "") { $dl_version = stripinput(trim($_POST['dl_version'])); } else { $error = 1; }
    if (!preg_match('/^(\d{1,2}).(\d{1,2})$/', $dl_version)) {
        if (!preg_match('/^(\d{1}).(\d{1,2}).(\d{1,2})$/', $dl_version)) {
            $error = 2;
        }
    }
    if (isset($_POST['dl_changelog']) && $_POST['dl_changelog'] != "") { $dl_changelog = stripinput(trim($_POST['dl_changelog'])); } elseif (isset($_GET['edit']) && isnum($_GET['edit'])) { $error = 1; }
    else { $dl_changelog = ""; }
    $versions = dbcount("(dl_version_id)", DL_VERSION, "dl_id='".$_GET['dl_id']."'");
    require_once INCLUDES."infusions_include.php";
    if (!isset($_GET['dl_version_id'])) {
        $upload = upload_file("dl_file", "", DL_FILES, $dl_settings['file_ext'], $dl_settings['file_size']);
    } elseif (isset($_POST['delete_file']) && $_POST['delete_file'] == 1) {
        $upload['error'] = 0;
        @unlink(DL_FILES.$dl_file);
        $count = dbcount("(dl_id)", DL_VERSION, "WHERE dl_id='".$_GET['dl_id']);
        if ($count == 0) {
            $result = dbquery("UPDATE ".DL." SET dl_status='0' WHERE dl_id='".$_GET['dl_id']."'");
        } else {
            dl_reorder(DL_VERSION, "dl_version_id", "dl_order", "dl_timestamp ASC");
        }
    } else {
        $upload['error'] = 0;
    }
    if ($upload['error'] == 0 && $error == "") {
        if (isset($_GET['dl_version_id']) && isnum($_GET['dl_version_id'])) {
            $result = dbquery("UPDATE ".DL_VERSION." SET dl_version='".$dl_version."', dl_changelog='".$dl_changelog."', dl_version_status='0' WHERE dl_version_id='".$_GET['dl_version_id']."'");
            $count = dbcount("(dl_id)", DL_VERSION, "WHERE dl_id='".$_GET['dl_id']);
            if ($count == 1) {
                $result = dbquery("UPDATE ".DL." SET dl_status='0' WHERE dl_id='".$_GET['dl_id']."'");
            }
            redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2&amp;dl_version_id=".$_GET['dl_version_id']."&amp;edit=ok");
        } else {
            $result = dbquery("INSERT INTO ".DL_VERSION." SET dl_id='".$_GET['dl_id']."', dl_version='".$dl_version."', dl_file='".$upload['target_file']."', dl_order='1', dl_changelog='".$dl_changelog."', dl_version_timestamp='".time()."', dl_user_id='".$userdata['user_id']."'");
            $result = dbquery("UPDATE ".DL_VERSION." SET dl_status='0' WHERE dl_id='".$_GET['dl_id']."'");
            dl_reorder(DL_VERSION, "dl_version_id", "dl_order", "dl_timestamp ASC");
            redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2&amp;dl_version_id=".$_GET['dl_version_id']."&amp;save=ok");
        }
    } elseif ($upload['error'] == 0) {
        @unlink(DL_FILES.$upload['target_file']);
        if ($error == 1) {
            echo "<div id='close-message'><div class='admin-message'>Bitte f&uuml;lle alle Felder aus.</div></div>\n";
        } elseif ($error == 2) {
            echo "<div id='close-message'><div class='admin-message'>Bitte Versionsnummer anpassen (erlaubt: 1.00, 10.11 oder 1.10.11).</div></div>\n";
        }
    } else {
        switch($upload['error']) {
            case 1: echo "<div id='close-message'><div class='admin-message'>Die Dateigr&ouml;&szlig;e &uuml;berschreitet das Limit.</div></div>\n";
            case 2: echo "<div id='close-message'><div class='admin-message'>Dieser Dateityp ist nicht erlaubt.</div></div>\n";
            case 3: echo "";
            case 4: echo "<div id='close-message'><div class='admin-message'>Die Upload konnte nicht durchgef&uuml;hrt werden. Bitte &uuml;berpr&uuml;fe die Schreibrechte.</div></div>\n";
        }
    }
}

opentable("Vorhandene Datei");
$result = dbquery("SELECT dl_version, dl_file FROM ".DL." WHERE dl_id='".$_GET['dl_id']."' ORDER BY dl_order ASC");
if (dbrows($result) > 0) {
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
    echo "<tr>";
    echo "<td class='tbl2'>Version</td>\n";
    echo "<td class='tbl2'>Dateiname</td>\n";
    echo "<td class='tbl2'>Optionen</td>\n";
    echo "</tr>";
    $i = 0;
    while ($data = dbarray($result)) {
        $class = ($i % 2 ? "tbl2" : "tbl1");
        echo "<tr>\n";
        echo "<td class='".$class."'>".$data['dl_version']."</td>\n";
        echo "<td class='".$class."'>".$data['dl_file']."</td>\n";
        echo "<td class='".$class."'>";
        echo "<a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2&amp;dl_version_id=".$data['dl_version_id']."&amp;action=edit' title='Version editieren'><img src='".DL_DIR."images/bug_edit.png' alt='Editieren' /></a>";
        echo " <a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2&amp;dl_version_id=".$data['dl_version_id']."&amp;action=delete' title='Version l&ouml;schen'><img src='".DL_DIR."images/bug_delete.png' alt='L&ouml;schen' /></a>";
        echo " <a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2&amp;dl_version_id=".$data['dl_version_id']."&amp;up=".($data['dl_order']-1)."&amp;action' title='Version hochschieben'><img src='".DL_DIR."images/arrow_up.png' alt='Hoch' /></a>";
        echo " <a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2&amp;dl_version_id=".$data['dl_version_id']."&amp;down=".($data['dl_order']+1)."&amp;action' title='Version herabsetzen'><img src='".DL_DIR."images/arrow_down.png' alt='Runter' /></a>";
        echo "</td>\n";
        echo "</tr>\n";
        $i++;
    }
    echo "</table>\n";
} else {
    echo "<center>Bisher wurde keine Version erstellt.</center>";
}
closetable();

opentable((isset($_GET['version_id']) && isnum($_GET['version_id']) ? "Version bearbeiten":"Version erstellen"));
echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;step=2".$edit."' method='post' enctype='multipart/form-data'>\n";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>";
echo "<td class='tbl1' style='width:200px'>Versionsnummer:</td>\n";
echo "<td class='tbl1'><input type='text' name='dl_version' maxlength='8' class='textbox' style='width:400px' value='".$dl_version."' /></td>\n";
echo "</tr><tr>\n";
$count = dbcount("(dl_id)", DL_VERSION, "dl_id='".$_GET['dl_id']."'");
if ($count > 0) {
    echo "<td class='tbl1'>Changelog:</td>\n";
    echo "<td class='tbl1'><textarea name='dl_changelog' cols='80' rows='10' class='textbox' style='width:400px'>".$dl_changelog."</textarea></td>\n";
    echo "</tr><tr>\n";
}
echo "<td class='tbl1'>Datei:</td>\n";
echo "<td class='tbl1'>";
if (isset($_GET['version_id']) && isnum($_GET['version_id'])) {
    echo "";
} else { 
    echo "<input type='file' name='dl_file' class='textbox' style='width:200px' />";
}
echo "</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'></td>\n";
echo "<td class='tbl1'><input type='submit' name='save' value=' Speichern' class='button' /></td>\n";
echo "</tr></table>\n";
echo "</form>\n";
closetable();
?>
