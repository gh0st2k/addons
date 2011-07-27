<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
include INFUSIONS."faq_db/includes/faq_core.php";
require_once INCLUDES."bbcode_include.php";
require_once INFUSIONS."faq_db/includes/class.nestedsets.php";

$faq_q_title = ""; $faq_a_text = ""; $faq_q_cat = "0";
$faq_a_fusion = array();

opentable("FAQ DB - Eintrag einsenden");
if (isset($_POST['submit'])) {
    $error = "";
    if (isset($_POST['faq_q_cat']) && isnum($_POST['faq_q_cat'])) {
        $faq_q_cat = $_POST['faq_q_cat'];
    } else { $error .= "Bitte w&auml;hle eine Kategorie aus.<br />\n"; }
    if (isset($_POST['faq_a_fusion']) && is_array($_POST['faq_a_fusion'])) {
        $faq_fusion = implode(",",stripinput($_POST['faq_a_fusion']));
    } else { $error .= "Bitte w&auml;hle mindestens eine PHP-Fusion Version aus.<br />\n";}
    if (isset($_POST['faq_q_title']) && $_POST['faq_q_title'] != "") {
        $faq_q_title = stripinput($_POST['faq_q_title']);
    } else { $error .= "Bitte gebe einen Titel / eine Frage f&uuml;r deine Einsendung an."; }
    if (isset($_POST['faq_a_text']) && $_POST['faq_a_text'] != "") {
        $faq_a_text = stripinput($_POST['faq_a_text']);
    } else { $error .= "Bitte gebe eine Antwort f&uuml;r deine Einsendung an."; }
    if ($error == "") {
        $result = dbquery("INSERT INTO ".DB_FAQ_Q." SET 
                            faq_q_cat_id='".$faq_q_cat."',
                            faq_q_user_id='".$userdata['user_id']."',
                            faq_q_title='".$faq_q_title."',
                            faq_q_timestamp='".time()."'");
        $q_id = mysql_insert_id();
        $result = dbquery("INSERT INTO ".DB_FAQ_A." SET 
                            faq_q_id='".$q_id."', 
                            faq_a_user_id='".$userdata['user_id']."',
                            faq_a_fusion='".$faq_fusion."',
                            faq_a_text='".$faq_a_text."',
                            faq_a_timestamp='".time()."'");
        $a_id = mysql_insert_id();
        $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_a='".$a_id."' WHERE faq_q_id='".$q_id."'");
        redirect(FAQ_DIR."faq_center.php");
    } else {
        echo "Folgende Fehler sind aufgetreten:<br />".$error."<br /><br />";
    }
}

if (isset($_POST['preview'])) {
    $error = "";
    if (isset($_POST['faq_q_cat']) && isnum($_POST['faq_q_cat'])) {
        $faq_q_cat = $_POST['faq_q_cat'];
    } else { $error .= "Bitte w&auml;hle eine Kategorie aus.<br />\n"; }
    if (isset($_POST['faq_a_fusion']) && is_array($_POST['faq_a_fusion'])) {
		$faq_a_fusion = array();
		foreach ($_POST['faq_a_fusion'] AS $id) {
			$faq_a_fusion[] = stripinput($id);
		}
    } else { $error .= "Bitte w&auml;hle mindestens eine PHP-Fusion Version aus.<br />\n";}
    if (isset($_POST['faq_q_title']) && $_POST['faq_q_title'] != "") {
        $faq_q_title = stripinput($_POST['faq_q_title']);
    } else { $error .= "Bitte gebe einen Titel / eine Frage f&uuml;r deine Einsendung an."; }
    if (isset($_POST['faq_a_text']) && $_POST['faq_a_text'] != "") {
        $faq_a_text = stripinput($_POST['faq_a_text']);
    } else { $error .= "Bitte gebe eine Antwort f&uuml;r deine Einsendung an."; }
	if ($error == "") {
		echo "<table cellpadding='0' cellspacing='0' width='95%' align='center' class='tbl-border'>\n";
		echo "<tr>\n";
		echo "<td class='tbl1'>";
		echo parseubb(nl2br($faq_a_text));
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<br /><br />";
	} else {
        echo "Folgende Fehler sind aufgetreten:<br />".$error."<br /><br />";
    }
}

$obj = new NestedSets(DB_FAQ_CAT, "faq_cat_", "faq_cat_title", "faq_cat_id");
$tree = $obj->GetNestedSets();
$cat_options = $obj->GetNestedSetsOptions($tree, "title", $faq_q_cat, "0", "1");

echo "<form action='".FUSION_SELF."' method='post' enctype='multipart/form-data' name='inputform'>\n";
echo "<table width='700px' cellpadding='0' cellspacing='0' align='center' class='tbl-border'>\n";
echo "<tr>\n";
echo "<td class='tbl' valign='top'>Kategorie:</td>\n";
echo "<td class='tbl'><select name='faq_q_cat' size='1' class='textbox' style='width:500px'>\n";
echo $cat_options;
echo "</select></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl' valign='top'>Frage / Titel:</td>\n";
echo "<td class='tbl'><input type='text' name='faq_q_title' maxlength='200' value='".$faq_q_title."' style='width:500px;' class='textbox' />\n";
echo "</tr><tr>\n";
echo "<td class='tbl' valign='top'>PHP Fusion Versionen:</td>\n";
echo "<td class='tbl'>";
$result = dbquery("SELECT faq_fusion_id, faq_fusion_1st, faq_fusion_2nd FROM ".DB_FAQ_FUSION." ORDER BY faq_fusion_order ASC");
if (dbrows($result)>0) {
    $i = 0;
    while ($data = dbarray($result)) {
        $i++;
        $checked = (in_array($data['faq_fusion_id'], $faq_a_fusion) ? "checked='checked'" : "");
        echo "<input type='checkbox' name='faq_a_fusion[]' value='".$data['faq_fusion_id']."' ".$checked." /> ".$data['faq_fusion_1st'].".".$data['faq_fusion_2nd']." ";
        if (($i%5) == 0) { echo "<br />\n"; }
    }
}
echo "</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl' valign='top'>Antwort:</td>\n";
echo "<td class='tbl'><textarea name='faq_a_text' class='textbox' cols='60' rows='30' style='width:500px' class='textbox'>".$faq_a_text."</textarea></td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl'></td>\n";
echo "<td class='tbl'>".display_bbcodes("400px", "faq_a_text", "inputform", "smiley|b|i|u|center|small|url|mail|img|color|geshi|code")."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl'></td>\n";
echo "<td class='tbl'><input type='submit' name='preview' value=' Vorschau' class='button' /> <input type='submit' name='submit' value=' Absenden' class='button' /></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";
closetable();
require_once THEMES."templates/footer.php";
?>
