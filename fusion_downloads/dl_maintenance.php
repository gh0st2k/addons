<?php
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";

if (isset($dl_settings['maintenance']) && $dl_settings['maintenance'] == 1) {
    opentable("Download DB Wartungsmodus");
    echo "<center>Die Downloaddatenbank wird gerade &uuml;berarbeitet. Wir bitten um euer Verst&auml;ndnis.</center>";
    closetable();
}

require_once THEMES."templates/footer.php";
?>
