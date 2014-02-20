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