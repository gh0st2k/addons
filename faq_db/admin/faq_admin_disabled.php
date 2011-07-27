<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
include INFUSIONS."faq_db/includes/faq_core.php";

if (!checkrights("FAQ") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect(BASEDIR."../index.php"); }

if (isset($_GET['q_id']) && isnum($_GET['q_id'])) {
    if (isset($_GET['action'])) {
        if ($_GET['action'] == "enable") {
            $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_status='1' WHERE faq_q_id='".$_GET['q_id']."'");
        } elseif($_GET['action'] == "edit") {
            redirect(FAQ_DIR."admin/faq_admin_edit.php".$aidlink."&amp;q_id=".$_GET['q_id']);
        } elseif ($_GET['action'] == "delete") {
            $result = dbquery("DELETE FROM ".DB_FAQ_Q." WHERE faq_q_id='".$_GET['q_id']."'");
            $result = dbquery("DELETE FROM ".DB_FAQ_A." WHERE faq_q_id='".$_GET['q_id']."'");
        }
    }
}

opentable("FAQ DB Administration: Navigation");
require_once FAQ_DIR."admin/faq_admin_navigation.php";
closetable();

opentable("FAQ DB Administration: Deaktivierte Fragen");
$result = dbquery("SELECT faq_q_id, faq_q_title FROM ".DB_FAQ_Q." WHERE faq_q_status='2'");
if (dbrows($result)) {
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
    echo "<tr>\n";
    echo "<td class='tbl' width='70%'>Frage:</td>\n<td class='tbl' width='30%'>Optionen</td>\n";
    echo "</tr>\n";
    while ($data = dbarray($result)) {
        echo "<tr>\n";
        echo "<td class='tbl'>".$data['faq_q_title']."</td>\n";
        echo "<td class='tbl'>";
        echo " <a href='".FUSION_SELF.$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;action=enable'>Freischalten</a>\n";
        echo " <a href='".FUSION_SELF.$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;action=edit'>Bearbeiten</a>\n";
        echo " <a href='".FUSION_SELF.$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;action=delete'>L&ouml;schen</a>\n";
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<center>Es sind keine deaktivierten Fragen vorhanden.</center>\n";
}
closetable();


require_once THEMES."templates/footer.php";
?>
