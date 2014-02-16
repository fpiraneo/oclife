<?php
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$ctags = new OCA\OCLife\hTags();

$JSONtags = filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_URL);

// Look for selected tag and child
$tags = json_decode($JSONtags);
$tagsToSearch = array();

foreach($tags as $tag) {    
    $tagID = intval($tag->key);
    
    $partTags = $ctags->getAllChildID($tagID);
    
    foreach($partTags as $tag) {
        $tagsToSearch[] = intval($tag);
    }
}

// Look for files with that tag
$filesIDs = OCA\OCLife\hTags::getFileWithTagArray($tagsToSearch);
$fileData = OCA\OCLife\utilities::getFileInfoFromID(OCP\User::getUser(), $filesIDs);

$result = '';
foreach($fileData as $file) {
    $result .= OCA\OCLife\utilities::prepareTile($file);
}

echo $result;