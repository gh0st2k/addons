<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(FUSION_SELF); }

//$screens = dbcount("(dl_screen_id)", DL_SCREEN, "dl_id='".$_GET['dl_id']."'");
//if ($screens < 1) { redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&back&step=3"); }

if (isset($_GET['thread'])) {
    if ($_GET['thread'] == "ok") {
        echo "<div id='close-message'><div class='admin-message'>Der Thread wurde zugeordnet.</div></div>\n";
    } else {
        echo "<div id='close-message'><div class='admin-message'>Der Thread wurde erstellt.</div></div>\n";
    }
}
if (isset($_GET['back'])) {
    echo "<div id='close-message'><div class='admin-message'>Bitte erst eine Auswahl treffen und speichern.</div></div>\n";
}
if (isset($_GET['delete'])) {
    $result = dbquery("UPDATE ".DL." SET dl_thread='0' WHERE dl_id='".$_GET['dl_id']."'");
    echo "<div id='close-message'><div class='admin-message'>Der Thread wurde entfernt.</div></div>\n";
}

if (isset($_POST['save']) && isset($_POST['thread_id'])) {
    if ($_POST['thread_id'] != "-1" && isnum($_POST['thread_id'])) {
        if ($_POST['thread_id'] > 0) {
            $result = dbquery("UPDATE ".DL." SET dl_thread='".$_POST['thread_id']."' WHERE dl_id='".$_GET['dl_id']."'");
            redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=4");
        } else {
            if (dl_access("S") || dl_access("F") || dl_access("R")) {
				CreateSupportThread($_GET['dl_id']);
				redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=4");
            } else {
                $result = dbquery("UPDATE ".DL." SET dl_thread='-1' WHERE dl_id='".$_GET['dl_id']."'");
                redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=4");
            }
            /*
            $data = dbarray(dbquery("SELECT dl_title FROM ".DL." WHERE dl_id='".$_GET['dl_id']."'"));
            $count = dbcount("(thread_id)", DB_THREADS, "thread_subject='".$data['dl_title']."'");
            if ($count > 1) { $data['dl_title'] = $data['dl_title']." Support"; }
            $result = dbquery("INSERT INTO ".DB_THREADS." (forum_id, thread_subject, thread_author, thread_views, thread_lastpost, thread_lastpostid, thread_lastuser, thread_postcount, thread_poll, thread_sticky, thread_locked) VALUES('".$dl_settings['forum_id']."', '".$data['dl_title']."', '".$userdata['user_id']."', '0', '".time()."', '0', '".$userdata['user_id']."', '1', '0', '0', '0')");
            $thread_id = mysql_insert_id();
            $message = "Automatischer Supportthread f&uuml;r den folgenden Download: <a href=\'".DL_DIR."dl_details.php?dl_id=".$_GET['dl_id']."\'>".$data['dl_title']."</a>.";
            $result = dbquery("INSERT INTO ".DB_POSTS." (forum_id, thread_id, post_message, post_showsig, post_smileys, post_author, post_datestamp, post_ip, post_edituser, post_edittime) VALUES ('".$dl_settings['forum_id']."', '".$thread_id."', '".$message."', '0', '0', '".$userdata['user_id']."', '".time()."', '".USER_IP."', '0', '0')");
            $post_id = mysql_insert_id();
            $result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".time()."', forum_postcount=forum_postcount+1, forum_threadcount=forum_threadcount+1, forum_lastuser='".$userdata['user_id']."' WHERE forum_id='".$dl_settings['forum_id']."'");
            $result = dbquery("UPDATE ".DB_THREADS." SET thread_lastpostid='".$post_id."' WHERE thread_id='".$thread_id."'");
            $result = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts+1 WHERE user_id='".$userdata['user_id']."'");
            $result = dbquery("UPDATE ".DL." SET dl_thread='".$thread_id."' WHERE dl_id='".$_GET['dl_id']."'"); */
            
        }
    } else {
        redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=4");
    }
}

opentable("Supportthread");
$check = dbarray(dbquery("SELECT dl_thread FROM ".DL." WHERE dl_id='".$_GET['dl_id']."'"));
if ($check['dl_thread'] == 0) {
    echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=3' method='post'>";
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
    echo "<tr>\n";
    echo "<td class='tbl1'>Supportthread ausw&auml;hlen:</td>\n";
    echo "<td class='tbl1'><select name='thread_id' size='1' class='textbox'>";
    echo "<option value='0'>Einen neuen Thread erstellen</option>\n";
    echo "<option value='-1'>Keinen Thread erstellen</option>\n";
    echo "</select></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td class='tbl1'></td>\n";
    echo "<td class='tbl1'><input type='submit' name='save' value=' Speichern und weiter' class='button' /></td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</form>";
} else {
    $data = dbarray(dbquery("SELECT thread_subject FROM ".DB_THREADS." WHERE thread_id='".$check['dl_thread']."'"));
    echo "<center>Aktueller Supportthread: ".$data['thread_subject']." <a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=3&delete'>L&ouml;schen</a></center>";
}
closetable();

?>
