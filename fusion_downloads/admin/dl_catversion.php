<?php
require_once "../../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."fusion_downloads/includes/dl_core.php";
require_once DL_DIR."includes/dl_admin_navigation.php";
if (!dl_access("A")) { redirect(DL_DIR_ADMIN."dl_index.php?no_access"); }

if (isset($_GET['section'])) {
    if ($_GET['section'] == "cat") {
        $name = "Name der Kategorie:";
        $db = DL_CAT;
        $db_id = "dl_cat_id";
        $db_name = "dl_cat_name";
        $db_order = "dl_cat_order";
    }
    elseif ($_GET['section'] == "version") {
        $name = "PHP-Fusion Version:";
        $db = DL_FUSION;
        $db_id = "dl_fusion_id";
        $db_name = "dl_fusion_name";
        $db_order = "dl_fusion_order";
    } else {
        redirect(FUSION_SELF);
    }
}

if (isset($_GET['id']) && isnum($_GET['id'])) {
    if (isset($_GET['up']) && isnum($_GET['up'])) {
        $db_upper = dbarray(dbquery("SELECT ".$db_id." FROM ".$db." WHERE ".$db_order."='".$_GET['up']."'"));
        $result = dbquery("UPDATE ".$db." SET ".$db_order."='".($_GET['up']+1)."' WHERE ".$db_id."='".$db_upper[$db_id]."'");
        $result = dbquery("UPDATE ".$db." SET ".$db_order."='".$_GET['up']."' WHERE ".$db_id."='".$_GET['id']."'");
        dl_reorder($db, $db_id, $db_order);
    } elseif (isset($_GET['down']) && isnum($_GET['down'])) {
        $db_upper = dbarray(dbquery("SELECT ".$db_id." FROM ".$db." WHERE ".$db_order."='".$_GET['down']."'"));
        $result = dbquery("UPDATE ".$db." SET ".$db_order."='".($_GET['down']-1)."' WHERE ".$db_id."='".$db_upper[$db_id]."'");
        $result = dbquery("UPDATE ".$db." SET ".$db_order."='".$_GET['down']."' WHERE ".$db_id."='".$_GET['id']."'");
        dl_reorder($db, $db_id, $db_order);
    }
}

if (isset($_GET['delete']) && isnum($_GET['delete'])) {
    $query = dbquery("DELETE FROM ".$db." WHERE ".$db_id."='".$_GET['delete']."'");
    dl_reorder($db, $db_id, $db_order);
    redirect(FUSION_SELF."?section=".$_GET['section']."&amp;message=deleted");
}

if (isset($_GET['edit']) && isnum($_GET['edit'])) {
    $edit = dbarray(dbquery("SELECT ".$db_name.", ".$db_order." FROM ".$db." WHERE ".$db_id."='".$_GET['edit']."'"));
    $dl_name = $edit[$db_name];
    $dl_order = $edit[$db_order];
    $dl_id = $_GET['edit'];
} else { $dl_name = ""; $dl_order = ""; $dl_id = "";}

if (isset($_POST['dl_submit']) && isset($_GET['section'])) {
    if (isset($_POST['dl_name']) && $_POST['dl_name'] != "") { $dl_name = trim(stripinput($_POST['dl_name'])); }
    else { redirect(FUSION_SELF.$aidlink."&amp;section=".$_GET['section']."&amp;error=1"); }
    if (isset($_POST['dl_order']) && isnum($_POST['dl_order'])) {
        $dl_order = $_POST['dl_order'];
    } else {
        $dl_order = dbcount("(".$db_id.")", $db);
        $dl_order = $dl_order+1;
    }
    if (isset($_GET['edit']) && isnum($_GET['edit'])) {
        $result = dbquery("UPDATE ".$db." SET ".$db_order."=".$db_order."+1 WHERE ".$db_order.">='".$dl_order."'");
        $result = dbquery("UPDATE ".$db." SET ".$db_name."='".$dl_name."', ".$db_order."='".$dl_order."' WHERE ".$db_id."='".$_GET['edit']."'");
        dl_reorder($db, $db_id, $db_order);
        redirect(FUSION_SELF."?section=".$_GET['section']."&amp;message=edited");

    } else {
        if (isnum($_POST['dl_order'])) {
            $result = dbquery("UPDATE ".$db." SET ".$db_order."=".$db_order."+1 WHERE ".$db_order.">='".$dl_order."'");
        }
        $result = dbquery("INSERT INTO ".$db." SET ".$db_name."='".$dl_name."', ".$db_order."='".$dl_order."'");
        redirect(FUSION_SELF."?section=".$_GET['section']."&amp;message=saved");
    }
}

opentable("Download DB - Kategorien, Typen und PHP-Fusion Versionen");
admin_navigation();
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case "saved": echo "<div id='close-message'><div class='admin-message center' style='width:600px'>Die Eintrag wurde gespeichert.</div></div>\n<br />"; break;
        case "deleted": echo "<div id='close-message'><div class='admin-message center' style='width:600px'>Der Eintrag wurde gel&ouml;scht.</div></div>\n<br />"; break;
        case "edited": echo "<div id='close-message'><div class='admin-message center' style='width:600px'>Die Eintrag wurde editiert.</div></div>\n<br />"; break;
    }
}
echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='600px' align='center'>\n";
echo "<tr>\n";
echo "<td class='tbl1' style='text-align:center;'><a href='".FUSION_SELF."?section=cat'>Kategorien</a></td>\n";
echo "<td class='tbl1' style='text-align:center;'><a href='".FUSION_SELF."?section=version'>Versionen</a></td>\n";
echo "</tr>\n";
echo "</table>\n";

if (isset($_GET['section'])) {
    $result = dbquery("SELECT ".$db_id.", ".$db_name.", ".$db_order." FROM ".$db." ORDER BY ".$db_order." ASC");
    if (dbrows($result) > 0) {
        echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='500px' align='center'>\n";
        echo "<tr>\n";
        echo "<td class='tbl1' width='50px'>Nr.</td>\n";
        echo "<td class='tbl1' width='350px'>Name</td>\n";
        echo "<td class='tbl1' width='100px'>Optionen</td>\n";
        echo "</tr>\n";
        while ($data = dbarray($result)) {
            echo "<tr>\n";
            echo "<td class='tbl1'>".$data[$db_order]."</td>\n";
            echo "<td class='tbl1'>".$data[$db_name]."</td>\n";
            echo "<td class='tbl1'>";
            echo "<a href='".FUSION_SELF."?section=".$_GET['section']."&amp;edit=".$data[$db_id]."'><img src='".DL_DIR."images/bug_edit.png' alt='Editieren' /></a>";
            echo " <a href='".FUSION_SELF."?section=".$_GET['section']."&amp;delete=".$data[$db_id]."'><img src='".DL_DIR."images/bug_delete.png' alt='L&ouml;schen' /></a>";
            echo " <a href='".FUSION_SELF."?section=".$_GET['section']."&amp;id=".$data[$db_id]."&amp;up=".($data[$db_order]-1)."'><img src='".DL_DIR."images/arrow_up.png' alt='Hoch' /></a>";
            echo " <a href='".FUSION_SELF."?section=".$_GET['section']."&amp;id=".$data[$db_id]."&amp;down=".($data[$db_order]+1)."'><img src='".DL_DIR."images/arrow_down.png' alt='Runter' /></a>";
            echo "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo "<br \>";
    }
    echo "<form action='".FUSION_SELF."?section=".$_GET['section']."&amp;edit=".$dl_id."' method='post'>\n";
    echo "<table cellpadding='0' cellspacing='0' class='tbl-border' width='550px' align='center'>\n";
    echo "<tr>\n";
    echo "<td class='tbl1' width='150px'>".$name."</td>\n";
    echo "<td class='tbl1'><input type='text' name='dl_name' class='textbox' style='width:300px' maxlength='".($_GET['section'] == "version" ? "8" : "50")."' value='".$dl_name."' /></td>\n";
    echo "</tr>";
    echo "<tr>\n";
    echo "<td class='tbl1'>Position:</td>\n";
    echo "<td class='tbl1'><input type='text' name='dl_order' class='textbox' style='width:300px' maxlength='2' value='".$dl_order."' /></td>\n";
    echo "</tr>";
    echo "<tr>\n";
    echo "<td class='tbl1'></td>\n";
    echo "<td class='tbl1'><input type='submit' name='dl_submit' class='button' value='".(isset($_GET['edit']) ? "Daten editieren" : "Daten speichern")."' /></td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</form>\n";
}
closetable();
require_once THEMES."templates/footer.php";
?>
