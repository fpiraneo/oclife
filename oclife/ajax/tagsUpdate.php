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

$op = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_STRING);
$rawFileID = filter_input(INPUT_POST, 'fileID', FILTER_SANITIZE_URL);
$tagID = filter_input(INPUT_POST, 'tagID', FILTER_SANITIZE_NUMBER_INT);

$fileIDs = json_decode($rawFileID);

switch($op) {
    case 'add': {
        if(is_array($fileIDs)) {
            $result = \OCA\OCLife\hTags::addTagForFiles($fileIDs, $tagID);
        } else {
            $result = \OCA\OCLife\hTags::addTagForFile($fileIDs, $tagID);
        }
        
        break;
    }
    
    case 'remove': {
        if(is_array($fileIDs)) {
            $result = \OCA\OCLife\hTags::removeTagForFiles($fileIDs, $tagID);
        } else {
            $result = \OCA\OCLife\hTags::removeTagForFile($fileIDs, $tagID);
        }
        break;
    }
}

die($result);