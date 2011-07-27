<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."faq_db/includes/faq_core.php";

if (!checkrights("FAQ") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect(BASEDIR."../index.php"); }

$count = dbcount("(faq_fusion_id)", DB_FAQ_FUSION);

if (isset($_POST['faq_fusion_1st']) && isset($_POST['faq_fusion_2nd']) && isnum($_POST['faq_fusion_1st']) && isnum($_POST['faq_fusion_2nd']))
{
    $order = $count+1;
    $result = dbquery("INSERT INTO ".DB_FAQ_FUSION." SET faq_fusion_1st='".$_POST['faq_fusion_1st']."', faq_fusion_2nd='".$_POST['faq_fusion_2nd']."', faq_fusion_order='".$order."'");
}

if (isset($_GET['id']) && isnum($_GET['id'])) {
    if (isset($_GET['move_to']) && isnum($_GET['move_to'])) {
        if ($_GET['move_to'] != 0 && $_GET['move_to'] <= $count) {
            if (isset($_GET['up'])) {
                $result = dbquery("UPDATE ".DB_FAQ_FUSION." SET faq_fusion_order=faq_fusion_order+1 WHERE faq_fusion_order='".$_GET['move_to']."'");
                $result = dbquery("UPDATE ".DB_FAQ_FUSION." SET faq_fusion_order=faq_fusion_order-1 WHERE faq_fusion_id='".$_GET['id']."'");
            } elseif (isset($_GET['down'])) {
                $result = dbquery("UPDATE ".DB_FAQ_FUSION." SET faq_fusion_order=faq_fusion_order-1 WHERE faq_fusion_order='".$_GET['move_to']."'");
                $result = dbquery("UPDATE ".DB_FAQ_FUSION." SET faq_fusion_order=faq_fusion_order+1 WHERE faq_fusion_id='".$_GET['id']."'");
            }
        }
    }
    elseif (isset($_GET['delete'])) {
        $result = dbquery("DELETE FROM ".DB_FAQ_FUSION." WHERE faq_fusion_id='".$_GET['id']."'");
        $result = dbquery("SELECT faq_a_id, faq_a_fusion FROM ".DB_FAQ_A." WHERE faq_a_fusion REGEXP('^\\\.{$_GET['id']}$|\\\.{$_GET['id']}\\\.|\\\.{$_GET['id']}$')");
        while ($data = dbarray($result)) {
            $cats = $data['faq_a_fusion'];
            $cats_clean = preg_replace(array("(^\.{$_GET['id']}$)","(\.{$_GET['id']}\.)","(\.{$_GET['id']}$)"), array("",",",""), $cats);
            $result2 = dbquery("UPDATE ".DB_FAQ_A." SET faq_a_fusion='".$cats_clean."' WHERE faq_a_id='".$data['faq_a_id']."'");
        }
    }
}

opentable("Fusion FAQ: Navigation");
require_once FAQ_DIR."admin/faq_admin_navigation.php";
closetable();

opentable("Fusion FAQ: PHP-Fusion Version erstellen");
echo "<form action='".FUSION_SELF.$aidlink."' method='post'>\n";
echo "<table cellpadding='0' cellspacing='0' width='500px' align='center' class='tbl-border'>\n";
echo "<tr>\n";
echo "<td class='tbl'>PHP-Fusion Version:</td>\n";
echo "<td class='tbl'>";
echo "<input type='text' name='faq_fusion_1st' class='textbox' maxlength='2' style='width:20px' />\n";
echo ".<input type='text' name='faq_fusion_2nd' class='textbox' maxlength='2' style='width:20px' />.xx\n";
echo "</td>\n";
echo "<td class='tbl'><input type='submit' value=' Absenden' class='button' />\n";
echo "</tr>\n";
echo "</table>\n</form>\n";
closetable();

opentable("Fusion FAQ: PHP-Fusion Versionen");
$result = dbquery("SELECT faq_fusion_id, faq_fusion_1st, faq_fusion_2nd, faq_fusion_order FROM ".DB_FAQ_FUSION." ORDER BY faq_fusion_order ASC");
if (dbrows($result)>0)
{
    echo "<table cellpadding='0' cellspacing='0' width='500px' align='center' class='tbl-border'>\n";
    echo "<tr>\n";
    echo "<td class='tbl'>Id</td>\n";
    echo "<td class='tbl'>PHP-Fusion Version</td>\n";
    echo "<td class='tbl'>Optionen</td>\n";
    echo "</tr>\n";
    while ($data = dbarray($result))
    {
        echo "<tr>\n";
        echo "<td class='tbl'>".$data['faq_fusion_order']."</td>\n";
        echo "<td class='tbl'>".$data['faq_fusion_1st'].".".$data['faq_fusion_2nd']."</td>\n";
        echo "<td class='tbl'>";
        echo "<a href='".FUSION_SELF.$aidlink."&amp;id=".$data['faq_fusion_id']."&move_to=".($data['faq_fusion_order']-1)."&up'>Hoch</a>\n";
        echo " <a href='".FUSION_SELF.$aidlink."&amp;id=".$data['faq_fusion_id']."&move_to=".($data['faq_fusion_order']+1)."&down'>Runter</a>\n";
        echo " <a href='".FUSION_SELF.$aidlink."&amp;id=".$data['faq_fusion_id']."&delete'>L&ouml;schen</a>\n";
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}
else
{
    echo "<center>Es wurde bisher keine Version hinzugef&uuml;gt.</center>\n";
}
closetable();

require_once THEMES."templates/footer.php";
?>
