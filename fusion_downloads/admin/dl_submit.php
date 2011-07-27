<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";


if (isset($_GET['del']) && isnum($_GET['del'])) {
	$data = dbarray(dbquery("SELECT dl_thread FROM ".DL." WHERE dl_id='".$_GET['del']."'"));
	if ($data['dl_thread'] > 0) {
		DeleteSupportThread($data['dl_thread']);
	}
	$result = dbquery("DELETE FROM ".DL." WHERE dl_id='".$_GET['del']."'");
	$result = dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_item_id='".$_GET['del']."' AND comment_type='M'");
	$result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_item_id='".$_GET['del']."' AND rating_type='M'");
	echo "<div id='close-message'><div class='admin-message'>Der Download wurde gel&ouml;scht.</div></div>\n";
}

opentable("Zu bearbeitende Einsendungen");
admin_navigation();
echo "<br />\n";
$result = dbquery(" SELECT aa.dl_id, aa.dl_title, aa.dl_version, aa.dl_file, aa.dl_user_id, ac.user_name
                        FROM ".DL." aa
                        LEFT JOIN ".DB_USERS." ac ON aa.dl_user_id=ac.user_id
                        WHERE aa.dl_status='1'");
if (dbrows($result)) {
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='700px' align='center'>\n";
    echo "<tr>\n";
    echo "<td class='tbl2' style='width:20px'>ID</td>\n";
    echo "<td class='tbl2' style='width:170px'>Titel</td>\n";
    echo "<td class='tbl2' style='width:30px'>Version</td>\n";
    echo "<td class='tbl2' style='width:130px'>User</td>\n";
    echo "<td class='tbl2' style='width:220px'>Optionen</td>\n";
    echo "<td class='tbl2' style='width:130px'>Pr&uuml;fung</td>\n";
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
        if (dl_access("L")) { echo " <a href='".FUSION_SELF."?del=".$data['dl_id']."'>L&ouml;schen</a>"; }
        echo "</td>\n";
        echo "<td class='".$class."'>";
        echo "<a href='".DL_DIR_ADMIN."dl_status.php?dl_id=".$data['dl_id']."'>Zur Status&auml;nderung</a>\n";
        echo "</td>\n";
        echo "</tr>\n";
        $i++;
    }
    echo "</table>\n";
} else {
    echo "<center>Es sind keine Einsendungen vorhanden.</center>\n";
}
closetable();
require_once THEMES."templates/footer.php";
?>
