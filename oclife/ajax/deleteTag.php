<?php

$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));
OCP\JSON::checkAppEnabled('oclife');
if($onlyAdminCanEdit) {
    OCP\User::checkAdminUser();
} else {
    OCP\JSON::checkLoggedIn();
}

$tagID = filter_input(INPUT_POST, 'tagID', FILTER_SANITIZE_NUMBER_INT);

if($tagID === FALSE) {
    die('KO');
}

$ctags = new OCA\OCLife\hTags();

$deletedTags = $ctags->deleteTagAndChilds(intval($tagID));

if($deletedTags !== FALSE) {
    echo 'OK';
} else {
    echo 'KO';
}
