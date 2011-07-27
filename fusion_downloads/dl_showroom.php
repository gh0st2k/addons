<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

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
$themesCat = 8;
$result = dbquery("SELECT
                    aa.dl_id, aa.dl_title, aa.dl_folder, aa.dl_count, ab.dl_screen_file, ab.dl_screen_thumb
                   FROM
                    ".DL." aa
                   LEFT JOIN
                    ".DL_SCREEN." ab ON aa.dl_id=ab.dl_id
                   WHERE aa.dl_cat='8' AND ab.dl_screen_order='1'");
opentable("Download DB: Themegalerie");
if (dbrows($result) > 0) {
    //echo "<div style='position:absolute; display:inline;'>";
    $i = 1;
    while ($data = dbarray($result)) {
        $float = ($i % 2 == 0 ? "float:right;" : "float:left;");
        echo "<div style='".$float." width:250px; height='200px'>";
        echo "<a href='".DL_IMAGES.$data['dl_screen_file']."' id='single_image'><img src='".DL_IMAGES.$data['dl_screen_thumb']."' style='border:none;' /></a>\n ";
        echo "</div>\n";
        if ($i % 2 == 0) { "<div class='clearfix'>&nbsp;</div>\n"; }
    }
    //echo "</div>\n";
}
closetable();

require_once THEMES."templates/footer.php";
?>
