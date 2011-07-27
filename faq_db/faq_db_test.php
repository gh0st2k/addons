<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
include INFUSIONS."faq_db/includes/faq_core.php";
require_once INFUSIONS."faq_db/includes/class.nestedsets.php";
require_once INCLUDES."bbcode_include.php";

$obj = new NestedSets(DB_FAQ_CAT, "faq_cat_", "faq_cat_title, faq_cat_description", "faq_cat_id");
$tree = $obj->GetNestedSets("0");

require_once FAQ_BREADCRUMBS;
opentable("Sie befinden sich hier:");
echo do_breadcrumbs((isset($_GET['cat_id']) && isnum($_GET['cat_id']) ? $_GET['cat_id'] : "0"));
closetable();

if (dbrows($tree) > 0) {
	$buffer1 = ""; 
	$i = 1;
	$buffer1 .=  "<table width='98%' cellpadding='1' cellspacing='0' class='tbl-border' align='center'>\n";
	while ($data = dbarray($tree)) {
		$class = (isset($_GET['cat_id']) && $_GET['cat_id'] == $data['faq_cat_id'] ? "tbl2" : "tbl1");
		if ($data['level'] == 1) {
			if (isset($_GET['cat_id']) && $_GET['cat_id'] == $data['faq_cat_id']) { $cat = $_GET['cat_id']; }
			if ($i == 1) {
				$cat = $data['faq_cat_id'];
				$buffer1 .= "<tr>";
			}
			$buffer1 .= "<td width='20%' style='text-align:center;' class='".$class."'>";
			$buffer1 .= "<a href='".FUSION_SELF."?cat_id=".$data['faq_cat_id']."'' title='".$data['faq_cat_title'].": ".$data['faq_cat_description']."'>".$data['faq_cat_title']."</a>";
			$buffer1 .= "</td>\n";
			if ($i == 5) {
				$buffer1 .= "</tr>\n";
			}
			$i++;
		} 	
	}
	$buffer1 .=  "</table>\n";
}
$buffer2 = "";
$obj2 = new NestedSets(DB_FAQ_CAT, "faq_cat_", "faq_cat_title, faq_cat_description", "faq_cat_id");
$tree2 = $obj->GetNestedSets($cat);
$rows = dbrows($tree2);
if ($rows > 0) {
	$buffer2 .= "<table width='98%' cellpadding='5' cellspacing='0' class='tbl-border' align='center'>\n";
	while ($data = dbarray($tree2)) {
		if ($i == 1 || $i % 5 == 0) {
			$buffer2 .= "<tr>\n";
		}
		$buffer2 .= "<td class='tbl1' width='25%' height='30px' style='text-align:center;'>".$data['faq_cat_title']."</td>\n";
		if ($i == $rows || $i % 4 == 0) {
			$buffer2 .= "</tr>";
		}
	}
	$buffer2 .= "</table>";
}
opentable("FAQ Datenbank");
echo $buffer1;
echo $buffer2;
closetable();

require_once THEMES."templates/footer.php";
?>
