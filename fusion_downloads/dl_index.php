<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

if (!iADMIN && $dl_settings['maintenance'] == 1) { redirect(DL_DIR."dl_maintenance.php"); }

$get = "";
$db_where = "";
$db_orderby = "";
$limit = "";
$dl_cat = ""; $dl_fusion = ""; $sort = ""; $order_by = ""; $rowstart = ""; $items = ""; $dl_sort = "";

// GET Verarbeitung
if (isset($_GET['dl_cat']) && isnum($_GET['dl_cat']) && $_GET['dl_cat'] != "") {
    $get .= (empty($get) ? "?" : "&amp;")."dl_cat=".$_GET['dl_cat'];
    $dl_cat = $_GET['dl_cat'];
    $db_where = " AND dl_cat='".$_GET['dl_cat']."'";
}
if (isset($_GET['dl_fusion']) && isnum($_GET['dl_fusion']) && $_GET['dl_fusion'] != "") {
    $get .= (empty($get) ? "?" : "&amp;")."dl_fusion=".$_GET['dl_fusion'];
    $dl_fusion = $_GET['dl_fusion'];
    $db_where = " AND dl_fusion REGEXP('^\\\.{$_GET['dl_fusion']}$|\\\.{$_GET['dl_fusion']}\\\.|\\\.{$_GET['dl_fusion']}$')";
}
if (isset($_GET['dl_sort']) && isset($_GET['order_by']) && ($_GET['order_by'] == "ASC" || $_GET['order_by'] == "DESC")) {
    $get .= (empty($get) ? "?" : "&amp;")."dl_sort=".trim(stripinput($_GET['dl_sort']))."order_by=".$_GET['order_by'];
    $dl_sort = trim(stripinput($_GET['dl_sort']));
    $order_by = $_GET['order_by'];
    $db_orderby = ", ".$dl_sort." ".$_GET['order_by'];
    //echo $order_by; die();
} else {
    $dl_sort = "dl_title";
    $order_by = "dl_title";
    $db_orderby = ", dl_title ASC";
}

if (isset($_GET['rowstart']) && isnum($_GET['rowstart'])) {
    //$get .= (empty($get) ? "?" : "&amp;")."rowstart=".$_GET['rowstart'];
    $rowstart = $_GET['rowstart'];
} else {
    $rowstart = "0";
}

if (isset($_GET['items']) && (isnum($_GET['items']) || $_GET['items'] == "all")) {
    $get .= (empty($get) ? "?" : "&amp;")."items=".$_GET['items'];
    if ($_GET['items'] == "all") {
    	$items = "all";
    	$limit = "";
        $items2 = 0;
    } else {
    	$limit = "LIMIT ".$rowstart.",".$_GET['items'];
    	$items = $_GET['items'];
        $items2 = $items;
    }
} else {
    $limit = "LIMIT ".$rowstart.",20";
    $items = "20";
    $items2 = $items;
}


opentable("Fusion Downloads");
echo "<form action='".FUSION_SELF."' method='get' />\n";
echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top'>\n";
echo "<tr>";
echo "<td class='tbl1' width='400px'><img src='".IMAGES."php-fusion-logo.png' title='PHP-Fusion Logo' alt='PHP-Fusion Logo' /></td>\n";
echo "<td class='tbl1' width='300px'>";
echo "<table cellpadding='0' cellspacing='0'>\n";
echo "<tr>\n";
echo "<td class='tbl1'>Kategorie:</td>\n";
echo "<td class='tbl1'>".dl_cat_options($dl_cat, "style='width:200px;'", TRUE)."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>PHP Fusion:</td>\n";
echo "<td class='tbl1'>".dl_fusion_options($dl_fusion, "style='width:200px;'", TRUE)."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>Sortieren:</td>\n";
echo "<td class='tbl1'>".dl_sort_options($dl_sort, "style='width:98px;'", $order_by)."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'>Anzahl:</td>\n";
echo "<td class='tbl1'>".dl_items_options($items, "style='width:200px;'")."</td>\n";
echo "</tr><tr>\n";
echo "<td class='tbl1'></td>\n";
echo "<td class='tbl1'><input type='submit' value=' Absenden' class='button' /></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</td>\n";
echo "</tr></table>\n";
echo "</form>\n";
echo "<br />";
$rows = dbcount("(dl_id)", DL, "dl_status='2' ".$db_where);
$result = dbquery("SELECT aa.dl_id, aa.dl_cat, aa.dl_fusion, aa.dl_title, aa.dl_version, aa.dl_author, aa.dl_timestamp, aa.dl_user_id, aa.dl_count, ac.dl_cat_name, ae.user_name
                   FROM ".DL." aa
                   LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                   LEFT JOIN ".DB_USERS." ae ON aa.dl_user_id=ae.user_id
                   WHERE dl_status='2' ".$db_where."
                   GROUP BY aa.dl_id, ac.dl_cat_id
                   ORDER BY ac.dl_cat_order ".$db_orderby."
                   ".$limit);
if (dbrows($result) > 0) {
    echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top'>\n";
    echo "<tr>\n";
    echo "<td class='tbl2'></td>\n";
    echo "<td class='tbl2'>Titel (Anzahl der Downloads)</td>\n";
    echo "<td class='tbl2'>Datum</td>\n";
    echo "<td class='tbl2'>Autor</td>\n";
    echo "<td class='tbl2'>Version</td>\n";
    echo "<td class='tbl2'>Fusion</td>\n";
    echo "<td class='tbl2'>Rating</td>\n";
    echo "</tr>";
    $cat_dummy = "";
    $dl_cat = "";
    while ($data = dbarray($result)) {
        if ($data['dl_cat'] != $cat_dummy) {
			echo "<tr>\n";
            echo "<td class='forum-caption'></td>\n";
            echo "<td class='forum-caption'>".$data['dl_cat_name']."</td>\n";
            echo "<td class='forum-caption'></td>\n";
            echo "<td class='forum-caption'></td>\n";
            echo "<td class='forum-caption'></td>\n";
            echo "<td class='forum-caption'></td>\n";
            echo "<td class='forum-caption'></td>\n";
			echo "</tr>\n";
        }
        $cat_dummy = $data['dl_cat'];
		$time = date('d.m.Y H:i', $data['dl_timestamp']);
        if ($data['dl_timestamp'] > (time() - "432000")) { $new = "<img src='".DL_DIR."images/icon_neu.gif' border='0' alt='' />";} else {$new = "";}
		
        echo "<tr>\n";
        echo "<td class='tbl2'></td>";
        echo "<td class='tbl1'>".$new." <a href='".DL_DIR."dl_details.php?dl_id=".$data['dl_id']."'>".trimlink($data['dl_title'],40)."</a> (".$data['dl_count'].")</td>\n";
        echo "<td class='tbl1'>".date("d.m.Y", $data['dl_timestamp'])."</td>\n";
        echo "<td class='tbl1'>".profile_link($data['dl_user_id'], $data['user_name'], "1")."</td>\n";
        echo "<td class='tbl1'>".$data['dl_version']."</td>\n";
        $dummy = explode(".", $data['dl_fusion']);
        $fusions = "";
        $fusion_query = dbquery("SELECT dl_fusion_id, dl_fusion_name FROM ".DL_FUSION." ORDER BY dl_fusion_order DESC");
        $i = 1;
        while ($data2 = dbarray($fusion_query)) {
            if (in_array($data2['dl_fusion_id'], $dummy)) {
                if ($i > 1) { $fusions .= ", "; }
                $fusions .= $data2['dl_fusion_name'];
                $i++;
            }
        }
        echo "<td class='tbl1'>".$fusions."</td>\n";
        echo "<td class='tbl1'>";
        $result4 = dbquery("SELECT SUM(rating_vote) as sum_rating, COUNT(rating_item_id) as sum_votes FROM ".DB_RATINGS." WHERE rating_item_id='".$data['dl_id']."' AND rating_type='M'");
        if (dbrows($result4)) {
            $rate_array = dbarray($result4);
            if ($rate_array['sum_votes'] > 0) {
                $rate = $rate_array['sum_rating'] / $rate_array['sum_votes'];
                echo "<img src='".DL_DIR."images/".ceil($rate).".gif' alt='".ceil($rate)."' style='vertical-align:middle;' alt='Bewertung: ".ceil($rate)."' />";
            }
        }
        echo "</td>\n</tr>\n";
    }
    echo "</table>";
    if ($rows > $items2 && $items != "all") echo "<div align='center' style='margin-top:5px;'>\n".makePageNav($rowstart, $items, $rows, 3, ($get ? FUSION_SELF."".$get."&amp;" : ""))."\n</div>\n";
} else {
    echo "<center>Es sind keine Downloads mit diesen Vorgaben vorhanden.</center>";
}

echo "<table cellpadding='0' cellspacing='0' width='700px' align='center' style='border-collapse:collapse; margin-top:20px;'>\n";
echo "<tr>\n";
echo "<td width='33%' valign='top'>";
// Panel 1 Start: Neue Downloads
echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border' style='border-collapse:collapse'>\n";
echo "<tr>\n";
echo "<td class='tbl2'>Neue Downloads</td>\n";
echo "</tr>\n";
$result = dbquery("SELECT dl_id, dl_title, dl_timestamp FROM ".DL." WHERE dl_status='2' ORDER BY dl_timestamp DESC LIMIT 6");
if (dbrows($result) > 0) {
    while ($data = dbarray($result)) {
        echo "<tr>\n";
        echo "<td class='tbl1' style='padding-left:5px'><a href='".DL_DIR."dl_details.php?dl_id=".$data['dl_id']."'>".trimlink($data['dl_title'],35)."</td>\n";
        echo "</tr>\n";
    }
} else {
    echo "<tr><td class='tbl1' style='padding-left:5px'>Keine Downloads vorhanden.</td>\n</tr>\n";
}
echo "</table>\n";
// Panel 1 Ende
echo "</td><td width='33%' valign='top'>";
// Panel 2 Start: Top Downloads
echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border' style='border-collapse:collapse'>\n";
echo "<tr>\n";
echo "<td class='tbl2'>Top Downloads</td>\n<td class='tbl2'></td>\n";
echo "</tr>\n";
$result = dbquery("SELECT dl_id, dl_title, dl_count FROM ".DL." WHERE dl_status='2' ORDER BY dl_count DESC LIMIT 6");
if (dbrows($result) > 0) {
    while ($data = dbarray($result)) {
        echo "<tr>\n";
        echo "<td class='tbl1' style='padding-left:5px'><a href='".DL_DIR."dl_details.php?dl_id=".$data['dl_id']."'>".trimlink($data['dl_title'],30)."</a></td>\n";
        echo "<td class='tbl1' style='text-align:right;'>(".$data['dl_count'].")</td>\n";
        echo "</tr>\n";
    }
} else {
    echo "<tr><td class='tbl1' style='padding-left:5px'>Keine Downloads vorhanden.</td>\n</tr>\n";
}
echo "</table>\n";
// Panel 2 Ende
echo "</td><td width='33%' valign='top'>";
// Panel 3 Start: Best bewertesten Downloads
echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border' style='border-collapse:collapse'>\n";
echo "<tr>\n";
echo "<td class='tbl2'>Bestbewertesten Downloads</td>\n<td class='tbl2'></td>\n";
echo "</tr>\n";
 $rate_array =dbquery("SELECT rating_item_id, SUM(rating_vote) AS sum_rating, COUNT(rating_item_id) AS count_votes FROM ".DB_RATINGS." WHERE rating_type='M' GROUP BY rating_item_id");
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
 foreach ($dummy AS $value) {
     if ($i == 6) { break; }
     if ($value[2] > 3) {
         $data = dbarray(dbquery("SELECT dl_id, dl_title, dl_status FROM ".DL." WHERE dl_id='".$value[0]."'"));
         if ($data['dl_status'] == 2) {
             echo "<tr>\n";
             echo "<td class='tbl1'><a href='".DL_DIR."dl_details.php?dl_id=".$data['dl_id']."'>".trimlink($data['dl_title'],30)."</a></td>\n";
             echo "<td class='tbl1'>(".number_format(round($value[3],2),2,".",",")."/5)</td>\n";
             echo "</tr>\n";
             $i++;
         }
     }
     //if ($i == 6) { exit; }
 }
//} else {
//    echo "<tr><td class='tbl1' style='padding-left:5px'>Keine Downloads vorhanden.</td>\n</tr>\n";
//}
echo "</table>\n";
// Panel 3 Ende
echo "</td>\n</tr>\n</table>\n";
closetable();
require_once THEMES."templates/footer.php";
?>
