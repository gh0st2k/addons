<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
include INFUSIONS."faq_db/includes/faq_core.php";
require_once INFUSIONS."faq_db/includes/class.nestedsets.php";
require_once INCLUDES."bbcode_include.php";

if (!checkrights("FAQ") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect(BASEDIR."../index.php"); }

$faq_q_cat_id = "0";  
if (!isset($_GET['q_id']) || !isnum($_GET['q_id'])) { redirect(FAQ_DIR."admin/faq_admin_submit.php".$aidlink); }
if (!isset($_GET['a_id']) || !isnum($_GET['a_id'])) {
    $result = dbquery("SELECT faq_q_a FROM ".DB_FAQ_Q." WHERE faq_q_id='".$_GET['q_id']."'");
    $data = dbarray($result);
    $_GET['a_id'] = $data['faq_q_a'];
}

if (isset($_GET['enable'])) {
    $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_a='".$_GET['a_id']."', faq_q_status='1' WHERE faq_q_id='".$_GET['q_id']."'");
    $result = dbquery("UPDATE ".DB_FAQ_A." SET faq_a_status='1' WHERE faq_a_id='".$_GET['a_id']."'");
    redirect(FAQ_DIR."faq_details.php?q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']."&amp;enable");
} elseif (isset($_GET['disable_all'])) {
    $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_status='2' WHERE faq_q_id='".$_GET['q_id']."'");
    $result = dbquery("UPDATE ".DB_FAQ_A." SET faq_a_status='2' WHERE faq_q_id='".$_GET['q_id']."'");
    redirect(FAQ_DIR."admin/faq_admin_disabled.php".$aidlink."&amp;disabled_all");
} elseif (isset($_GET['disable'])) {
    $result = dbquery("UPDATE ".DB_FAQ_A." SET faq_a_status='2' WHERE faq_a_id='".$_GET['a_id']."'");
    $check = dbcount("(faq_a_id)", DB_FAQ_A, "faq_q_id='".$_GET['q_id']."' AND faq_a_status='1'");
    if ($check >= "1") {
        $data = dbarray(dbquery("SELECT faq_a_id FROM ".DB_FAQ_A." WHERE faq_q_id='".$_GET['q_id']."' AND faq_a_status='1' ORDER BY faq_a_id DESC LIMIT 1"));
        $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_a='".$data['faq_a_id']."' WHERE faq_q_id='".$_GET['q_id']."'");
        redirect(FAQ_DIR."faq_details.php?q_id=".$_GET['q_id']."&amp;disable");
    } else {
        $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_status='2' WHERE faq_q_id='".$_GET['q_id']."'");
        redirect(FAQ_DIR."admin/faq_admin_disabled.php".$aidlink."&amp;disable_all");
    }
}


$result = dbquery("SELECT aa.faq_q_cat_id, aa.faq_q_title, ab.faq_a_fusion, ab.faq_a_text FROM ".DB_FAQ_Q." aa, ".DB_FAQ_A." ab
                   WHERE aa.faq_q_id='".$_GET['q_id']."' AND ab.faq_a_id='".$_GET['a_id']."'");
$edit = dbarray($result);

if (isset($_POST['submit'])) {
    $error = "";
    if (isset($_POST['faq_q_cat']) && isnum($_POST['faq_q_cat'])) {
        $faq_q_cat = $_POST['faq_q_cat'];
    } else { $error .= "Bitte w&auml;hle eine Kategorie aus.<br />\n"; }
    if (isset($_POST['faq_a_fusion']) && is_array($_POST['faq_a_fusion'])) {
        $faq_fusion = implode(",",stripinput($_POST['faq_a_fusion']));
    } else { $eror .= "Bitte w&auml;hle mindestens eine PHP-Fusion Version aus.<br />\n";}
    if (isset($_POST['faq_q_title']) && $_POST['faq_q_title'] != "") {
        $faq_q_title = stripinput($_POST['faq_q_title']);
    } else { $error .= "Bitte gebe einen Titel / eine Frage f&uuml;r deine Einsendung an."; }
    if (isset($_POST['faq_a_text']) && $_POST['faq_a_text'] != "") {
        $faq_a_text = stripinput($_POST['faq_a_text']);
    } else { $error .= "Bitte gebe eine Antwort f&uuml;r deine Einsendung an."; }
    if ($error == "") {
        $result = dbquery("UPDATE ".DB_FAQ_Q." SET
                            faq_q_cat_id='".$faq_q_cat."',
                            faq_q_title='".$faq_q_title."'
                            WHERE faq_q_id='".$_GET['q_id']."'");
        $result = dbquery("UPDATE ".DB_FAQ_A." SET
                            faq_a_fusion='".$faq_fusion."',
                            faq_a_text='".$faq_a_text."'
                            WHERE faq_a_id='".$_GET['a_id']."'");
        redirect(FAQ_DIR."faq_details.php?q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']);
    } else {
        echo "Folgende Fehler sind aufgetreten:<br />".$error;
    }
}

$obj = new NestedSets(DB_FAQ_CAT, "faq_cat_", "faq_cat_title", "faq_cat_id");
$tree = $obj->GetNestedSets();
$cat_options = $obj->GetNestedSetsOptions($tree, "title", $edit['faq_q_cat_id'], "0", "1");

opentable("FAQ DB Administration: Navigation");
require_once FAQ_DIR."admin/faq_admin_navigation.php";
closetable();

opentable("FAQ DB Administration: Eintrag bearbeiten");
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='500px' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']."&amp;enable'>Aktivieren</a></td>\n";
echo "<td class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']."&amp;disable'>Deaktivieren</a></td>\n";
echo "<td class='tbl2'><a href='".FUSION_SELF.$aidlink."&amp;q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']."&amp;disable_all'>Komplett deaktivieren</a></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br />\n";
echo "<form action='".FUSION_SELF.$aidlink."&amp;q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']."' method='post' enctype='multipart/form-data' name='inputform'>\n";
echo "<table width='700px' cellpadding='0' cellspacing='0' align='center' class='tbl-border'>\n";
echo "<tr>\n";
echo "<td class='tbl' valign='top'>Kategorie:</td>\n";
echo "<td class='tbl'><select name='faq_q_cat' size='1' class='textbox' style='width:500px'>\n";
echo $cat_options;
echo "</select></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl' valign='top'>Frage / Titel:</td>\n";
echo "<td class='tbl'><input type='text' name='faq_q_title' maxlength='50' value='".$edit['faq_q_title']."' style='width:500px;' class='textbox' />\n";
echo "</tr><tr>\n";
echo "<td class='tbl' valign='top'>PHP Fusion Versionen:</td>\n";
echo "<td class='tbl'>";
$result = dbquery("SELECT faq_fusion_id, faq_fusion_1st, faq_fusion_2nd FROM ".DB_FAQ_FUSION." ORDER BY faq_fusion_order ASC");
if (dbrows($result)>0) {
    $i = 0;
    while ($data = dbarray($result)) {
        $i++;
        $checked = (in_array($data['faq_fusion_id'], explode(",", $edit['faq_a_fusion'])) ? "checked='checked'" : "");
        echo "<input type='checkbox' name='faq_a_fusion[]' value='".$data['faq_fusion_id']."' ".$checked." /> ".$data['faq_fusion_1st'].".".$data['faq_fusion_2nd']." ";
        if (($i%5) == 0) { echo "<br />\n"; }
    }
}
echo "</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl' valign='top'>Antwort:</td>\n";
echo "<td class='tbl'><textarea name='faq_a_text' class='textbox' cols='60' rows='30' style='width:500px' class='textbox'>".$edit['faq_a_text']."</textarea></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl'></td>\n";
echo "<td class='tbl'>".display_bbcodes("400px", "faq_a_text", "inputform", "smiley|b|i|u|center|small|url|mail|img|color|geshi|code")."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl'></td>\n";
echo "<td class='tbl'><input type='submit' name='submit' value=' Absenden' class='button' /></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";
closetable();
require_once THEMES."templates/footer.php";
?>
