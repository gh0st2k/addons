<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";

if (dl_access("S") || dl_access("F") || dl_access("R")) {
	$getCheck = array(
		"dl_status" =>  "int",
		"dl_version" => "int",
		"dl_title" => "string",
		"dl_author" => "string",
		"dl_user_id" => "int",
		"dl_count" => "int",
		"order" => "string",
		"orderBy" => "string",
		"limit" => "int"
	);
	$getData = array(
		"dl_status" =>  "",
		"dl_version" => "",
		"dl_title" => "",
		"dl_author" => "",
		"dl_user_id" => "",
		"dl_count" => "",
		"order" => "",
		"orderBy" => "",
		"limit" => "20"
	);
	$orderArray = array("dl_title", "dl_author", "dl_count", "dl_timestamp");

    if (isset($_GET['dl_option']) && isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
        switch ($_GET['dl_option']) {
            case 1: redirect(DL_DIR."dl_details.php?dl_id=".$_GET['dl_id']); break;
            case 2: redirect(DL_DIR."dl_submit.php?step=1&dl_id=".$_GET['dl_id']); break;
            case 3: redirect(FUSION_SELF."?del=".$_GET['dl_id']); break;
            case 4: redirect(DL_DIR_ADMIN."dl_status.php?dl_id=".$_GET['dl_id']); break;
            case 5: redirect(DL_DIR_ADMIN."dl_livedemo.php?dl_id=".$_GET['dl_id']); break;
			case 6: $result = dbquery("UPDATE ".DL." SET dl_folder='' WHERE dl_id='".$_GET['dl_id']."'"); redirect(FUSION_SELF); break;
        }
    }

	if (isset($_GET['del']) && isnum($_GET['del'])) {
			$data = dbarray(dbquery("SELECT dl_thread FROM ".DL." WHERE dl_id='".$_GET['del']."'"));
		if ($data['dl_thread'] > 0) {
			DeleteSupportThread($data['dl_thread']);
		}
        $result = dbquery("DELETE FROM ".DL." WHERE dl_id='".$_GET['del']."'");
        $result = dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_item_id='".$_GET['del']."' AND comment_type='M'");
        $result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_item_id='".$_GET['del']."' AND rating_type='M'");
        echo "<div id='close-message'><div class='admin-message'>Der Download wurde gel&ouml;scht.</div></div>\n";
    } elseif (isset($_GET['deak']) && isnum($_GET['deak'])) {
        $result = dbquery("UPDATE ".DL." SET dl_status='5' WHERE dl_id='".$_GET['deak']."'");
    } elseif (isset($_GET['akt']) && isnum($_GET['akt'])) {
        $result = dbquery("UPDATE ".DL." SET dl_status='2' WHERE dl_id='".$_GET['akt']."'");
    }

	$where = "";
	$order = "";

	if (isset($_GET)) {
		foreach ($_GET as $var => $value) {
			if (!isset($getCheck[$var])) { continue; }
			if ($getCheck[$var] == "int" && !isnum($value)) { continue; }
			else {
				if (isset($getData[$var])) {
					$getData[$var] = stripinput($value);
					if ($var == "order" && ($value == "ASC" || $value == "DESC")) {
						$order = $value;
					} else {
						$order = "DESC";
					}
					if ($var == "orderBy" && in_array($value, $orderArray)) {
						$order = $value." ".$order;
					} else {
						$order = "dl_timestamp ".$order;
					}
					$getString = ($getString == "" ? "?" : $getString."&amp;");
					$getString .= $var."='".$value."'";
				}
			}
		}
	}
	$likeArray = array("dl_author");
	foreach ($getData AS $key => $value) {
		if ($key != "limit" && $key != "order" && $key != "orderBy" && $value != "") {
			$where .= ($where != "" ? " AND " : "WHERE ");
			if (in_array($key, $likeArray)) {
				$where .= $key." LIKE '%".$value."%'";
			} else {
				$where .= $key."='".$value."'";
			}
		}
	}
	if ($order != "") { $order = "ORDER BY ".$order; }

    opentable("Downloads");
    admin_navigation();

	echo "<form action='".FUSION_SELF."' method='get'>\n";
	echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border' style='margin: 20px auto'>\n";
	echo "<tr>\n";
	echo "<td class='tbl' width='195px'>Author:</td>\n";
	echo "<td class='tbl' width='205px'><input type='text' name='dl_author' value='".$getData['dl_author']."' style='width:200px;' /></td>\n";
	echo "<td class='tbl' width='195px'>Einsender:</td>\n";
	echo "<td class='tbl' width='205px'><input type='text' name='dl_user_id' value='".$getData['dl_user_id']."' style='width:200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td class='tbl'>Status:</td>\n";
	echo "<td class='tbl'><select name='dl_status' size='1' class='textbox' style='width:200px;'>";
	echo "<option value=''>Egal</option>\n";
	echo dl_status_options($getData['dl_status'])."</select>\n</td>\n";
	echo "<td class='tbl'>pro Seite:</td>\n";
	echo "<td class='tbl'><input type='text' name='limit' value='".$getData['limit']."' style='width:200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td class='tbl'>Orndnen nach:</td>\n";
	echo "<td class='tbl'>";
	echo "<select name='orderBy' size='1' style='width:200px;'>";
	foreach ($orderArray AS $value) {
		echo "<option ".($getData['orderBy'] == $value ? "selected='selected'" : "").">".$value."</option>\n";
	}
	echo "</select>\n</td>\n";
	echo "<td class='tbl' colspan='2'><select name='order' size='1' style='width:200px;'>";
	echo "<option value='DESC' ".($getData['order'] == "DESC" ? "selected='selected'" : "").">Absteigend</option>\n";
	echo "<option value='ASC' ".($getData['order'] == "ASC" ? "selected='selected'" : "").">Ansteigend</option>\n";
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td class='tbl' colspan='4'><center><input type='submit' value=' Absenden' />\n</center></td>\n";
	echo "</tr>\n";
	echo "</table>\n</form>\n";


	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

	echo $where."<br />";
	echo $order."<br />";
    $result = dbquery(" SELECT SQL_CALC_FOUND_ROWS
    					aa.dl_id, aa.dl_title, aa.dl_user_id, aa.dl_status, cc.dl_cat_name, ac.user_name, ac.user_status FROM ".DL." aa
                        LEFT JOIN ".DL_CAT." cc ON cc.dl_cat_id=aa.dl_cat
                        LEFT JOIN ".DB_USERS." ac ON aa.dl_user_id=ac.user_id ".$where." ".$order."
                        LIMIT ".$_GET['rowstart'].", ".$getData['limit']);
	$rows = dbresult(dbquery("SELECT FOUND_ROWS()"), 0);
    if (dbrows($result)) {
        echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='700px' align='center'>\n";
        echo "<tr>\n";
        echo "<td class='tbl2'>Titel</td>\n";
        echo "<td class='tbl2'>User</td>\n";
        echo "<td class='tbl2'>Status</td>\n";
        echo "<td class='tbl2'>Optionen</td>\n";
        echo "</tr>\n";
        while ($data = dbarray($result)) {
            echo "<tr>\n";
            echo "<td class='tbl1'>".trimlink($data['dl_title'],30)."</td>\n";
            echo "<td class='tbl1'>".profile_link($data['dl_user_id'], $data['user_name'], $data['user_status'])."</td>\n";
            echo "<td class='tbl1'>".dl_status($data['dl_status'])."</td>\n";
            echo "<td class='tbl1'>";
            echo "<form action='".FUSION_SELF."' method='get'>";
            echo "<select name='dl_option' class='textbox' size='1' onchange='submit()' style='width:160px;'>\n";
            echo "<option value='-'>---</option>\n";
            echo "<option value='1'>Ansehen</option>\n";
            if (dl_access("B")) { echo "<option value='2'>Bearbeiten</option>\n"; }
            if (dl_access("L")) { echo "<option value='3'>L&ouml;schen</option>\n"; }
            if (dl_access("B")) { echo "<option value='4'>Status&auml;nderung</option>\n"; }
            if (dl_access("B") && $data['dl_cat_name'] == "Themes") { echo "<option value='5'>Livedemo hinzuf&uuml;gen</option>\n"; }
			if (dl_access("B") && $data['dl_folder'] != "" && $data['dl_cat_name'] == "Themes") { echo "<option value='6'>Livedemo entfernen</option>\n"; }
            echo "</select>\n";
            echo "<input type='hidden' name='dl_id' value='".$data['dl_id']."'>\n";
            echo "</form>\n";
            echo "</td>\n</tr>\n";
        }
        echo "</table>\n";
		echo makepagenav($_GET['rowstart'], $limit, $rows , "3", FUSION_SELF.$getString);
    } else {
        echo "<center>Es sind keine Downloads ".(isset($_GET['dl_status']) && isnum($_GET['dl_status']) ? "mit Status \"".dl_status($_GET['dl_status']) : "")."\" vorhanden.</center>\n";
    }
    closetable();
} else {
    redirect(DL_DIR_ADMIN."index.php?no_access");
}

require_once THEMES."templates/footer.php";
?>
