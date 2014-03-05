<?php
/*
 * Copyright 2014 by Francesco PIRANEO G. (fpiraneo@gmail.com)
 * 
 * This file is part of oclife.
 * 
 * oclife is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * oclife is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with oclife.  If not, see <http://www.gnu.org/licenses/>.
 */
\OCP\JSON::callCheck();
\OCP\JSON::checkLoggedIn();
\OCP\JSON::checkAppEnabled('oclife');

$ctags = new \OCA\OCLife\hTags();

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
$filesIDs = \OCA\OCLife\hTags::getFileWithTagArray($tagsToSearch);
$fileData = \OCA\OCLife\utilities::getFileInfoFromID(OCP\User::getUser(), $filesIDs);

$result = '';
foreach($fileData as $file) {
    $result .= \OCA\OCLife\utilities::prepareTile($file);
}

echo $result;