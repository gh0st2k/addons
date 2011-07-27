<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."faq_db/includes/faq_core.php";

if (!checkrights("FAQ") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect(BASEDIR."../index.php"); }

if (isset($_GET['delete'])) {
    if (isset($_GET['q_id']) && isnum($_GET['q_id'])) {
        $result = dbquery("DELETE FROM ".DB_FAQ_A." WHERE faq_q_id='".$_GET['q_id']."'");
        $result = dbquery("DELETE FROM ".DB_FAQ_Q." WHERE faq_q_id='".$_GET['q_id']."'");
    } elseif (isset($_GET['a_id']) && isnum($_GET['a_id'])) {
        $result = dbquery("DELETE FROM ".DB_FAQ_A." WHERE faq_a_id='".$_GET['a_id']."'");
    }
}

opentable("Fusion FAQ: Navigation");
require_once FAQ_DIR."admin/faq_admin_navigation.php";
closetable();

opentable("Fusion FAQ Administration: Neue Fragen und Antworten");
$result = dbquery("SELECT aa.faq_q_id, aa.faq_q_user_id, aa.faq_q_title, aa.faq_q_timestamp, ab.faq_a_id, ac.faq_cat_title, ad.user_name, ad.user_status
                          FROM ".DB_FAQ_Q." aa, ".DB_FAQ_A." ab, ".DB_FAQ_CAT." ac, ".DB_USERS." ad
                          WHERE aa.faq_q_status='0'
                          AND aa.faq_q_id=ab.faq_q_id
                          AND aa.faq_q_cat_id=ac.faq_cat_id
                          AND aa.faq_q_user_id=ad.user_id");
if (dbrows($result) > 0) {
    echo "<table cellpadding='0' cellspacing='0' width='700px' align='center' class='tbl-border'>\n";
    echo "<tr>\n";
    echo "<td class='tbl'>Id</td>\n";
    echo "<td class='tbl'>Kategorie</td>\n";
    echo "<td class='tbl'>User</td>\n";
    echo "<td class='tbl'>Datum</td>\n";
    echo "<td class='tbl'>Titel</td>\n";
    echo "<td class='tbl'>Optionen</td>\n";
    echo "</tr>\n";
    while ($data = dbarray($result)) {
        echo "<tr>\n";
        echo "<td class='tbl'>".$data['faq_q_id']."</td>\n";
        echo "<td class='tbl'>".$data['faq_cat_title']."</td>\n";
        echo "<td class='tbl'>".profile_link($data['faq_q_user_id'], $data['user_name'], $data['user_status'])."</td>\n";
        echo "<td class='tbl'>".date('d.m.Y',$data['faq_q_timestamp'])."</td>\n";
        echo "<td class='tbl'>".trimlink($data['faq_q_title'], 25)."</td>\n";
        echo "<td class='tbl'>";
        echo "<a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."&amp;a_id=".$data['faq_a_id']."'>Details</a>\n";
        echo " <a href='".FAQ_DIR."admin/faq_admin_edit.php".$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;details&amp;a_id=".$data['faq_a_id']."'>Bearbeiten</a>\n";
        echo " <a href='".FUSION_SELF.$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;delete'>L&ouml;schen</a>\n";
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<center>Es sind keine unbearbeiteten eingesendeten Fragen vorhanden.</center>\n";
}
closetable();

opentable("FAQ DB Administration: &Uuml;berarbeitete Antworten");
$result = dbquery("SELECT aa.faq_q_id, aa.faq_q_user_id, aa.faq_q_title, aa.faq_q_timestamp, ab.faq_a_id, ac.faq_cat_title, ad.user_name, ad.user_status
                          FROM ".DB_FAQ_Q." aa, ".DB_FAQ_A." ab, ".DB_FAQ_CAT." ac, ".DB_USERS." ad
                          WHERE aa.faq_q_status='1'
                          AND aa.faq_q_id=ab.faq_q_id
                          AND ab.faq_a_status='0'
                          AND aa.faq_q_cat_id=ac.faq_cat_id
                          AND ab.faq_a_user_id=ad.user_id");
if (dbrows($result) > 0) {
    echo "<table cellpadding='0' cellspacing='0' width='700px' align='center' class='tbl-border'>\n";
    echo "<tr>\n";
    echo "<td class='tbl'>Id</td>\n";
    echo "<td class='tbl'>Kategorie</td>\n";
    echo "<td class='tbl'>User</td>\n";
    echo "<td class='tbl'>Datum</td>\n";
    echo "<td class='tbl'>Titel</td>\n";
    echo "<td class='tbl'>Optionen</td>\n";
    echo "</tr>\n";
    while ($data = dbarray($result)) {
        echo "<tr>\n";
        echo "<td class='tbl'>".$data['faq_q_id']."</td>\n";
        echo "<td class='tbl'>".$data['faq_cat_title']."</td>\n";
        echo "<td class='tbl'>".profile_link($data['faq_q_user_id'], $data['user_name'], $data['user_status'])."</td>\n";
        echo "<td class='tbl'>".date('d.m.Y',$data['faq_q_timestamp'])."</td>\n";
        echo "<td class='tbl'>".trimlink($data['faq_q_title'], 25)."</td>\n";
        echo "<td class='tbl'>";
        echo "<a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."&amp;a_id=".$data['faq_a_id']."'>Details</a>\n";
        echo "<a href='".FAQ_DIR."admin/faq_admin_edit.php".$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;a_id=".$data['faq_a_id']."&amp;details'>Bearbeiten</a>\n";
        echo "<a href='".FUSION_SELF.$aidlink."&amp;a_id=".$data['faq_a_id']."&amp;delete'>L&ouml;schen</a>\n";
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<center>Es sind keine unbearbeiteten eingesendeten Antworten vorhanden.</center>\n";
}
closetable();
require_once THEMES."templates/footer.php";
?>
