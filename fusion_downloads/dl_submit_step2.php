<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (!isset($_GET['dl_id']) || !isnum($_GET['dl_id'])) { redirect(FUSION_SELF); }

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

if (isset($_GET['back'])) {
    echo "<div id='close-message'><div class='admin-message'>Bitte erst mindestens einen Screenshot erstellen, danach geht es weiter.</div></div>\n";
}

if (isset($_GET['screen_id']) && isnum($_GET['screen_id']) && isset($_GET['delete'])) {
    $data = dbarray(dbquery("SELECT dl_screen_file, dl_screen_thumb FROM ".DL_SCREEN." WHERE dl_screen_id='".$_GET['screen_id']."'"));
    @unlink(DL_IMAGES.$data['dl_screen_file']);
    @unlink(DL_IMAGES.$data['dl_screen_thumb']);
    $result = dbquery("DELETE FROM ".DL_SCREEN." WHERE dl_screen_id='".$_GET['screen_id']."'");
    dl_reorder(DL_SCREEN, "dl_screen_id", "dl_screen_order");
    echo "<div id='close-message'><div class='admin-message'>Der Screenshot wurde gel&ouml;scht.</div></div>\n";
}

if (isset($_POST['save'])) {
    require_once INCLUDES."infusions_include.php";
    $upload = upload_image("dl_file", "", DL_IMAGES, $dl_settings['screen_width'], $dl_settings['screen_height'], $dl_settings['screen_size'], FALSE, TRUE, FALSE,  "", DL_IMAGES, "thumb", $dl_settings['thumb_width'], $dl_settings['thumb_height']);
    if ($upload['error'] == 0) {
        $order = dbcount("(dl_screen_id)", DL_SCREEN, "dl_id='".$_GET['dl_id']."'")+1;
        $result = dbquery("INSERT INTO ".DL_SCREEN." SET dl_id='".$_GET['dl_id']."', dl_screen_file='".$upload['image_name']."', dl_screen_thumb='".$upload['thumb1_name']."', dl_screen_order='".$order."'");
        $result = dbquery("UPDATE ".DL." SET dl_status='1' WHERE dl_id='".$_GET['dl_id']."'");
        echo "<div id='close-message'><div class='admin-message'>Der Screenshot wurde gespeichert.</div></div>\n";
    } else {
        switch ($upload['error']) {
            case 1: echo "<div id='close-message'><div class='admin-message'>Die maximale Dateigr&ouml;&szlig;e ist &uuml;berschritten.</div></div>\n"; break;
            case 2: echo "<div id='close-message'><div class='admin-message'>Dieser Dateityp wird nicht unterst&uuml;tzt.</div></div>\n"; break;
            case 3: echo "<div id='close-message'><div class='admin-message'>Die Aufl&ouml;sung ist zu hoch. (max. ".$dl_settings['screen_width']."x".$dl_settings['screen_height']."</div></div>\n"; break;
            case 4: echo "<div id='close-message'><div class='admin-message'>Unbekannter Fehler.</div></div>\n"; break;
        }
    }
}

opentable("&Uuml;bersicht der Screenshots:");
$result = dbquery("SELECT dl_screen_id, dl_screen_file, dl_screen_thumb FROM ".DL_SCREEN." WHERE dl_id='".$_GET['dl_id']."'");
$dummy = array();
if (dbrows($result) > 0) {
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
    $i = dbrows($result);
    $j = 1;
    while ($data = dbarray($result)) {
        $dummy[$j]["dl_screen_id"] = $data['dl_screen_id'];
        $dummy[$j]["dl_screen_file"] = $data['dl_screen_file'];
        $dummy[$j]["dl_screen_thumb"] = $data['dl_screen_thumb'];
        $j++;
    }
    echo "<tr>\n";
    for ($k=1; $k<=$i; $k++) {
        echo "<td class='tbl2' width='120px'><center><a href='".DL_IMAGES.$dummy[$k]["dl_screen_file"]."' id='single_image'><img src='".DL_IMAGES.$dummy[$k]["dl_screen_thumb"]."' alt='".$dummy[$k]["dl_screen_id"]."' /></a></center></td>";
    }
    echo "</tr\n";
    echo "<tr>\n";
    for ($k=1; $k<=$i; $k++) {
        echo "<td class='tbl1'>";
        echo "<center><a href='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=2&screen_id=".$dummy[$k]["dl_screen_id"]."&delete'>L&ouml;schen</a></center>";
        echo "</td>\n";
    }
    echo "</tr>\n";
    echo "</table>\n";
} else {
    echo "<center>Bisher wurden keine Screenshots hochgeladen.</center>";
}
closetable();

opentable("Screenshot hochladen");
echo "<form action='".FUSION_SELF."?dl_id=".$_GET['dl_id']."&step=2' method='post' enctype='multipart/form-data'>\n";
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>";
echo "<td class='tbl1'>Screenshot ausw&auml;hlen:</td>\n";
echo "<td class='tbl1'><input type='file' name='dl_file' class='textbox' style='width:300px' /></td>\n";
echo "</tr><tr>";
echo "<td class='tbl1'></td>\n";
echo "<td class='tbl1'><input type='submit' name='save' value=' Speichern' class='button' /></td>\n";
echo "</tr></table>\n";
echo "</form>";
closetable();
?>
