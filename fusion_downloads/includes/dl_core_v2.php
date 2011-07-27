<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }
include INFUSIONS."fusion_downloads/infusion_db.php";

if (!defined("DL_DIR_ADMIN")) {
	define("DL_DIR_ADMIN", INFUSIONS."fusion_downloads/admin/");
}
if (!defined("DL_DIR")) {
	define("DL_DIR", INFUSIONS."fusion_downloads/");
}
if (!defined("DL_FILES")) {
    define("DL_FILES", DL_DIR."upload/files/");
}
if (!defined("DL_IMAGES")) {
    define("DL_IMAGES", DL_DIR."upload/screenshots/");
}

if (!isset($aidlink)) { $aidlink = ""; }
$dlSettings = dbarray(dbquery("SELECT * FROM ".DL_SETTINGS));

function displayCatOptions

if (!function_exists("dl_cat_options")) {
    function dl_cat_options($id = "", $style = "", $all = FALSE) {
        global $dl_settings;
        if ($dl_settings['cat_sort'] == 0) { $sort = "dl_cat_order"; } else { $sort = "dl_cat_name"; }
        $result = dbquery("SELECT dl_cat_id, dl_cat_name FROM ".DL_CAT." ORDER BY ".$sort." ASC");
        $dummy = "<select name='dl_cat' size='1' class='textbox' ".$style.">\n";
        if ($all) {
            $dummy .= "<option value=''>Alle</option>\n";
        }
        while ($data = dbarray($result)) {
            if ($data['dl_cat_id'] == $id) { $sel = "selected='selected'"; } else { $sel = ""; }
            $dummy .= "<option value='".$data['dl_cat_id']."' ".$sel.">".$data['dl_cat_name']."</option>\n";
        }
        $dummy .= "</select>\n";
        return $dummy;
    }
}
if (!function_exists("dl_fusion_options")) {
    function dl_fusion_options($id = "", $style = "", $all = FALSE) {
        $result = dbquery("SELECT dl_fusion_id, dl_fusion_name FROM ".DL_FUSION." ORDER BY dl_fusion_order ASC");
        $dummy = "<select name='dl_fusion' size='1' class='textbox' ".$style.">\n";
        if ($all) {
            $dummy .= "<option value=''>Alle</option>\n";
        }
        while ($data = dbarray($result)) {
            if ($data['dl_fusion_id'] == $id) { $sel = "selected='selected'"; } else { $sel = ""; }
            $dummy .= "<option value='".$data['dl_fusion_id']."' ".$sel.">".$data['dl_fusion_name']."</option>\n";
        }
        $dummy .= "</select>\n";
        return $dummy;
    }
}

if (!function_exists("dl_fusion_options_new")) {
    function dl_fusion_options_new($id = array()) {
        $result = dbquery("SELECT dl_fusion_id, dl_fusion_name FROM ".DL_FUSION." ORDER BY dl_fusion_order ASC");
        $dummy = "";
        while ($data = dbarray($result)) {
            if ((is_array($id) && in_array($data['dl_fusion_id'], $id)) || $data['dl_fusion_id'] == $id) { $sel = "checked='checked'"; } else { $sel = ""; }
            $dummy .= "<input type='checkbox' name='dl_fusion[]' value='".$data['dl_fusion_id']."' ".$sel." /> ".$data['dl_fusion_name']." ";
        }
        return $dummy;
    }
}

if (!function_exists("dl_create_options")) {
    function dl_create_options($array, $get) {
        $dummy = "";
        while (list($key, $val) = each($array)) {
            $dummy .= "<option value='".$key."'";
            if ($get == $key) {
                $dummy .= " selected='selected'";
            }
            $dummy .= ">".$val."</option>\n";
        }
        return $dummy;
    }
}

if (!function_exists("dl_sort_options")) {
    function dl_sort_options($id = "", $style = "", $order_by = "") {
        $sort = array("dl_title"=>"Name", "dl_author"=>"Autor", "dl_timestamp"=>"Datum");
        $dummy = "<select name='dl_sort' size='1' class='textbox' ".$style.">\n";
        $dummy .= dl_create_options($sort, $id);
        $dummy .= "</select>\n";
        $dummy .= " <select name='order_by' size='1' class='textbox' ".$style.">\n";
        $dummy .= "<option value='ASC' ".($order_by == "ASC" ? "selected='selected'" : "").">Aufsteigend</option>\n<option value='DESC' ".($order_by == "DESC" ? "selected='selected'" : "").">Absteigend</option>\n";
        $dummy .= "</select>\n";
        return $dummy;
    }
}

if (!function_exists("dl_item_options")) {
    function dl_items_options($id = "", $style = "") {
        $items = array("20"=>"20", "50"=>"50", "all"=>"Alle");
        $dummy = "<select name='items' size='1' class='textbox' ".$style.">\n";
        $dummy .= dl_create_options($items, $id);
        $dummy .= "</select>\n";
        return $dummy;
    }
}

if (!function_exists("dl_reorder")) {
    function dl_reorder($db, $db_id, $db_item, $time = 0) {
        if ($time != "0") { $order_by = ", ".$time; } else { $order_by = ""; }
        $result = dbquery("SELECT ".$db_id." FROM ".$db." ORDER BY ".$db_item." ASC".$order_by);
        $i = 1;
        while ($data = dbarray($result)) {
            $query = dbquery("UPDATE ".$db." SET ".$db_item."='".$i."' WHERE ".$db_id."='".$data[$db_id]."'");
            $i++;
        }
    }
}
if (!function_exists("dl_status")) {
    function dl_status($id) {
        switch ($id) {
            case 0: return "In Bearbeitung"; break;
            case 1: return "In Pr&uuml;fung"; break;
            case 2: return "Online"; break;
            case 3: return "Abgelehnt"; break;
            case 4: return "Deaktiviert"; break;
        }
    }
}

if (!function_exists("dl_status_link")) {
    function dl_status_link($status_id, $tooltip = "", $url = "") {
        $output = "";
        $status = dl_status($status_id);
        $link = ($url != "" ? $url : "");
        if ($tooltip != "") {
            $output .= "<a class='tooltip' title='".$tooltip."' href='".$url."'>";
            $output .= $status;
            $output .="</a>\n";
        } else {
            $output .= $status;
        }
        return $output;
    }
}

if (!function_exists("dl_status_options")) {
    function dl_status_options($id) {
        $items = array("In Bearbeitung", "In Pr&uuml;fung", "Online", "Abgelehnt", "Deaktiviert");
        $dummy = dl_create_options($items, $id);
        return $dummy;
    }
}

if (!function_exists("dl_error_type")) {
    function dl_error_type($id) {
        switch ($id) {
            case 1: return "Download defekt";
            case 2: return "Sicherheitsl&uuml;cke";
            case 3: return "Sonstiges";
            case 4: return "Link defekt";
        }
    }
}

if (!function_exists("dl_access")) {
    function dl_access($right = "") {
        global $userdata;
        if (iMEMBER) {
            if ($right != "") {
                if (iSUPERADMIN || (iADMIN && checkrights("I"))) { return TRUE; }
                $data = dbarray(dbquery("SELECT dl_access_rights FROM ".DL_ACCESS." WHERE dl_access_user='".$userdata['user_id']."' LIMIT 1"));
                if (in_array($right, explode(".",$data['dl_access_rights']))) { return TRUE; }
                else { return FALSE; }
            } else {
                $dbcount = dbcount("(dl_access_user)", DL_ACCESS, "dl_access_user='".$userdata['user_id']."'");
                if ($dbcount > 0) { return TRUE; } else { return FALSE; }
            }
        } else { return FALSE; }
    }
}

if (!function_exists("CreateSupportThread")) {
	function CreateSupportThread($id) {
		global $dl_settings, $settings;
		$data = dbarray(dbquery("SELECT aa.dl_title, aa.dl_description, aa.dl_user_id, ab.dl_screen_thumb
									FROM ".DL." aa
									LEFT JOIN ".DL_SCREEN." ab ON aa.dl_id=ab.dl_id
									WHERE aa.dl_id='".$id."'
									ORDER BY ab.dl_screen_order ASC LIMIT 1
									"));
		$count = dbcount("(thread_id)", DB_THREADS, "thread_subject='".$data['dl_title']."'");
		if ($count > 1) { $data['dl_title'] = $data['dl_title']." Support"; }
		$result = dbquery("INSERT INTO ".DB_THREADS." (forum_id, thread_subject, thread_author, thread_views, thread_lastpost, thread_lastpostid, thread_lastuser, thread_postcount, thread_poll, thread_sticky, thread_locked) VALUES('".$dl_settings['forum_id']."', '".$data['dl_title']."', '".$data['dl_user_id']."', '0', '".time()."', '0', '".$data['dl_user_id']."', '1', '0', '0', '0')");
		$thread_id = mysql_insert_id();
		$message = "";
		if ($data['dl_screen_thumb'] != "") {
$message .= "
[img]".$settings['siteurl']."infusions/fusion_downloads/upload/screenshots/".$data['dl_screen_thumb']."[/img]

";
		}
		$message .= $data['dl_description']."

[b]Zum Download:[/b] [addon=".$id."]".$data['dl_title']."[/addon]";

		$result = dbquery("INSERT INTO ".DB_POSTS." (forum_id, thread_id, post_message, post_showsig, post_smileys, post_author, post_datestamp, post_ip, post_edituser, post_edittime) VALUES ('".$dl_settings['forum_id']."', '".$thread_id."', '".$message."', '0', '0', '".$data['dl_user_id']."', '".time()."', '".USER_IP."', '0', '0')");
		$post_id = mysql_insert_id();
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".time()."', forum_postcount=forum_postcount+1, forum_threadcount=forum_threadcount+1, forum_lastuser='".$data['dl_user_id']."' WHERE forum_id='".$dl_settings['forum_id']."'");
		$result = dbquery("UPDATE ".DB_THREADS." SET thread_lastpostid='".$post_id."' WHERE thread_id='".$thread_id."'");
		$result = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts+1 WHERE user_id='".$data['dl_user_id']."'");
		$result = dbquery("UPDATE ".DL." SET dl_thread='".$thread_id."' WHERE dl_id='".$id."'");
	}
}

if (!function_exists("DeleteSupportThread")) {
	function DeleteSupportThread($id) {
		global $dl_settings;
		$result = dbquery("SELECT post_author, COUNT(post_id) as num_posts FROM ".DB_POSTS." WHERE thread_id='".$id."' GROUP BY post_author");
		if (dbrows($result)) {
			while ($pdata = dbarray($result)) {
				$result2 = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts-".$pdata['num_posts']." WHERE user_id='".$pdata['post_author']."'");
			}
		}

		$tdata = dbarray(dbquery("SELECT forum_id, thread_id, thread_lastpost, thread_lastuser FROM ".DB_THREADS." WHERE thread_id='".$id."'"));

		$threads_count = dbcount("(forum_id)", DB_THREADS, "forum_id='".$tdata['forum_id']."'") - 1;
		$result = dbquery("DELETE FROM ".DB_POSTS." WHERE thread_id='".$id."'");
		$del_posts = mysql_affected_rows();
		$result = dbquery("DELETE FROM ".DB_THREADS." WHERE thread_id='".$id."'");
		$result = dbquery("DELETE FROM ".DB_THREAD_NOTIFY." WHERE thread_id='".$id."'");
		$result = dbquery("SELECT attach_name FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id='".$id."'");
		if (dbrows($result) != 0) {
			while ($attach = dbarray($result)) {
				unlink(FORUM."attachments/".$attach['attach_name']);
			}
		}
		$result = dbquery("DELETE FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id='".$id."'");

		if ($threads_count > 0) {
			$result = dbquery("SELECT forum_id FROM ".DB_FORUMS." WHERE forum_id='".$tdata['forum_id']."' AND forum_lastpost='".$tdata['thread_lastpost']."' AND forum_lastuser='".$tdata['thread_lastuser']."'");
			if (dbrows($result)) {
				$result = dbquery(
					"SELECT p.forum_id, p.post_author, p.post_datestamp FROM ".DB_POSTS." p
					INNER JOIN ".DB_THREADS." t ON p.thread_id=t.thread_id
					WHERE p.forum_id='".$tdata['forum_id']."' AND p.post_hidden='0'
					ORDER BY p.post_datestamp DESC LIMIT 1"
				);
				$pdata = dbarray($result);
				$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".$pdata['post_datestamp']."', forum_postcount=forum_postcount-".$del_posts.", forum_threadcount=forum_threadcount-1, forum_lastuser='".$pdata['post_author']."' WHERE forum_id='".$tdata['forum_id']."'");
			}
		} else {
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='0', forum_postcount=0, forum_threadcount=0, forum_lastuser='0' WHERE forum_id='".$tdata['forum_id']."'");
		}
	}
}
?>