<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once INCLUDES."comments_include.php";
require_once INCLUDES."ratings_include.php";
require_once INCLUDES."bbcode_include.php";

if (!iADMIN && $dl_settings['maintenance'] == 1) { redirect(DL_DIR."dl_maintenance.php"); }

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(DL_DIR."index.php"); }

$access = dbcount("(dl_id)", DL, "dl_id='".$_GET['dl_id']."' AND dl_status='2'");
if (iADMIN || ($access > 0) || dl_access("S") || dl_access("F") || dl_access("R")) {
    add_to_head("
    <script type='text/javascript' src='".DL_DIR."includes/fancybox/jquery.mousewheel-3.0.2.pack.js'></script>
    <script type='text/javascript' src='".DL_DIR."includes/fancybox/jquery.fancybox-1.3.1.js'></script>
    <link rel='stylesheet' href='".DL_DIR."includes/fancybox/jquery.fancybox-1.3.1.css' type='text/css' media='screen' />");
    add_to_head('
    <script type=\'text/javascript\'>
    $(document).ready(function() {

        /* This is basic - uses default settings */

        $("a#single_image").fancybox({
                    \'titleShow\'		: false
                });

        /* Using custom settings */

        $("a#inline").fancybox({
            \'hideOnContentClick\': true
        });

        /* Apply fancybox to multiple items */

        $("a.grouped_elements").fancybox({
            \'titleShow\'		: false,
            \'transitionIn\'	:	\'elastic\',
            \'transitionOut\'	:	\'elastic\',
            \'speedIn\'		:	600,
            \'speedOut\'		:	200,
            \'overlayShow\'	:	false
        });

    });
    </script>
    ');

    if (isset($_GET['msg_admin']) && isnum($_GET['msg_admin'])) {
        if (isset($_POST['dl_error_type']) && isnum($_POST['dl_error_type'])) {
            if (isset($_POST['dl_error_message']) && $_POST['dl_error_message'] != "") { $error_msg = stripinput($_POST['dl_error_message']); } else { $error = 1; }
            if (!isset($error)) {
                $result = dbquery("INSERT INTO ".DL_ERROR." SET dl_id='".$_GET['dl_id']."', dl_error_type='".$_POST['dl_error_type']."',
                                                                dl_error_user_id='".$userdata['user_id']."', dl_error_message='".$error_msg."',
                                                                dl_error_timestamp='".time()."'");
                redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&message=1");
            } else {
                redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&message=2");
            }
        } elseif (isset($_GET['msg_admin']) && $_GET['msg_admin'] == 4) {
            $result = dbquery("INSERT INTO ".DL_ERROR." SET dl_id='".$_GET['dl_id']."', dl_error_type='".$_GET['msg_admin']."',
                                                            dl_error_user_id='".$userdata['user_id']."', dl_error_message='',
                                                            dl_error_timestamp='".time()."'");
            redirect(FUSION_SELF."?dl_id=".$_GET['dl_id']."&message=1");
        }
        $name = "";
        switch ($_GET['msg_admin']) {
            case 1: $name = "Fehlerbeschreibung:"; break;
            case 2: $name = "Beschreibung der Sicherheitsl&uuml;cke:"; break;
            case 3: $name = "Deine Information an das Team:"; break;
        }
        opentable("Benachrichtung an das Team");
        echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=".$_GET['msg_admin']."' method='post'>\n";
        echo "<table width='500px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top'>\n";
        echo "<tr>\n";
        echo "<td class='tbl2'>".$name."</td>\n";
        echo "</tr><tr>\n";
        echo "<td class='tbl1'><textarea name='dl_error_message' cols='100' rows='8' class='textbox' style='width:500px'></textarea></td>\n";
        echo "</tr><tr>\n";
        echo "<td class='tbl1'><input type='hidden' name='dl_error_type' value='".$_GET['msg_admin']."' /><input type='submit' value=' Fehlerbericht absenden' class='button' /></td>\n";
        echo "</tr>\n</table>\n";
        echo "</form>\n";
        closetable();
    } else {
        if (!isset($_GET['version_id']) || !isnum($_GET['version_id'])) {
            $result = dbquery("SELECT aa.dl_fusion, aa.dl_title, aa.dl_version, aa.dl_file, aa.dl_description, aa.dl_changelog, aa.dl_copyright, aa.dl_licence, aa.dl_author, aa.dl_author_email, aa.dl_author_www, aa.dl_coauthor,
                                  aa.dl_timestamp, aa.dl_thread, aa.dl_user_id, aa.dl_count, aa.dl_folder, ac.dl_cat_name,
                                  ae.user_name, ae.user_status, af.dl_screen_file, af.dl_screen_thumb
                                  FROM ".DL." aa
                                  LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                  LEFT JOIN ".DB_USERS." ae ON aa.dl_user_id=ae.user_id
                                  LEFT JOIN ".DL_SCREEN." af ON aa.dl_id=af.dl_id
                                  WHERE aa.dl_id='".$_GET['dl_id']."'
                                  ORDER BY af.dl_screen_order
                                  LIMIT 1");
        } else {
            $result = dbquery("SELECT aa.dl_fusion, aa.dl_title, aa.dl_version, aa.dl_file, aa.dl_description, aa.dl_changelog, aa.dl_copyright, aa.dl_licence, aa.dl_author, aa.dl_author_email, aa.dl_author_www, aa.dl_coauthor,
                                  aa.dl_timestamp, aa.dl_thread, aa.dl_user_id, aa.dl_count, aa.dl_folder, ac.dl_cat_name,
                                  ae.user_name, ae.user_status, af.dl_screen_file, af.dl_screen_thumb
                                  FROM ".DL." aa
                                  LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                  LEFT JOIN ".DB_USERS." ae ON aa.dl_user_id=ae.user_id
                                  LEFT JOIN ".DL_SCREEN." af ON aa.dl_id=af.dl_id
                                  WHERE aa.dl_id='".$_GET['dl_id']."'
                                  ORDER BY af.dl_screen_order
                                  LIMIT 1");
        }

        if (dbrows($result)) {
            $data = dbarray($result);
            // Englisches Downloadsystem Copyright by Barspin
            if (isset($_GET['start_dl'])) {
                $result = dbquery("UPDATE ".DL." SET dl_count=(dl_count+1) WHERE dl_id='".$_GET['dl_id']."'");
                require_once INCLUDES."class.httpdownload.php";
                redirect(DL_FILES.$data['dl_file']);
                ob_end_clean();
                $object = new httpdownload;
                $object->set_byfile(DL_FILES.$data['dl_file']);
                $object->use_resume = true;
                $object->download();
                exit;
            }

            if ($data['dl_count'] == 0) {
                $download_count = "[0 Downloads]";
            } elseif ($data['dl_count'] == 1) {
                $download_count = "[1 Download]";
            } else {
                $download_count = "[".$data['dl_count']." Downloads]";
            }

            opentable("Downloaddetails: ".$data['dl_title']);
            echo "<table width='98%' cellpadding='0' cellspacing='0' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td width='20%' class='tbl1'>";
            $result3 = dbquery("SELECT dl_screen_file, dl_screen_thumb FROM ".DL_SCREEN." WHERE dl_id='".$_GET['dl_id']."'");
            $rows = dbrows($result3);
            if ($rows > 0) {
                if ($rows > 1) {
                    $i = 1;
                    while ($data3 = dbarray($result3)) {
                        if ($i == 1) {
                            echo "<a href='".DL_IMAGES.$data3['dl_screen_file']."' class='grouped_elements' rel='dl_".$_GET['dl_id']."'><img src='".DL_IMAGES.$data3['dl_screen_thumb']."' style='border:none;' /></a>\n ";
                        } else {
                            echo "<a href='".DL_IMAGES.$data3['dl_screen_file']."' class='grouped_elements' style='display:none;' rel='dl_".$_GET['dl_id']."'><img src='".DL_IMAGES.$data3['dl_screen_thumb']."' style='border:none;' /></a>\n ";
                        }
                        $i++;
                    }
                    unset($i);
                } else {
                    $data3 = dbarray($result3);
                    echo "<a href='".DL_IMAGES.$data3['dl_screen_file']."' id='single_image'><img src='".DL_IMAGES.$data3['dl_screen_thumb']."' style='border:none;' /></a>\n ";
                }
            }
            echo "</td>\n";
            echo "<td width='80%' style='background-color:#fff;'>";

            echo "<table width='100%' cellpadding='0' cellspacing='0' align='center' height='100%' style='margin:0;'>\n";
            echo "<tr>\n";
            echo "<td width='70%' style='background-color:#fff;'>\n";

            echo "<table width='100%' cellpadding='0' cellspacing='0' align='center' height='100%' style='margin:0;'>\n";
            echo "<tr>\n";
            echo "<td class='tbl1'><strong>Downloadtitel:</strong></td>\n";
            echo "<td class='tbl1'>".$data['dl_title']."</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'><strong>Version:</strong></td>\n";
            echo "<td class='tbl1'>".$data['dl_version']."</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'><strong>Kategorie:</strong></td>\n";
            echo "<td class='tbl1'>".$data['dl_cat_name']."</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'><strong>Fusion Version(en):</strong></td>\n";
            $dummy = explode(".", $data['dl_fusion']);
            $fusions = "";
            $i = 1;
            $fusion_query = dbquery("SELECT dl_fusion_id, dl_fusion_name FROM ".DL_FUSION." ORDER BY dl_fusion_order DESC");
            while ($data2 = dbarray($fusion_query)) {
                if (in_array($data2['dl_fusion_id'], $dummy)) {
                    if ($i > 1) { $fusions .= ", "; }
                    $fusions .= $data2['dl_fusion_name'];
                    $i++;
                }
            }
            echo "<td class='tbl1'>".$fusions."</td>\n";
            if ($data['user_name'] == $data['dl_author']) {
                echo "</tr><tr>\n";
                echo "<td class='tbl1'><strong>Author / Einsender:</strong></td>\n";
                echo "<td class='tbl1'>".profile_link($data['dl_user_id'], $data['user_name'], $data['user_status'])."</td>\n";
            } else {
                echo "</tr><tr>\n";
                echo "<td class='tbl1'><strong>Autor:</strong></td>\n";
                echo "<td class='tbl1'>".$data['dl_author']." ".($data['dl_author_www'] != "" ? "<a href='".$data['dl_author_www']."' title='Homepage'>Homepage</a>" : "")." ".($data['dl_author_email'] != "" ? "<a href='mailto:".$data['dl_author_email']."' title='E-Mail'>E-Mail</a>" : "")."</td>\n";
            }
            if ($data['dl_coauthor'] != 0) {
                echo "</tr><tr>\n";
                echo "<td class='tbl1'><strong>Co-Autor:</strong></td>\n";
                echo "<td class='tbl1'>".$data['dl_coauthor']."</td>\n";
            }
            if ($data['user_name'] != $data['dl_author']) {
                echo "</tr><tr>\n";
                echo "<td class='tbl1'><strong>Einsender:</strong></td>\n";
                echo "<td class='tbl1'>".profile_link($data['dl_user_id'], $data['user_name'], $data['user_status'])."</td>\n";
            }
            if ($data['dl_thread'] > 0) {
                echo "</tr><tr>\n";
                echo "<td class='tbl1'><strong>Supportthread:</strong></td>\n";
                echo "<td class='tbl1'><a href='".FORUM."viewthread.php?thread_id=".$data['dl_thread']."' title='Supportthread &ouml;ffnen' target='_blank'>Hier im Forum</a></td>\n";
            }
            if ($data['dl_folder'] != "") {
                echo "</tr><tr>\n";
                echo "<td class='tbl1'><strong>Livedemo:</strong></td>\n";
                echo "<td class='tbl1'><a href='http://themes.phpfusion-support.de/v701/news.php?theme=".$data['dl_folder']."' title='Livedemo &ouml;ffnen' target='_blank'>Hier ansehen</a></td>\n";
            }
            echo "</tr>\n";
            echo "</table>\n";
            echo "</td>\n";
            echo "<td class='tbl1'><center><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&amp;start_dl' title='Download starten'><img src='".DL_DIR."images/download.png' alt='Download starten' /></a>";
			echo "[".parsebytesize(filesize(DL_FILES.$data['dl_file']))."]<br />";
			echo $download_count."</center></td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            echo "</td>\n";
            echo "</tr>\n";
            echo "</table>\n";

            echo "<table width='98%' cellpadding='0' cellspacing='0' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td class='tbl1'><strong>Beschreibung:</strong></td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>".nl2br(parseubb($data['dl_description']))."</td>\n";
            echo "</tr>\n";
            echo "</table>\n";

            if ($data['dl_changelog'] != "") {
                echo "<table width='98%' cellpadding='0' cellspacing='0' align='center' class='tbl-border'>\n";
                echo "<tr>\n";
                echo "<td class='tbl1'><strong>Changelog:</strong></td>\n";
                echo "</tr><tr>\n";
                echo "<td class='tbl1'>".nl2br(parseubb($data['dl_changelog']))."</td>\n";
                echo "</tr>\n";
                echo "</table>\n";
            }

            echo "<table width='98%' cellpadding='0' cellspacing='0' align='center' class='tbl-border'>\n";
            echo "<tr>\n";
            echo "<td class='tbl1'><strong>Copyright / Lizenz:</strong></td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>".nl2br(parseubb($data['dl_copyright']))."</td>\n";
            echo "</tr>\n";
            echo "</table>\n";

            if (iMEMBER) {
                echo "<table width='98%' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top'>\n";
                echo "<tr>\n";
                echo "<td class='tbl1' style='width:12%; text-align:center;'><strong>Optionen:</strong></td>\n";
                echo "<td class='tbl1' style='width:22%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=1'>Download defekt</a></td>\n";
                echo "<td class='tbl1' style='width:19%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=4'>Link defekt</a></td>\n";
                echo "<td class='tbl1' style='width:25%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=2'>Sicherheitsl&uuml;cke entdeckt</a></td>\n";
                echo "<td class='tbl1' style='width:22%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=3'>Sonstiger Hinweis</a></td>\n";
                echo "</tr>\n";
                echo "</table>\n";
            }

            closetable();
            $dl_id = $_GET['dl_id'];
            showratings("M", $dl_id, FUSION_SELF."?dl_id=".$dl_id);
            showcomments("M", DL, "dl_id", $dl_id, FUSION_SELF."?dl_id=".$dl_id);
            if (isnum($data['dl_author'])) {
                $result3 = dbquery("SELECT aa.dl_id, aa.dl_title, aa.dl_count, ab.user_name, ac.dl_cat_name
                                    FROM ".DL." aa
                                    LEFT JOIN ".DB_USERS." ab ON aa.dl_user_id=ab.user_id
                                    LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                    WHERE aa.dl_user_id='".$data['dl_author']."' AND dl_id!=".$_GET['dl_id']."");
            } else {
                $result3 = dbquery("SELECT aa.dl_id, aa.dl_title, aa.dl_count, ab.user_name, ac.dl_cat_name
                                    FROM ".DL." aa
                                    LEFT JOIN ".DB_USERS." ab ON aa.dl_user_id=ab.user_id
                                    LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                    WHERE user_name='".$data['dl_author']."' AND dl_id!=".$_GET['dl_id']."");
            }
            if (dbrows($result3) > 0) {
                opentable("Weitere Downloads von ".$data['user_name']);
                echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top' style='margin-top:10px;'>\n";
                echo "<tr>\n";
                echo "<td class='tbl2'>Kategorie</td>\n";
                echo "<td class='tbl2'>Titel</td>\n";
                echo "<td class='tbl2'>Downloads</td>\n";
                echo "<td class='tbl2'>Bewertung</td>\n";
                echo "</tr>\n";
                while ($data3 = dbarray($result3)) {
                    echo "<tr>\n";
                    echo "<td class='tbl2'>".$data3['dl_cat_name']."</td>\n";
                    echo "<td class='tbl2'>".trimlink($data3['dl_title'], 40)."</td>\n";
                    echo "<td class='tbl2' style='text-align:center;'>".$data3['dl_count']."</td>\n";
                    $rate_array = dbarray(dbquery("SELECT SUM(rating_vote) sum_rating, COUNT(rating_item_id) count_votes FROM ".DB_RATINGS." WHERE rating_item_id='".$_GET['dl_id']."' AND rating_type='M'"));
                    if ($rate_array['count_votes'] > 0) {
                        $rate = $rate_array['sum_rating'] / $rate_array['count_votes'];
                        echo "<td class='tbl2'><img src='".DL_DIR."images/".ceil($rate).".gif' alt='".ceil($rate)."' style='vertical-align:middle;' title='Bewertung: ".ceil($rate)."' /></td>\n";
                    } else {
                        echo "<td class='tbl2'></td>\n";
                    }

                    echo "</tr>\n";
                }
                echo "</table>\n";
                closetable();
            }
        } else {
            opentable("Download DB");
            echo "<center>Der Download ist nicht vorhanden, wurde entfernt oder deaktiviert. <a href='".DL_DIR."dl_index.php' title='Download &Uuml;bersicht'>Hier geht es zur &Uuml;bersicht.</a></center>";
            closetable();
        }
    }
} else {
    opentable("Download DB");
    echo "<center>Der Download ist nicht vorhanden, wurde entfernt oder deaktiviert. <a href='".DL_DIR."dl_index.php' title='Download &Uuml;bersicht'>Hier geht es zur &Uuml;bersicht.</a></center>";
    closetable();
}
require_once THEMES."templates/footer.php";
?>
