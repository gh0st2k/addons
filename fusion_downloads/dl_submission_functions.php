<?php
if (!defined("IN_FUSION")) { die("Access Denied"); }

function getFormValues ($id) {
	$result = dbquery("SELECT dl_title, dl_version, dl_file, dl_changelog, dl_fusion, dl_cat, dl_description, dl_author, dl_author_email, dl_author_www, dl_coauthor, dl_copyright, dl_licence, dl_user_id FROM ".DL." WHERE dl_id='".$_GET['dl_id']."' LIMIT 1");
}


if (isset($_GET['dl_id']) && isnum($_GET['dl_id'])) {
    $result = dbquery("SELECT dl_title, dl_version, dl_file, dl_changelog, dl_fusion, dl_cat, dl_description, dl_author, dl_author_email, dl_author_www, dl_coauthor, dl_copyright, dl_licence, dl_user_id FROM ".DL." WHERE dl_id='".$_GET['dl_id']."' LIMIT 1");
    if (dbrows($result) > 0) {
        $data = dbarray($result);
        if (!iADMIN && $data['dl_user_id'] != $userdata['user_id']) { redirect(FUSION_SELF); }
        $dl_title = $data['dl_title'];
        $dl_fusion = explode(".",$data['dl_fusion']);
        $dl_cat = $data['dl_cat'];
        $dl_description = $data['dl_description'];
        $dl_author = $data['dl_author'];
        $dl_author_email = $data['dl_author_email'];
        $dl_author_www = $data['dl_author_www'];
        $dl_coauthor = $data['dl_coauthor'];
        $dl_copyright = $data['dl_copyright'];
        $dl_version = $data['dl_version'];
        $dl_file = $data['dl_file'];
        $dl_changelog = $data['dl_changelog'];
        //$dl_licence = $data['dl_licence'];
        $get = "?dl_id=".$_GET['dl_id']."&step=1";
    } else {
        redirect(FUSION_SELF);
    }

