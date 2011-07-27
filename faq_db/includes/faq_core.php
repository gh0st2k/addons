<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }
include INFUSIONS."faq_db/infusion_db.php";

if (!defined("FAQ_DIR")) {
	define("FAQ_DIR", BASEDIR."infusions/faq_db/");
}

if (!defined("FAQ_INCLUDES")) {
	define("FAQ_INCLUDES", FAQ_DIR."includes/");
}

if (!defined("FAQ_BREADCRUMBS")) {
	define("FAQ_BREADCRUMBS", FAQ_DIR."includes/breadcrumbs_include.php");
}

if (!defined("FAQ_CATS")) {
	define("FAQ_CATS", FAQ_DIR."images/cats/");
}


if (!function_exists('status_output')) {
    function status_output($status) {
        switch($status) {
            case "0" : return "Pr&uuml;fung"; break;
            case "1" : return "Online"; break;
            case "2" : return "Deaktiviert"; break;
        }
    }
}
if (!function_exists('faq_cookie_check')) {
    function faq_cookie_check($id) {
        if (!isset($_COOKIE['faq_visited'])) {
            return faq_cookie_set($id);
        } else {
            $cookie_vars = explode(".", $_COOKIE['faq_visited']);
            if (!in_array($id, $cookie_vars)) {
                return faq_cookie_set($id);
            } else {
                return FALSE;
            }
        }
    }
}

if (!function_exists('faq_cookie_set')) {
    function faq_cookie_set($id) {
        if (isset($_COOKIE['faq_visited'])) {
            $cookie_vars = explode(".", $_COOKIE['faq_visited']);
            $cookie_vars[] = $id;
            $values = implode(".", $cookie_vars);
        } else {
            $values = $id;
        }
        $blub = setcookie("faq_visited", $values, time() + 86400, "/", "", "0");
        return $blub;
    }
}
?>
