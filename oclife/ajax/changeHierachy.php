<?php
$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));
OCP\JSON::checkAppEnabled('oclife');
if($onlyAdminCanEdit) {
    OCP\User::checkAdminUser();
} else {
    OCP\JSON::checkLoggedIn();
}

$tagID = filter_input(INPUT_POST, 'movedTag', FILTER_SANITIZE_NUMBER_INT);
$parentID = filter_input(INPUT_POST, 'droppedTo', FILTER_SANITIZE_NUMBER_INT);

if($tagID === FALSE || $parentID === FALSE) {
    die('KO');
}

$ctags = new OCA\OCLife\hTags();
$ctags->setTagParentByID($tagID, $parentID);

echo 'OK';
