<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(DL_DIR."index.php"); }

$access = dbcount("(dl_id)", DL, "dl_id='".$_GET['dl_id']."' AND dl_status='1'");
if (isset($_GET['dl_version_id']) && isnum($_GET['dl_version_id'])) {
    $access2 = dbcount("(dl_version_id)", DL, "dl_version_id='".$_GET['dl_version_id']."' AND dl_status='1'");
} else { $access2 = 1; }
if (($access > 0 && $access2 > 0) || dl_access("S") || dl_access("F") || dl_access("R")) {
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

        $("a.group").fancybox({
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
            $result = dbquery("SELECT aa.dl_title, aa.dl_description, aa.dl_copyright, aa.dl_licence, aa.dl_author, aa.dl_author_email, aa.dl_author_www, aa.dl_coauthor,
                                  aa.dl_timestamp, aa.dl_user_id, aa.dl_count, ab.dl_version_id, ab.dl_version, ab.dl_file, ab.dl_order, ab.dl_changelog, ab.dl_version_timestamp, ac.dl_cat_name,
                                  ad.dl_fusion_name, ae.user_name, af.dl_screen_file, af.dl_screen_thumb
                                  FROM ".DL." aa
                                  LEFT JOIN ".DL_VERSION." ab ON aa.dl_id=ab.dl_id
                                  LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                  LEFT JOIN ".DL_FUSION." ad ON aa.dl_fusion=ad.dl_fusion_id
                                  LEFT JOIN ".DB_USERS." ae ON aa.dl_user_id=ae.user_id
                                  LEFT JOIN ".DL_SCREEN." af ON aa.dl_id=af.dl_id
                                  WHERE aa.dl_id='".$_GET['dl_id']."' AND aa.dl_status='1' AND ab.dl_order=(SELECT dl_order FROM ".DL_VERSION." WHERE dl_id='".$_GET['dl_id']."' AND dl_version_status='1' ORDER BY dl_order ASC LIMIT 1)
                                  ORDER BY af.dl_screen_order
                                  LIMIT 1");
        } else {
            $result = dbquery("SELECT aa.dl_title, aa.dl_description, aa.dl_copyright, aa.dl_licence, aa.dl_author, aa.dl_author_email, aa.dl_author_www, aa.dl_coauthor,
                                  aa.dl_timestamp, aa.dl_user_id, aa.dl_count, ab.dl_version_id, ab.dl_version, ab.dl_file, ab.dl_order, ab.dl_changelog, ab.dl_version_timestamp, ac.dl_cat_name,
                                  ad.dl_fusion_name, ae.user_name, af.dl_screen_file, af.dl_screen_thumb
                                  FROM ".DL." aa
                                  LEFT JOIN ".DL_VERSION." ab ON aa.dl_id=ab.dl_id
                                  LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                  LEFT JOIN ".DL_FUSION." ad ON aa.dl_fusion=ad.dl_fusion_id
                                  LEFT JOIN ".DB_USERS." ae ON aa.dl_user_id=ae.user_id
                                  LEFT JOIN ".DL_SCREEN." af ON aa.dl_id=af.dl_id
                                  WHERE aa.dl_id='".$_GET['dl_id']."' AND aa.dl_status='1' AND ab.dl_version_id='".$_GET['version_id']."'
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
            // Ende
            opentable("Downloaddetails: ".$data['dl_title']);
            if (isset($_GET['message']) && $_GET['message'] == 1) {
                echo "<div id='close-message'><div class='admin-message center' style='width:600px'>Die Benachrichtung wurde an das Team &uuml;bermittelt.</div></div>\n<br />";
            } elseif (isset($_GET['message']) && $_GET['message'] == 2) {
                echo "<div id='close-message'><div class='admin-message center' style='width:600px'>Benachrichtigung nicht erfolgt. Bitte alle Felder ausf&uuml;llen.</div></div>\n<br />";
            }
            echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top'>\n";
            echo "<tr>\n";
            echo "<td class='tbl2' width='20%'><strong>Kategorie</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'>".$data['dl_cat_name']."</td>\n";
            echo "<td class='tbl2' width='20%'><strong>PHP-Fusion</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'>".$data['dl_fusion_name']."</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "<td class='tbl2' width='20%'><strong>Author</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'>".$data['dl_author'];
            if ($data['dl_author_email'] != "") { echo " [<a href='mailto:".$data['dl_author_email']."'>E-Mail</a>]"; }
            if ($data['dl_author_www'] != "") { echo "[<a href='".$data['dl_author_www']."'>www</a>]"; }
            echo "</td>\n";
            echo "<td class='tbl2' width='20%'><strong>Einreicher</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'>".profile_link($data['dl_user_id'], $data['user_name'], "1")."</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "<td class='tbl2' width='20%'><strong>Releasedatum</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'>".date("d.m.Y", $data['dl_timestamp'])."</td>\n";
            echo "<td class='tbl2' width='20%'><strong>Version</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'>".$data['dl_version']." vom ".date("d.m.Y", $data['dl_version_timestamp'])."</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "<td class='tbl2' width='20%'><strong>Download</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&start_dl'>Download starten</a></td>\n";
            echo "<td class='tbl2' width='20%'><strong>Downloads</strong>:</td>\n";
            echo "<td class='tbl1' width='30%'>".$data['dl_count']."</td>\n";
            echo "</tr>\n";
            echo "</table>";
                        echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top' style='margin-top:10px;'>\n";
            echo "<tr>\n";
            echo "<td class='tbl2'><strong>Screenshots</strong>:</td>\n";
            echo "</tr>\n<tr>\n";
            echo "<td class='tbl1'><center>";
            $result2 = dbquery("SELECT dl_screen_file, dl_screen_thumb FROM ".DL_SCREEN." WHERE dl_id='".$_GET['dl_id']."' ORDER BY dl_screen_order ASC");
            $i = 1;
            if (dbrows($result2) > 0) {
                while ($data2 = dbarray($result2)) {
                    echo "<a id='single_image' href='".DL_IMAGES.$data2['dl_screen_file']."' style='padding-left:10px;'><img src='".DL_IMAGES.$data2['dl_screen_thumb']."' alt='".$data['dl_title']."_screenhot_".$i."'></a>";
                    $i++;
                }
            } else {
                echo "<center>Es sind keine Screenshots verf&uuml;gbar.</center>";
            }
            unset($i);
            echo "</center></td>";
            echo "</tr>\n</table>\n";
            echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top' style='margin-top:10px;'>\n";
            echo "<tr>\n";
            echo "<td class='tbl2' width='100%'><strong>Beschreibung</strong>:</td>\n";
            echo "</tr><tr>\n";
            echo "<td class='tbl1'>".$data['dl_description']."</td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top' style='margin-top:10px;'>\n";
            echo "<tr>\n";
            echo "<td class='tbl2'><strong>Copyright / Lizenz</strong>:</td>\n";
            echo "</tr>\n<tr>\n";
            echo "<td class='tbl1'>".$data['dl_copyright']."</td>\n";
            //echo "</tr>\n<tr>\n";
            //echo "<td class='tbl2'><strong>Lizenz</strong>:</td>\n";
            //echo "</tr>\n<tr>\n";
            //echo "<td class='tbl1'>".$data['dl_licence']."</td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top' style='margin-top:10px;'>\n";
            echo "<tr>\n";
            echo "<td class='tbl2' width='25%'><strong>Optionen:</strong></td>\n";
            echo "<td class='tbl2' width='25%'></td>\n";
            echo "<td class='tbl2' width='25%'></td>\n";
            echo "<td class='tbl2' width='25%'></td>\n";
            if (iMEMBER) {
                echo "</tr>\n<tr>\n";
                echo "<td class='tbl1' style='width:25%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=1'>Download defekt</a></td>\n";
                echo "<td class='tbl1' style='width:25%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=4'>Link defekt</a></td>\n";
                echo "<td class='tbl1' style='width:25%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=2'>Sicherheitsl&uuml;cke entdeckt</a></td>\n";
                echo "<td class='tbl1' style='width:25%; text-align:center;'><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&msg_admin=3'>Sonstiger Hinweis</a></td>\n";
            }
            echo "</tr>\n";
            echo "</table>\n";
            $result2 = dbquery("SELECT dl_version_id, dl_version, dl_version_timestamp FROM ".DL_VERSION." WHERE dl_id='".$_GET['dl_id']."' AND dl_version_status='1' AND dl_version_id!='".$data['dl_version_id']."' AND dl_order>".$data['dl_order']." ORDER BY dl_order ASC");
            if (dbrows($result2) > 0) {
                echo "<table width='700px' class='tbl-border' cellpadding='0' cellspacing='0' align='center' valign='top' style='margin-top:10px;'>\n";
                echo "<tr>\n";
                echo "<td class='tbl2'><strong>Changelog zur Vorversion</strong>:</td>\n";
                echo "</tr>\n<tr>\n";
                echo "<td class='tbl1'>".nl2br($data['dl_changelog'])."</td>\n";
                echo "</tr>\n<tr>\n";
                echo "<td class='tbl2' width='100%'>Andere Versionen:</td>\n";
                while ($data2 = dbarray($result2)) {
                    echo "</tr>\n<tr>\n";
                    echo "<td class='tbl1'>";
                    echo "<a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&version_id=".$data2['dl_version_id']."'>Version ".$data2['dl_version']."</a> vom ".date("d.m.Y", $data2['dl_version_timestamp']);
                    echo "</td>\n";
                }
                echo "</tr>\n";
                echo "</table>\n";
            }

            closetable();
            require_once INCLUDES."comments_include.php";
            require_once INCLUDES."ratings_include.php";
            showratings("M", $_GET['dl_id'], FUSION_SELF."?dl_id=".$_GET['dl_id']);
            showcomments("M", DL, "dl_id", $_GET['dl_id'], FUSION_SELF."?dl_id=".$_GET['dl_id']);
            if (isnum($data['dl_author'])) {
                $result3 = dbquery("SELECT aa.dl_id, aa.dl_title, aa.dl_count, ab.user_name, ac.dl_cat_name, SUM(ad.rating_vote) AS vote_value, COUNT(ad.rating_item_id) AS vote_count
                                    FROM ".DL." aa
                                    LEFT JOIN ".DB_USERS." ab ON aa.dl_user_id=ab.user_id
                                    LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                    LEFT JOIN ".DB_RATINGS." ad ON aa.dl_id=ad.rating_item_id
                                    WHERE aa.dl_user_id='".$data['dl_author']."' AND rating_type='F' GROUP BY dl_id");
            } else {
                $result3 = dbquery("SELECT aa.dl_id, aa.dl_title, aa.dl_count, ab.user_name, ac.dl_cat_name, SUM(ad.rating_vote) AS vote_value, COUNT(ad.rating_item_id) AS vote_count
                                    FROM ".DL." aa
                                    LEFT JOIN ".DB_USERS." ab ON aa.dl_user_id=ab.user_id
                                    LEFT JOIN ".DL_CAT." ac ON aa.dl_cat=ac.dl_cat_id
                                    LEFT JOIN ".DB_RATINGS." ad ON aa.dl_id=ad.rating_item_id
                                    WHERE user_name='".$data['dl_author']."' AND rating_type='F' GROUP BY dl_id");
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
                    echo "<td class='tbl2'>".$data3['cat_name']."</td>\n";
                    echo "<td class='tbl2'>".trimlink($data3['dl_title'], 40)."</td>\n";
                    echo "<td class='tbl2' style='text-align:center;'>".$data3['dl_count']."</td>\n";
                    $rate_array = dbarray(dbquery("SELECT SUM(rating_vote) sum_rating, COUNT(rating_item_id) count_votes FROM ".DB_RATINGS." WHERE rating_item_id='".$mod_id."' AND rating_type='M'"));
                    echo "<td class='tbl2'>Bewertung</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                closetable();
            }
        }
    }
} else {
    opentable("Download DB");
    echo "<center>Sie haben hier keinen Zugriff. <a href='".DL_DIR."dl_index.php' title='Download &Uuml;bersicht'>Hier geht es zur &Uuml;bersicht.</a></center>";
    closetable();
}
require_once THEMES."templates/footer.php";
?>
