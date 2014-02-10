<?php
$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));
OCP\JSON::checkAppEnabled('oclife');
if($onlyAdminCanEdit) {
    OCP\User::checkAdminUser();
} else {
    OCP\User::checkLoggedIn();
}

$parentID = intval(filter_input(INPUT_POST, 'parentID', FILTER_SANITIZE_NUMBER_INT));
$tagName = filter_input(INPUT_POST, 'tagName', FILTER_SANITIZE_STRING);
$tagLang = filter_input(INPUT_POST, 'tagLang', FILTER_SANITIZE_STRING);

if($parentID === FALSE || $tagName === FALSE || strlen($tagLang) === 0 || strlen($tagLang) > 2) {
    die('KO-0');
}

$ctags = new OCA\OCLife\hTags();

$newTagID = $ctags->newTag($tagLang, $tagName, $parentID);

if($newTagID === FALSE) {
    echo 'KO-1';
} else {
    echo 'OK-' . $newTagID;
}