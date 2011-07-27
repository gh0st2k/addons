<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";

if (dl_access("S") || dl_access("F") || dl_access("R")) {
    if (isset($_GET['dl_status']) && ($_GET['dl_status'] == 3 || $_GET['dl_status'] == 4) && isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
        include INCLUDES."infusions_include.php";
        if (isset($_POST['reason2'])) {
            if ($_GET['dl_status'] == 3) {
                $title = dbarray(dbquery("SELECT dl_title FROM ".DL." WHERE dl_id='".$_GET['dl_id']));
                $subject = "Dein Download ".$title['dl_title']." wurde abgelehnt.";
                if (isset($_POST['reason']) && isnum($_POST['reason'])) {
                    switch ($_POST['reason']) {
                        case 1: $reason = "Richtlinien nicht erf&uuml;llt"; break;
                        case 0: $reason = "Sonstiges (siehe Text)"; break;
                    }
                    if ($_POST['reason'] == 0 && $_POST['reason2'] == "") {
                        redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;dl_status=3&amp;no_reason");
                    }
                } else {
                    redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;dl_status=3&amp;no_reason");
                }
                $reason2 = ($_POST['reason2'] != "" ? "Folgender Erg&auml;nzung wurde angegeben: ".trim(stripinput($_POST['reason2'])) : "");
                $message = "
                Folgender Grund f&uuml;hrte zur Ablehnung:
                ".$reason."
               
                ".$reason2;
            } else {

            }
            $sendpm = send_pm($to, $from, $subject, $message);
            redirect(FUSION_SELF."?pn=ok");
        }
        if ($_GET['dl_status'] == 3) {
            echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;dl_status=".$_GET['dl_status']."' method='post'>\n";
            echo "<table cellpadding='0' cellspacing='0' width='600px' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td class='tbl' width='200px'>Grund:</td>\n";
            echo "<td class='tbl' width='400px'>";
            echo "<input type='radio' name='reason' value='0'> Richtlinien nicht erf&uuml;llt.";
            echo "</td>\n";
            echo "<td class='tbl'>Zus&auml;tzlicher Text:</td>\n";
            echo "<td class='tbl'><textarea name='reason2' cols='90' rows='5' style='width:400px;' class='textbox'></textarea></td>\n";
            echo "</tr>\n<tr>\n";
            echo "<td class='tbl'></td>\n";
            echo "<td class='tbl'><input type='submit' value=' Absenden' class='button' /></td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            echo "</form>\n";
        } else {
            echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;dl_status=".$_GET['dl_status']."' method='post'>\n";
            echo "<table cellpadding='0' cellspacing='0' width='600px' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td class='tbl' width='200px'>Grund:</td>\n";
            echo "<td class='tbl'><textarea name='reason2' cols='90' rows='5' style='width:400px;' class='textbox'></textarea></td>\n";    
            echo "</tr>\n<tr>\n";
            echo "<td class='tbl'></td>\n";
            echo "<td class='tbl'><input type='submit' value=' Absenden' class='button' /></td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            echo "</form>\n";
        }
    } else {
        opentable("Nicht bearbeitete Einsendungen");
        admin_navigation();
        if (isset($_GET['del']) && isnum($_GET['del']) && isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
            $result = dbquery("DELETE FROM ".DL." WHERE dl_id='".$_GET['dl_id']."'");
            $result = dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_item_id='".$_GET['dl_id']."' AND comment_type='M'");
            $result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_item_id='".$_GET['dl_id']."' AND rating_type='M'");
            echo "<div id='close-message'><div class='admin-message'>Der Download wurde gel&ouml;scht.</div></div>\n";
        }

        if (isset($_GET['dl_status']) && $_GET['dl_status'] == 2 && isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
            $result = dbquery("UPDATE ".DL." SET dl_status='2' WHERE dl_id='".$_GET['dl_id']."'");
            if ($_GET['dl_status'] == 2) {
                $result = dbquery("SELECT dl_thread FROM ".DL." WHERE dl_id='".$_GET['dl_id']."'");
                $thread_id = dbarray($result);
                $result = dbquery("UPDATE ".DL." SET dl_status='1' WHERE dl_id='".$_GET['dl_id']."'");
                if ($thread_id['dl_thread'] == "-1") {
                    $data = dbarray(dbquery("SELECT aa.dl_title, aa.dl_description, ab.dl_screen_thumb
                                                FROM ".DL." aa
                                                LEFT JOIN ".DL_SCREEN." ab ON aa.dl_id=ab.dl_id
                                                WHERE aa.dl_id='".$_GET['dl_id']."' AND ab.dl_screen_order='1'

                                                "));
                    $count = dbcount("(thread_id)", DB_THREADS, "thread_subject='".$data['dl_title']."'");
                    if ($count > 1) { $data['dl_title'] = $data['dl_title']." Support"; }
                    $result = dbquery("INSERT INTO ".DB_THREADS." (forum_id, thread_subject, thread_author, thread_views, thread_lastpost, thread_lastpostid, thread_lastuser, thread_postcount, thread_poll, thread_sticky, thread_locked) VALUES('".$dl_settings['forum_id']."', '".$data['dl_title']."', '".$userdata['user_id']."', '0', '".time()."', '0', '".$userdata['user_id']."', '1', '0', '0', '0')");
                    $thread_id = mysql_insert_id();
                    $message = "Automatischer Supportthread f&uuml;r den folgenden Download: [b]".$data['dl_title']."[/b]

                                [img]".$settings['siteurl']."infusions/fusion_downloads/upload/screenshots/".$data['dl_screen_thumb']."[/IMG]

                                [b]Beschreibung:[/b]
                                ".$data['dl_description']."

                                [b]Zum Download:[/b] [addon=".$_GET['dl_id']."]".$data['dl_title']."[/addon]";

                    $result = dbquery("INSERT INTO ".DB_POSTS." (forum_id, thread_id, post_message, post_showsig, post_smileys, post_author, post_datestamp, post_ip, post_edituser, post_edittime) VALUES ('".$dl_settings['forum_id']."', '".$thread_id."', '".$message."', '0', '0', '".$userdata['user_id']."', '".time()."', '".USER_IP."', '0', '0')");
                    $post_id = mysql_insert_id();
                    $result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".time()."', forum_postcount=forum_postcount+1, forum_threadcount=forum_threadcount+1, forum_lastuser='".$userdata['user_id']."' WHERE forum_id='".$dl_settings['forum_id']."'");
                    $result = dbquery("UPDATE ".DB_THREADS." SET thread_lastpostid='".$post_id."' WHERE thread_id='".$thread_id."'");
                    $result = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts+1 WHERE user_id='".$userdata['user_id']."'");
                    $result = dbquery("UPDATE ".DL." SET dl_thread='".$thread_id."' WHERE dl_id='".$_GET['dl_id']."'");
                }
                $msg .= "<div id='close-message'><div class='admin-message'>Der Download ist nun online, der Supportthread wurde erstellt.</div>\n";
            }
            $msg .= "</div>\n";
            echo $msg;
        }

        $result = dbquery(" SELECT aa.dl_id, aa.dl_title, aa.dl_version, aa.dl_file, aa.dl_user_id, ac.user_name
                            FROM ".DL." aa
                            LEFT JOIN ".DB_USERS." ac ON aa.dl_user_id=ac.user_id
                            WHERE ab.dl_version_status='0'");
        if (dbrows($result)) {
            echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='700px' align='center'>\n";
            echo "<tr>\n";
            echo "<td class='tbl2' style='width:20px'>ID</td>\n";
            echo "<td class='tbl2' style='width:170px'>Titel</td>\n";
            echo "<td class='tbl2' style='width:30px'>Version</td>\n";
            echo "<td class='tbl2' style='width:50px'>User</td>\n";
            echo "<td class='tbl2' style='width:220px'>Optionen</td>\n";
            echo "<td class='tbl2' style='width:210px'>Pr&uuml;fung</td>\n";
            echo "</tr>\n";
            $i = 0;
            while ($data = dbarray($result)) {
                $class = ($i % 2 ? "tbl2" : "tbl1");
                echo "<tr>\n";
                echo "<td class='".$class."'>".$data['dl_id']."</td>\n";
                echo "<td class='".$class."'>".trimlink($data['dl_title'], 20)."</td>\n";
                echo "<td class='".$class."'>".$data['dl_version']."</td>\n";
                echo "<td class='".$class."'>".$data['user_name']."</td>\n";
                echo "<td class='".$class."'>";
                echo "<a href='".DL_FILES.$data['dl_file']."'>Download</a>";
                if (dl_access("B")) { echo " <a href='".DL_DIR."dl_submit.php?step=1&dl_id=".$data['dl_id']."'>Bearbeiten</a>"; }
                if (dl_access("L")) { echo " <a href='".FUSION_SELF."?del=".$data['dl_version_id']."&dl_id=".$data['dl_id']."'>L&ouml;schen</a>"; }
                echo "</td>\n";
                echo "<td class='".$class."'>";
                echo "<form action='".FUSION_SELF."' method='get'>\n";
                echo "<input type='hidden' name='dl_id' value='".$data['dl_id']."' />";
                echo "<select name='dl_status' size='1' class='textbox'>\n";
                echo dl_status("1");
                echo "</select>\n";
                echo " <input type='submit' value=' &Auml;ndern' class='button' />";
                echo "</td>\n";
                echo "</tr>\n";
                $i++;
            }
            echo "</table>\n";
        } else {
            echo "<center>Es sind keine Einsendungen vorhanden.</center>\n";
        }
        closetable();
    } 
} else {
        redirect(DL_DIR_ADMIN."dl_index.php?no_access");
}
    


require_once THEMES."templates/footer.php";
?>
