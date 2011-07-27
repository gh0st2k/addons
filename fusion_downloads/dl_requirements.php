<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright � 2002 - 2009 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: submission_guidelines.php
| CVS Version: 1.00
| Author: PHP-Fusion MODs & Infusions Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

add_to_title(" Anforderungen f&uuml;r Downloads");

opentable("Anforderungen und Richtlinien f&uuml;r Downloads");

echo "<img src='".DL_DIR."images/logo.png' width='200' align='left' alt ='' />\n";
echo "<div>";
echo "<br />";
echo "Die Qualit&auml;t der Downloads hat f&uuml;r uns eine hohe Priorit&auml;t. Wir haben einige Regeln aufgestellt um diese Qualit&auml;t zu gew&auml;hrleisten und
      die einfache Installation f&uuml;r alle User zu erm&ouml;glichen. Wir bitten euch, diese Regeln zu beachten!";
echo "<br /><br /><div>";
echo "<div><strong>Einsendungen, die nicht den Regeln entsprechen, werden von unserem Team nach der &Uuml;berpr&uuml;fung abgelehnt.</strong>";
echo "</div>";

echo "<br /><br />";
echo "<br /><br />";

echo "<div>";
echo "<b>Allgemein:</b>";
echo "<br />";
echo "<ul>";
    echo "<li>Einsendungen m&uuml;ssen f&uuml;r PHP Fusion geschrieben sein oder explizit damit verwendbar sein.</li>";	      
    echo "<li>Alle Einsendungen m&uuml;ssen getestet und voll funktionsf&auml;hig sein.</li>";
	echo "<li>Alpha- oder Betaversionen bitte vorher im Forum posten und erst in der finalen Version einsenden.</li>";
echo "</ul>";

echo "<strong>Copyright / Lizenz</strong>";
echo "<br />";
echo "<ul>
	      <li>Alle eingesendeten Downloads m&uuml;ssen unter der AGPL v3 lizenziert werden.</li>
	      <li>Sofern enthaltene Bilder oder Scripte nicht unter der AGPL v3 lizenziert sind, brauchst du die Erlaubnis des Authors, damit der Download eingesendet werden darf.</li>";
          echo "<li><b>Wichtig:</b> Alle Einsendungen m&uuml;ssen eine Kopie der Lizenz enthalten (<a href='".DL_DIR."agpl.txt'>agpl.txt</a>).</li>";
          echo "<li>Der Download muss in mindestens einer Datei den folgenden Copyright Teil enthalten:</li>";
          echo "</ul><br />\n";
          echo "<img style='padding-left:10px' src='".DL_DIR."images/header_info.jpg' width=416' alt ='' /><br /><br />\n";

echo "<b>Inhaltliche Vorgaben:</b>";
echo "<ul>";
	echo "<li>Die Einsendung muss eine Installationsanleitung in unten genannter Form enthalten:<br /></li>";
	  echo "<ul type='circle'>";
	  echo "<li>readme-de.txt oder readme-de.html - Deutsch</li>";
	  echo "<li>readme-en.txt oder readme-en.html - Englisch</li>";
	  echo "<li>readme-es.txt oder readme-es.html - Spanisch</li>";
	  echo "<li>readme-fr.txt oder readme-fr.html - Franz&ouml;sisch und so weiter.</li>";
	  echo "</ul>";
      echo "<li>";
	echo "<b>Screenshots: </b>";
	echo sprintf("Max. Dateigr&ouml;&szlig;e: %s / Max. Aufl&ouml;sung: %ux%u Pixel", parsebytesize($settings['photo_max_b']), $settings['photo_max_w'], $settings['photo_max_h']);
	echo "</li>";
    echo "<li>Die Downloads sollten die unten abgebildete Ordnerstruktur erhalten, so dass eine m&ouml;glichst einfache Installation gew&auml;hrleistet ist:";
	echo "</ul>\n";
	
    echo "<table border='0' width='80%' align='center' cellspacing='0' cellpadding='0'><tr>\n";
	echo "<td class='tbl1' align='center'><b>Infusion / Panel</b></td><td class='tbl1' align='center'><b>Userfeld</b></td>";
	echo "</tr><tr>";
	echo "<td align='center'><img src='".DL_DIR."images/folder_tree.jpg' width=230' alt ='' /></td><td align='center'><img src='".DL_DIR."images/user_field_tree.jpg' width=266' alt ='' /></td>";
	echo "</tr>\n</table>\n";
	
	echo "<br /><br />\n";

    echo "<b>Sicherheit:</b>";
	echo "<ul>";
    echo "<li>Die Sicherheit hat oberste Priorit&auml;t, da meist schlecht entwickelte und gesch&uuml;tzte Mods das Einfallstor f&uuml;r Hacker sind!</li>";
    echo "<li>Alle \$_GET und \$_POST Variablen m&uuml;ssen vor der Verarbeitung gepr&uuml;ft werden:</li>";
    echo "<ul>";
    echo "<li>Numerische Werte: Zahlen k&ouml;nnen leicht mit der Funktion \"<a href='http://code.starefossen.com/infusions/fusion_functions/functions.php?function=isnum&highlight=isnum'>isnum();</a>\" gepr&uuml;ft werden.</li>";
    echo "<li>Alle anderen Typen werden mit der Funktion \"<a href='http://code.starefossen.com/infusions/fusion_functions/functions.php?function=stripinput&highlight=stripinput'>stripinput();</a>\" vor der Weiterverarbeitung gepr&uuml;ft.</li>";
    echo "</ul>";
    echo "</ul>";
	echo "Diese und alle anderen PHP-Fusion Funktionen sind in der <a href='http://code.starefossen.com/infusions/fusion_functions/functions.php' target='_blank' rel='nofollow'>PHP-Fusion Funktionen Codex Datenbank</a> erl&auml;utert.";
	echo "</div>";
	
    if (iMEMBER) {
	echo "<div>";
    echo "<br /><br /><b>Wichtig:</b>";
    echo "<br />Wir bitten um Beachtung dieser Regeln, so dass die nachtr&auml;gliche Bearbeitung nicht n&ouml;tig ist.<br />";
    echo "Wenn weitere Fragen bestehen, wende dich bitte an <a href='".BASEDIR."profile.php?lookup=2178'>gh0st2k</a> oder <a href='".BASEDIR."profile.php?lookup=1'>Sascha</a>.<br />";
    echo "<br /><br /><br /><div align='left'>Ich habe die Bedingungen gelesen, verstanden und akzeptiere diese: <a href='".DL_DIR."dl_submit.php' title='Weiter zum Einsendeformular' class='button'>Weiter zum Einsendeformular</a></div><br />\n";
    echo "</div>";
	} else { echo "<br /><span class='small'>Du musst eingeloggt sein, um einen Download einzusenden</span>\n"; }
    echo "<br /><div align='right' class='small'>Download Datenbank Richtlinien v1.4</div>\n";

closetable();
            
require_once THEMES."templates/footer.php";
?>