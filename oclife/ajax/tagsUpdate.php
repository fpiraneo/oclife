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

$op = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_STRING);
$fileID = filter_input(INPUT_POST, 'fileID', FILTER_SANITIZE_NUMBER_INT);
$tagID = filter_input(INPUT_POST, 'tagID', FILTER_SANITIZE_NUMBER_INT);
$tagIDString = filter_input(INPUT_POST, 'tagID', FILTER_SANITIZE_STRING);
$tagName = filter_input(INPUT_POST, 'tagName', FILTER_SANITIZE_STRING);

//error_log(sprintf("tagsUpdate.php - Op: %s, fileID: %d, tagID: %d == ", $op, $fileID, $tagID));

switch($op) {
    case 'add': {
        $result = OCA\OCLife\hTags::addTagForFile($fileID, $tagID);
        break;
    }
    
    case 'remove': {
        $result = OCA\OCLife\hTags::removeTagForFile($fileID, $tagID);
        break;
    }
}

die($result);