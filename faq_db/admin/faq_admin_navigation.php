<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

echo "<table cellpadding='0' cellspacing='0' width='600px' class='tbl-border' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl'><a href='".FAQ_DIR."admin/faq_admin_index.php".$aidlink."'>&Uuml;bersicht</a></td>\n";
echo "<td class='tbl'><a href='".FAQ_DIR."admin/faq_admin_cats.php".$aidlink."'>Kategorien</a></td>\n";
echo "<td class='tbl'><a href='".FAQ_DIR."admin/faq_admin_fusion.php".$aidlink."'>PHP-Fusion Versionen</a></td>\n";
echo "<td class='tbl'><a href='".FAQ_DIR."admin/faq_admin_submit.php".$aidlink."'>Einsendungen</a></td>\n";
echo "<td class='tbl'><a href='".FAQ_DIR."admin/faq_admin_disabled.php".$aidlink."'>Deaktivierte Einsendungen</a></td>\n";
echo "</tr>\n";
echo "</table>\n";
?>
