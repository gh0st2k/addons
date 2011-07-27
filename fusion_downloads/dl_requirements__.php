<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

opentable("Anforderungen f&uuml;r Downloads");
echo "<ul>";
echo "<li> eine deutsche Readme muss enthalten sein</li>\n";
echo "<li> die Infusion muss sollte gewissenhaft getestet sein, zeigt sie uns eventuell vorher im Forum</li>\n";
echo "<li> die Ordnerstruktur muss immer vom Hauptverzeichnis von PHP-Fusion ausgehend sein</li>\n";
echo "</ul>";
echo "Beispiel:<br />";
echo "<img src='".DL_DIR."images/dl_requirements.png' alt='Ordnerstruktur' />";
echo "<br />";
echo "<br />";
echo "<a href='".DL_DIR."dl_submit.php' title='Download Einsenden'>Hier geht es weiter zur Einsendung</a>\n";
closetable();

require_once THEMES."templates/footer.php";
?>
