<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
include INFUSIONS."faq_db/includes/faq_core.php";
require_once INCLUDES."comments_include.php";
require_once INCLUDES."ratings_include.php";

if (!isset($_GET['q_id']) || !isnum($_GET['q_id'])) { redirect(FAQ_DIR."faq_db.php"); }
$q_id = $_GET['q_id'];

if (isset($_GET['a_id']) && isnum($_GET['a_id'])) {
    if (isset($_GET['edit'])) { redirect(FAQ_DIR."admin/faq_admin_edit.php?q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']); }
    elseif (isset($_GET['disable_all'])) {
        $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_status='2' WHERE faq_q_id='".$_GET['q_id']."'");
        $result = dbquery("UPDATE ".DB_FAQ_A." SET faq_a_status='2' WHERE faq_q_id='".$_GET['q_id']."'");
    }
    elseif (isset($_GET['disable'])) {
        $result = dbquery("UPDATE ".DB_FAQ_A." SET faq_a_status='2' WHERE faq_a_id='".$_GET['a_id']."'");
        $count = dbcount("(faq_a_id)", DB_FAQ_A, "faq_a_status='1'");
        if ($count == 0) {
            $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_status='2' WHERE faq_q_a='".$_GET['a_id']."'");
        }
    }
}

if (!isset($_GET['a_id']) || !isnum($_GET['a_id'])) {
    $data = dbarray(dbquery("SELECT faq_q_a FROM ".DB_FAQ_Q." WHERE faq_q_id='".$_GET['q_id']."'"));
    $_GET['a_id'] = $data['faq_q_a'];
}

if (iADMIN) {
    $status = "";
} else {
    $status = "AND aa.faq_q_status='1' AND ab.faq_a_status='1'";
}

$result = dbquery("SELECT   aa.faq_q_id, aa.faq_q_title,
                            ab.faq_a_id, faq_a_user_id, faq_a_fusion, faq_a_text, faq_a_timestamp, faq_a_counter,
                            ac.faq_cat_id, ac.faq_cat_title,
                            ad.user_name, ad.user_status
                            FROM ".DB_FAQ_Q." aa, ".DB_FAQ_A." ab, ".DB_FAQ_CAT." ac, ".DB_USERS." ad
                            WHERE aa.faq_q_id='".$_GET['q_id']."'
                            AND ab.faq_a_id='".$_GET['a_id']."'
                            AND aa.faq_q_cat_id=ac.faq_cat_id
                            AND ab.faq_a_user_id=ad.user_id
                            ".$status
                            );
$rows = dbrows($result);
if ($rows > 0) {
    $data = dbarray($result);
    if (faq_cookie_check($data['faq_a_id'])) {
        $result = dbquery("UPDATE ".DB_FAQ_Q." SET faq_q_counter=faq_q_counter+1 WHERE faq_q_id='".$_GET['q_id']."'");
        $result = dbquery("UPDATE ".DB_FAQ_A." SET faq_a_counter=faq_a_counter+1 WHERE faq_a_id='".$data['faq_a_id']."'");
    }
    require_once FAQ_BREADCRUMBS;
    opentable("Sie befinden sich hier:");
    echo do_breadcrumbs($data['faq_cat_id'], $data['faq_q_title']);
    closetable();
    add_to_title(" Fusion FAQ: ".$data['faq_q_title']);
    opentable("Fusion FAQ: ".$data['faq_q_title']);
    if (isset($_GET['submit_a'])) {
        echo "<div id='close-message'><div class='admin-message'>Deine Antwort wurde erfolgreich gespeichert und wird von den Administratoren gepr&uuml;ft.</div></div>\n";
    }
    // @ TODO: Adminoptionen als seitliches Panel
    /*if (iADMIN) {
        echo "<table cellpadding='0' cellspacing='0' width='600px' align='center' class='tbl-border'>\n";
        echo "<tr>\n";
        echo "<td class='tbl'><strong>Admin:</strong></td>\n";
        echo "<td class='tbl'><a href='".FUSION_SELF."?a_id=".$data['faq_a_id']."&amp;edit'>Editieren</td>\n";
        echo "<td class='tbl'><a href='".FUSION_SELF."?a_id=".$data['faq_q_id']."&amp;disable_all'>Frage deaktivieren</td>\n";
        echo "<td class='tbl'><a href='".FUSION_SELF."?a_id=".$data['faq_a_id']."&amp;disable'>Antwort deaktivieren</td>\n";
        echo "</tr>\n";
        echo "</table>\n";
        echo "<br />";
    }*/
    $dummy1 = explode(",", $data['faq_a_fusion']);
    $dummy2 = array();
    $result3 = dbquery("SELECT faq_fusion_id, faq_fusion_1st, faq_fusion_2nd FROM ".DB_FAQ_FUSION." ORDER BY faq_fusion_order ASC");
    if (dbrows($result3) > 0) {
        while ($data3 = dbarray($result3)) {
            $id = $data3['faq_fusion_id'];
            $dummy2[$id] = $data3['faq_fusion_1st'].".".$data3['faq_fusion_2nd'];
        }
    }
    $count = count($dummy1);
    $i = 0;
    $versions = "";
    foreach ($dummy1 as $value) {
        $i++;
        if ($i != $count) {
            $versions .= $dummy2[$value].", ";
        } else {
            $versions .= $dummy2[$value];
        }
    }
    echo parseubb(nl2br($data['faq_a_text']))."<br /><br />";

    echo "<table cellpadding='0' cellspacing='0' width='95%' align='center' class='tbl-border'>\n";
    echo "<tr>\n";
    echo "<td class='tbl2' width='25%'>Ersteller:</td>\n";
    echo "<td class='tbl' width='25%'>".profile_link($data['faq_a_user_id'], $data['user_name'], $data['user_status'])."</td>\n";
    echo "<td class='tbl2' width='25%'>Datum:</td>\n";
    echo "<td class='tbl' width='25%'>".date('d.m.Y', $data['faq_a_timestamp'])."</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class='tbl2'>PHP Fusion Versionen:</td>\n";
    echo "<td class='tbl'>".$versions."</td>\n";
    echo "<td class='tbl2'>Aufrufe:</td>\n";
    echo "<td class='tbl'>".$data['faq_a_counter']."</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "<table cellpadding='0' cellspacing='0' width='95%' align='center' class='tbl-border' style='margin-top:10px;'>\n";
    echo "<tr>\n";
    echo "<td class='tbl2' style='text-align:center; width:100%'>Klicke <a href='".FAQ_DIR."faq_submit_a.php?q_id=".$_GET['q_id']."&amp;a_id=".$_GET['a_id']."'>hier</a> um eine eigene bessere Antwort auf diese Frage zu erstellen.</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    closetable();
    $q_id = $_GET['q_id'];
    $a_id = $_GET['a_id'];
    showratings("F",  $a_id, FUSION_SELF."?q_id=".$q_id."&amp;a_id=".$a_id);
    //showcomments("F", DB_FAQ_A, "faq_q_id", $a_id, FUSION_SELF."?q_id=".$q_id."&amp;a_id=".$a_id);

    if ($rows > 0) {
        $result2 = dbquery("SELECT  aa.faq_q_id, aa.faq_q_title,
                                    ab.faq_a_id, faq_a_user_id, faq_a_fusion, faq_a_text, faq_a_timestamp, faq_a_counter,
                                    ac.faq_cat_title,
                                    ad.user_name, ad.user_status
                                    FROM ".DB_FAQ_Q." aa, ".DB_FAQ_A." ab, ".DB_FAQ_CAT." ac, ".DB_USERS." ad
                                    WHERE aa.faq_q_a=ab.faq_a_id
                                    AND aa.faq_q_id!=".$_GET['q_id']."
                                    AND aa.faq_q_status='1' AND ab.faq_a_status='1'
                                    AND ab.faq_a_user_id='".$data['faq_a_user_id']."'
                                    AND aa.faq_q_cat_id=ac.faq_cat_id
                                    AND ab.faq_a_user_id=ad.user_id
                                    ORDER BY RAND()
                                    LIMIT 5");
        $rows2 = dbrows($result2);
        if ($rows2 > 0) {
            opentable("Andere Beitr&auml;ge von ".$data['user_name']);
            echo "<table cellpadding='0' cellspacing='0' width='95%' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td class='tbl'>Titel</td>\n";
            echo "<td class='tbl'>Datum</td>\n";
            echo "<td class='tbl'>Bewertung</td>\n";
            echo "</tr>\n";
            while ($data2 = dbarray($result2)) {
                echo "<tr>\n";
                echo "<td class='tbl'><a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."&a_id=".$data['faq_a_id']."'>".$data['faq_q_title']."</a></td>\n";
                echo "<td class='tbl'>".date('d.m.Y', $data['faq_a_timestamp'])."</td>\n";
                echo "<td class='tbl'>";
                $result4 = dbquery("SELECT SUM(rating_vote) sum_rating, COUNT(rating_item_id) sum_votes FROM ".DB_RATINGS." WHERE rating_item_id='".$data['faq_a_id']."' AND rating_type='F'");
                if (dbrows($result4)) {
                    $rate_array = dbarray($result4);
                    if ($rate_array['sum_votes'] > 0) {
                        $rate = $rate_array['sum_rating'] / $rate_array['sum_votes'];
                        echo "<img src='".DL_DIR."images/".ceil($rate).".gif' alt='".ceil($rate)."' style='vertical-align:middle;' title='Bewertung: ".ceil($rate)."' />";
                    }
                }
                echo"</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
            closetable();
        }
    }
} else {
    opentable("FAQ DB");
    echo "<center>Dieser Beitrag existiert nicht mehr oder der Zugriff wurde gesperrt.";
    closetable();
}
require_once THEMES."templates/footer.php";
?>
