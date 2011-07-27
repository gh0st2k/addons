<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."faq_db/includes/faq_core.php";

if (!checkrights("FAQ") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect(BASEDIR."../index.php"); }
if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

$faq_items = "25";

$rows = dbcount("(faq_q_id)", DB_FAQ_Q);

if (isset($_GET['delete'])) {
    if (isset($_GET['q_id']) && isnum($_GET['q_id'])) {
        $result = dbquery("DELETE FROM ".DB_FAQ_A." WHERE faq_q_id='".$_GET['q_id']."'");
        $result = dbquery("DELETE FROM ".DB_FAQ_Q." WHERE faq_q_id='".$_GET['q_id']."'");
    }
}

opentable("Fusion FAQ: Navigation");
include "faq_admin_navigation.php";
closetable();

opentable("Fusion FAQ Administration:");
$result = dbquery("SELECT aa.faq_q_id, aa.faq_q_user_id, aa.faq_q_title, aa.faq_q_timestamp, aa.faq_q_status, ab.faq_a_id, ac.faq_cat_title, ad.user_name, ad.user_status
                          FROM ".DB_FAQ_Q." aa, ".DB_FAQ_A." ab, ".DB_FAQ_CAT." ac, ".DB_USERS." ad
                          WHERE aa.faq_q_id=ab.faq_q_id
                          AND aa.faq_q_cat_id=ac.faq_cat_id
                          AND aa.faq_q_user_id=ad.user_id LIMIT ".$_GET['rowstart'].",".$faq_items."");
if (dbrows($result) > 0) {
    echo "<table cellpadding='0' cellspacing='0' width='700px' align='center' class='tbl-border'>\n";
    echo "<tr>\n";
    echo "<td class='tbl'>Id</td>\n";
    echo "<td class='tbl'>Kategorie</td>\n";
    echo "<td class='tbl'>User</td>\n";
    echo "<td class='tbl'>Datum</td>\n";
    echo "<td class='tbl'>Titel</td>\n";
	echo "<td class='tbl'>Status</td>\n";
    echo "<td class='tbl'>Optionen</td>\n";
    echo "</tr>\n";
    while ($data = dbarray($result)) {
        echo "<tr>\n";
        echo "<td class='tbl'>".$data['faq_q_id']."</td>\n";
        echo "<td class='tbl'>".$data['faq_cat_title']."</td>\n";
        echo "<td class='tbl'>".profile_link($data['faq_q_user_id'], $data['user_name'], $data['user_status'])."</td>\n";
        echo "<td class='tbl'>".date('d.m.Y',$data['faq_q_timestamp'])."</td>\n";
        echo "<td class='tbl'>".trimlink($data['faq_q_title'], 25)."</td>\n";
		echo "<td class='tbl'>".$data['faq_q_status']."</td>\n";
        echo "<td class='tbl'>";
        echo "<a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."&amp;a_id=".$data['faq_a_id']."'>Details</a>\n";
        echo " <a href='".FAQ_DIR."admin/faq_admin_edit.php".$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;details&amp;a_id=".$data['faq_a_id']."'>Bearbeiten</a>\n";
        echo " <a href='".FUSION_SELF.$aidlink."&amp;q_id=".$data['faq_q_id']."&amp;delete'>L&ouml;schen</a>\n";
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<center>Es sind keine Fragen vorhanden.</center>\n";
}
closetable();


if ($rows > $faq_items) echo "<div align='center' style=';margin-top:5px;'>\n".makepagenav($_GET['rowstart'],$faq_items,$rows,3)."\n</div>\n";


require_once THEMES."templates/footer.php";
?>
