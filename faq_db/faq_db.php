<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
include INFUSIONS."faq_db/includes/faq_core.php";
require_once INFUSIONS."faq_db/includes/class.nestedsets.php";
require_once INCLUDES."bbcode_include.php";

$obj = new NestedSets(DB_FAQ_CAT, "faq_cat_", "faq_cat_title, faq_cat_description", "faq_cat_id");
$tree = $obj->GetNestedSets((isset($_GET['cat_id']) && isnum($_GET['cat_id']) ? $_GET['cat_id'] : "0"));

// TODO: Nested Sets

require_once FAQ_BREADCRUMBS;
opentable("Sie befinden sich hier:");
echo do_breadcrumbs((isset($_GET['cat_id']) && isnum($_GET['cat_id']) ? $_GET['cat_id'] : "0"));
closetable();

if (dbrows($tree) > 0) {
    $rows = dbrows($tree);
    $i = 1;
    $lvl_before = 0;
    $buffer = "";
    while ($data = dbarray($tree)) {
        if ($i == 1) { $lvl_start = $data['level']; }
        if ($data['level'] == $lvl_start && $lvl_before >= $lvl_start) {
            if ($lvl_before > $lvl_start) {
                $buffer .= "</td>\n";
            }
            $buffer .= "</tr>\n</table>\n";
        }// Test
        if (($data['level'] == ($lvl_start+1))) {
            if ($lvl_before == $data['level']) {
                $buffer .= ", <a href='".FUSION_SELF."?cat_id=".$data['faq_cat_id']."'>".$data['faq_cat_title']."  (".dbcount("(faq_q_id)", DB_FAQ_Q, "faq_q_cat_id='".$data['faq_cat_id']."'").")</a>";
            } else {
                $buffer .= "</tr>\n<tr>\n";
                $buffer .= "<td class='tbl1 small'>Unterkategorien: ";
                $buffer .= "<a href='".FUSION_SELF."?cat_id=".$data['faq_cat_id']."'>".$data['faq_cat_title']." (".dbcount("(faq_q_id)", DB_FAQ_Q, "faq_q_cat_id='".$data['faq_cat_id']."'").")</a>";
            }
        }
        if ($data['level'] == $lvl_start) {
            $buffer .= "<table cellpadding='0' cellspacing='0' width='600px' align='center' class='tbl-border' style='margin-bottom:10px;'>\n";
            $buffer .= "<tr>\n";
            $buffer .= "<td class='tbl2'><a href='".FUSION_SELF."?cat_id=".$data['faq_cat_id']."'>".$data['faq_cat_title']."  (".dbcount("(faq_q_id)", DB_FAQ_Q, "faq_q_cat_id='".$data['faq_cat_id']."'").")</a></td>\n";
            if ($data['faq_cat_description'] != "") {
                $buffer .= "</tr>\n<tr>\n";
                $buffer .= "<td class='tbl1'>".$data['faq_cat_description']."</td>\n";
            }
        }
        if ($rows == $i) {
            $buffer .= "</tr>\n</table>\n";
        }
        $lvl_before = $data['level'];
        $i++;
    }
    opentable((isset($_GET['cat_id']) && isnum($_GET['cat_id']) ? "FAQ DB: Unterkategorien" : "FAQ DB: Kategorien"));
    echo $buffer;
    closetable();
}

if (isset($_GET['cat_id']) && isnum($_GET['cat_id'])) {
    $result = dbquery("SELECT aa.faq_q_id, aa.faq_q_title, aa.faq_q_a, ab.faq_a_fusion FROM ".DB_FAQ_Q." AS aa, ".DB_FAQ_A." AS ab
                       WHERE aa.faq_q_cat_id='".$_GET['cat_id']."' AND faq_q_status='1' AND aa.faq_q_a=ab.faq_a_id");
    $rows = dbrows($result);
    if ($rows > 0) {
        $i = 1;
        opentable("FAQ DB: Fragen in dieser Kategorie");
        echo "<table cellspacing='0' cellpadding='0' width='600px' align='center' class='tbl-border'>\n";
        $dummy2 = array();
        $result3 = dbquery("SELECT faq_fusion_id, faq_fusion_1st, faq_fusion_2nd FROM ".DB_FAQ_FUSION." ORDER BY faq_fusion_order ASC");
        if (dbrows($result3) > 0) {
            while ($data3 = dbarray($result3)) {
                $id = $data3['faq_fusion_id'];
                $dummy2[$id] = $data3['faq_fusion_1st'].".".$data3['faq_fusion_2nd'];
            }
        }
        $i = 1;
        while ($data = dbarray($result)) {
            $dummy1 = explode(",", $data['faq_a_fusion']);
            $count = count($dummy1);
            $j = 0;
            $versions = "";
            foreach ($dummy1 as $value) {
                $j++;
                if ($j != $count) {
                    $versions .= "v".$dummy2[$value].", ";
                } else {
                    $versions .= "v".$dummy2[$value];
                }
            }
            echo "<tr>";
            echo "<td class='tbl1' width='70%'><a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."&amp;a_id=".$data['faq_q_a']."'>".$data['faq_q_title']."</a></td>\n";
            echo "<td class='tbl1' width='30%' style='text-align:right;'>".$versions."</td>\n";
            echo "</tr>\n";
            
            /*
            if ($i == "1") { echo "<tr>\n"; }
            if ($i != "1" && $i % 2) { echo "</tr><tr>\n"; }
            echo "<td class='tbl'>".$data['faq_q_title']." (".$versions.")</td>\n";
            if ($i == $rows) {
                if ($i % 2) { echo "</tr>"; }
                else { echo "<td class='tbl'></td></tr>\n"; }
            }
            $i++;*/
        }
        echo "</table>\n";
        closetable();
    } else {
        opentable("FAQ Datenbank");
        echo "<center>Es sind noch keine Eintr&auml;ge in diser Kategorie enthalten.</center>";
        closetable();
    }
} 

if (!isset($_GET['cat_id']) || !isnum($_GET['cat_id'])) {
    add_to_head("
    <style type=text/css><!--
    .panel_li {
        padding-left:5px;
    }

    .panel_ul {
        padding-left:15px;
    }
    //--></style>
    ");

    openside("Neue Fragen");
    $result = dbquery("SELECT faq_q_id, faq_q_title, faq_q_timestamp, faq_q_counter, faq_q_a FROM ".DB_FAQ_Q." WHERE faq_q_status='1' ORDER BY faq_q_timestamp DESC LIMIT 5");
    if (dbrows($result) > 0) {
        echo "<table width='600px' align='center' class='tbl-border'>\n";
        $i = 1;
		echo "<tr>\n";
		echo "<td class='tbl2small' style='width:100px'>Datum</td>\n";
		echo "<td class='tbl2small' style='width:400px'>Titel</td>\n";
		echo "<td class='tbl2small' style='width:100px;'align='right'>Aufrufe</td>\n";
		echo "</tr>\n";
        while ($data = dbarray($result)) {
            echo "<tr>\n";
			echo "<td class='tbl1small'>".date('d.m.Y', $data['faq_q_timestamp'])."</td>\n";
            echo "<td class='tbl1small'><a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."'>".trimlink($data['faq_q_title'],150)."</a></td>\n";
            echo "<td class='tbl1small' align='right'><span style='text-align:center;'>".$data['faq_q_counter']."</span></td>\n";
			echo "</tr>\n";
            $i++;
        }
        echo "</table>";
    } else {
        echo "Keine Beitr&auml;ge vorhanden.";
    }
    closeside();

    
    echo "<table width='100%'><tr>";
    echo "<td width='50%' valign='top'>";
    openside("Meistgesehenen Beitr&auml;ge");
    $result = dbquery("SELECT faq_q_id, faq_q_title, faq_q_a, faq_q_counter FROM ".DB_FAQ_Q." WHERE faq_q_status='1' ORDER BY faq_q_counter DESC LIMIT 5");
    if (dbrows($result) > 0) {
        echo "<table width='100%'>\n";
        $i = 1;
        while ($data = dbarray($result)) {
            echo "<tr>\n";
            echo "<td class='tbl1small'>".$i.". <a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."'>".trimlink($data['faq_q_title'],50)."</a></td>\n";
            echo "<td class='tbl1small' align='right'>".$data['faq_q_counter']."</td>\n";
            echo "</tr>\n";
            $i++;
        }
        echo "</table>";
    } else {
        echo "Keine Beitr&auml;ge vorhanden.";
    }
    closeside();
    echo "</td><td width='50%' valign='top'>";

    $result = 0;
    openside("Bestbewertesten Beitr&auml;ge");
    $rate_array =dbquery("SELECT rating_item_id, SUM(rating_vote) AS sum_rating, COUNT(rating_item_id) AS count_votes FROM ".DB_RATINGS." WHERE rating_type='F' GROUP BY rating_item_id");
    $dummy = array();
    while ($data = dbarray($rate_array)) {
        $dummy[] = array($data['rating_item_id'], $data['sum_rating'], $data['count_votes'], ($data['sum_rating']/$data['count_votes']));
    }
    $dummy2 = array();
    foreach ($dummy AS $key => $row) {
        $dummy2[$key] = $row[3];
    }
    $i = 0;
    array_multisort($dummy2, SORT_DESC, $dummy);
    if (count($dummy > 1)) {
        echo "<table width='100%'>\n";;
        $i = 1;
        foreach ($dummy AS $value) {
            if ($i == 6) { break; }
            $data = dbarray(dbquery("SELECT faq_q_id, faq_q_title, faq_q_status FROM ".DB_FAQ_Q." WHERE faq_q_id='".$value[0]."' AND faq_q_status='1'"));
            if ($data['faq_q_status'] == 1) {
                echo "<tr>\n";
                echo "<td class='tbl1small'>".$i.". <a href='".FAQ_DIR."faq_details.php?q_id=".$data['faq_q_id']."'>".trimlink($data['faq_q_title'],50)."</a></td>\n";
                echo "<td class='tbl1small'>(".number_format(round($value[3],2),2,".",",")."/5)</td>\n";
                echo "</tr>\n";
                $i++;
            } 
        }
        echo "</table>";
    } else {
        echo "Keine Beitr&auml;ge vorhanden.";
    }
    closeside();
    echo "</td></tr></table>\n";

}
require_once THEMES."templates/footer.php";
?>
