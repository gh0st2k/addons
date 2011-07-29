<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
include INFUSIONS."faq_db/includes/faq_core.php";

/**
 * 
 * Test für Github 2
 * 
 **/

if (iMEMBER) {
    if (isset($_GET['delete']) && isset($_GET['q_id']) && isnum($_GET['q_id'])) {
        $check = dbcount("(faq_q_id)", DB_FAQ_Q, "faq_q_id='".$_GET['q_id']."' AND faq_q_user_id='".$userdata['user_id']."'");
        if ($check > 0 || iADMIN) {
            $result = dbquery("DELETE FROM ".DB_FAQ_Q." WHERE faq_q_id='".$_GET['q_id']."'");
            $result = dbquery("DELETE FROM ".DB_FAQ_A." WHERE faq_q_id='".$_GET['q_id']."'");
        }
    }
    $double_array = array();
    /*$result = dbquery("SELECT aa.faq_q_id, aa.faq_q_user_id, aa.faq_q_title, aa.faq_q_a, aa.faq_q_timestamp, ab.faq_a_id, ab.faq_a_status, ab.faq_a_user_id, ac.faq_cat_title, ad.user_name, ad.user_status
                          FROM ".DB_FAQ_Q." aa, ".DB_FAQ_A." ab, ".DB_FAQ_CAT." ac, ".DB_USERS." ad
                          WHERE ab.faq_a_user_id='".$userdata['user_id']."'
                          AND ab.faq_q_id=aa.faq_q_a
                          AND aa.faq_q_cat_id=ac.faq_cat_id
                          AND aa.faq_q_user_id=ad.user_id");*/
    $result = dbquery("SELECT aa.faq_q_id, aa.faq_q_user_id, aa.faq_q_title, aa.faq_q_a, aa.faq_q_timestamp, ab.faq_a_id, ab.faq_a_status, ab.faq_a_user_id, ac.faq_cat_title, ad.user_name, ad.user_status
                        FROM ".DB_FAQ_A." ab
                        LEFT JOIN ".DB_FAQ_Q." aa ON ab.faq_q_id=aa.faq_q_id
                        LEFT JOIN ".DB_FAQ_CAT." ac ON aa.faq_q_cat_id=ac.faq_cat_id
                        LEFT JOIN ".DB_USERS." ad ON aa.faq_q_user_id=ad.user_id");
    opentable("FAQ Center");
    if (dbrows($result) > 0) {
        if (isset($_GET['submit_a'])) {
            echo "<div id='close-message'><div class='admin-message'>Deine Antwort wurde erfolgreich gespeichert und wird von den Administratoren gepr&uuml;ft.</div></div>\n";
        }
        echo "<table cellpadding='0' cellspacing='0' width='700px' align='center' class='tbl-border'>\n";
        echo "<tr>\n";
        echo "<td class='tbl'>Id</td>\n";
        echo "<td class='tbl'>Kategorie</td>\n";
        echo "<td class='tbl'>Datum</td>\n";
        echo "<td class='tbl'>Titel</td>\n";
        echo "<td class='tbl'>Status</td>\n";
        echo "<td class='tbl'>Optionen</td>\n";
        echo "</tr>\n";
        while ($data = dbarray($result)) {
            if (!in_array($data['faq_q_a'], $double_array) && $data['faq_a_user_id'] == $userdata['user_id']) {
                echo "<tr>\n";
                echo "<td class='tbl'>".$data['faq_q_id']."</td>\n";
                echo "<td class='tbl'>".$data['faq_cat_title']."</td>\n";
                echo "<td class='tbl'>".date('d.m.Y',$data['faq_q_timestamp'])."</td>\n";
                echo "<td class='tbl'>".trimlink($data['faq_q_title'], 25)."</td>\n";
                echo "<td class='tbl'>".status_output($data['faq_a_status'])."</td>";
                echo "<td class='tbl'>";
                echo "<a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."&amp;a_id=".$data['faq_a_id']."'>Details</a>\n";
                //echo "<a href='".FAQ_DIR."faq_admin_edit.php?amp;q_id=".$data['faq_q_id']."&amp;details&amp;a_ id=".$data['faq_a_id']."'>Bearbeiten</a>\n";
                echo "<a href='".FUSION_SELF."&amp;q_id=".$data['faq_q_id']."&amp;delete'>L&ouml;schen</a>\n";
                echo "</td>\n";
                echo "</tr>\n";
                $double_array[] = $data['faq_q_a'];
            }
        }
        echo "</table>\n";
    } else {
        echo "<center>Du hast noch keine Einsendung get&auml;tigt.</center>\n";
    }
    closetable();
} else {
    opentable("FAQ Datenbank");
    echo "<center>Du musst dich erst registrieren oder einloggen, damit du diese Seite sehen kannst.</center>";
    closetable();
}

require_once THEMES."templates/footer.php";
?>