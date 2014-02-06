<?php
$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));
OCP\JSON::checkAppEnabled('oclife');
if($onlyAdminCanEdit) {
    OCP\User::checkAdminUser();
} else {
    OCP\JSON::checkLoggedIn();
}

$tagID = filter_input(INPUT_POST, 'tagID', FILTER_SANITIZE_NUMBER_INT);
$tagName = filter_input(INPUT_POST, 'tagName', FILTER_SANITIZE_STRING);

if($tagID === FALSE || $tagName === FALSE) {
    die('KO');
}

$ctags = new OCA\OCLife\hTags();

$tagData = array('xx' => $tagName);

$ctags->alterTag($tagID, $tagData);

echo 'OK';
