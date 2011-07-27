<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."faq_db/includes/faq_core.php";
require_once FAQ_INCLUDES."NestedSets.class.php";
require_once INCLUDES."infusions_include.php";
if (!checkrights("FAQ") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

$obj = new NestedSets(DB_FAQ_CAT, "faq_cat_id", "faq_cat_");

if (isset($_GET['move']) && isnum($_GET['move'])) {
	if (isset($_GET['up']) && isnum($_GET['up'])) {
		$obj->moveItem($_GET['move'], $_GET['up']);
	} elseif (isset($_GET['down']) && isnum($_GET['down'])) {
		$obj->moveItem($_GET['move'], $_GET['down']);
	}
}

if (isset($_GET['edit']) && isnum($_GET['edit'])) {
    $result = dbquery("SELECT faq_cat_title, faq_cat_description, faq_cat_parent FROM ".DB_FAQ_CAT." WHERE faq_cat_id='".$_GET['edit']."'");
    $edit = dbarray($result);
    $faq_title = $edit['faq_cat_title'];
    $faq_description = $edit['faq_cat_description'];
	$faq_cat_parent = $edit['faq_cat_parent'];
    $get = "&edit=".$_GET['edit'];
} else {
    $faq_title = ""; $faq_description = ""; $get = ""; $faq_cat_parent = "";
}

if (isset($_POST['save'])) {
    $faq_title = trim(stripinput($_POST['faq_title']));
    $faq_description = trim(stripinput($_POST['faq_description']));
	$obj->cols = array("faq_cat_title", "faq_cat_description");
    $obj->values = array($faq_title, $faq_description);
	$faq_father = (isset($_POST['faq_father']) && isnum($_POST['faq_father']) ? $_POST['faq_father'] : "1");
    if (!isset($_GET['edit']) || !isnum($_GET['edit'])) {
        $obj->addItem($faq_father);
    } else {
        $obj->updateItem($_GET['edit']);
		if ($faq_father != $faq_cat_parent) {
			$obj->moveItem($_GET['edit'], $faq_father);
		}
    }
}
$obj->cols = array("faq_cat_title", "faq_cat_description");
$tree = $obj->displayItems();

if (isset($_GET['del']) && isnum($_GET['del']) && isset($_GET['lft']) && isnum($_GET['lft']) && isset($_GET['rgt']) && isnum($_GET['rgt'])) {
    $obj->deleteItem($_GET['del'], $_GET['lft'], $_GET['rgt']);
}



opentable("FAQ DB Administration: Navigation");
require_once FAQ_DIR."admin/faq_admin_navigation.php";
closetable();

opentable("FAQ DB Administration: Kategorien erstellen");
echo "<form action='".FUSION_SELF.$aidlink.$get."' method='post'>\n";
echo "<table cellpadding='0' cellspacing='0' width='500px' align='center' class='tbl-border'>\n";
echo "<tr>\n";
echo "<td class='tbl1'>Titel:</td>\n";
echo "<td class='tbl1'><input type='text' name='faq_title' maxlength='50' class='textbox' style='width:200px' value='".$faq_title."' /></td>\n";
echo "</tr>\n<tr>\n";
echo "<td class='tbl1'>Beschreibung:</td>\n";
echo "<td class='tbl1'><textarea name='faq_description' cols='50' rows='5' class='textbox' style='with:200px'>".$faq_description."</textarea></td>\n";
echo "</tr>\n<tr>\n";
echo "<td class='tbl1'>Unterpunkt von:</td>\n";
$obj->cols = array("faq_cat_title");
echo "<td class='tbl1'><select name='faq_father' size='1' class='textbox' style='width:200px'>".$obj->displayOptions($faq_cat_parent)."</select></td>\n";
echo "</tr>\n<tr>\n";
echo "<td class='tbl1'></td>\n";
echo "<td class='tbl1'><input type='submit' value=' Kategorie speichern' class='button' name='save' /></td>\n";
echo "</tr>\n</table>\n</form>\n";
closetable();
opentable("FAQ DB Administration: Aktuelle FAQ Kategorien");
echo "<table cellpadding='0' cellspacing='0' width='500px' align='center' class='tbl-border'>\n";
echo "<tr>\n";
echo "<td class='tbl1'>Level:</td>\n";
echo "<td class='tbl1'>Titel:</td>\n";
echo "<td class='tbl1'>Optionen:</td>\n";
echo "</tr>\n";
$obj->cols = array("faq_cat_title", "faq_cat_description");
$tree = $obj->displayItems();
echo render_tree($tree);
/*
while ($data = dbarray($tree)) {
    echo "<tr>\n";
    echo "<td class='tbl1'>".$data['level']."</td>\n";
    echo "<td class='tbl1'>".$data['faq_cat_title']."</td>\n";
    echo "<td class='tbl1'>";
    echo "<a href='".FUSION_SELF.$aidlink."&amp;edit=".$data['faq_cat_id']."' title='Editieren'>Editieren</a>";
    echo " <a href='".FUSION_SELF.$aidlink."&amp;del=".$data['faq_cat_id']."&amp;lft=".$data['faq_cat_lft']."&amp;rgt=".$data['faq_cat_rgt']."' title='L&ouml;schen'>L&ouml;schen</a>";
    echo "</td>\n";
    echo "</tr>\n";
}*/
echo "</table>\n";
closetable();

function render_tree($tree) {
	global $aidlink;
	$rows = dbrows($tree);
	$array = array();
	$i = 0;
	while ($data = dbarray($tree)) {
		if ($i == 0) {
			$array[] = array("id"=>$data['faq_cat_id'], "title"=>$data['faq_cat_title'], "down"=>"", "up"=>"", "level"=>$data['level'], "lft"=>$data['faq_cat_lft'], "rgt"=>$data['faq_cat_rgt']);
		} else {
			$j = $i-1; $array[$j]['down'] = $data['faq_cat_id'];
			$array[] = array("id"=>$data['faq_cat_id'], "title"=>$data['faq_cat_title'], "down"=>"", "up"=>$array[$j]["id"], "level"=>$data['level'], "lft"=>$data['faq_cat_lft'], "rgt"=>$data['faq_cat_rgt']);
		}
		$i++;
	}
	$dummy ="";
	for ($i=0; $i<count($array);$i++) {
		$dummy .= "<tr>\n";
		$dummy .= "<td class='tbl1'>".$array[$i]["level"]."</td>\n";
		$dummy .= "<td class='tbl1'>".$array[$i]["title"]."</td>\n";
		$dummy .= "<td class='tbl1'>";
		$dummy .= "<a href='".FUSION_SELF.$aidlink."&amp;edit=".$array[$i]["id"]."' title='Editieren'>Editieren</a>";
		$dummy .= " <a href='".FUSION_SELF.$aidlink."&amp;del=".$array[$i]["id"]."&amp;lft=".$array[$i]["lft"]."&amp;rgt=".$array[$i]["rgt"]."' title='L&ouml;schen'>L&ouml;schen</a>";
		$dummy .= " <a href='".FUSION_SELF.$aidlink."&amp;move=".$array[$i]["id"]."&amp;up=".$array[$i]["up"]."' title='Up'>Hoch</a>";
		$dummy .= " <a href='".FUSION_SELF.$aidlink."&amp;move=".$array[$i]["id"]."&amp;down=".$array[$i]["down"]."' title='Down'>Runter</a>";
		$dummy .= "</tr>\n";
	}
	return $dummy;
}
require_once THEMES."templates/footer.php";
?>
