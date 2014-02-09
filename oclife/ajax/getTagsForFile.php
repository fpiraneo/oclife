<?php
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$fileID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$etag = filter_input(INPUT_POST, 'etag', FILTER_SANITIZE_STRING);

$result = array();

$tagCodes = OCA\OCLife\hTags::getAllTagsForFile($fileID);

$tags = new OCA\OCLife\hTags();

foreach($tagCodes as $tagID) {
    $tagData = $tags->searchTagFromID($tagID);
    $result[] = new OCA\OCLife\tag($tagID, $tagData['xx']);
}

$jsonTagData = json_encode((array) $result);
echo $jsonTagData;