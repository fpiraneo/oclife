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
\OCP\JSON::checkAppEnabled('oclife');
\OCP\User::checkLoggedIn();

// Check for a valid operation to perform
$tagOp = filter_input(INPUT_POST, 'tagOp', FILTER_SANITIZE_STRING);
$validOps = array('new', 'rename', 'delete');

if(array_search($tagOp, $validOps) === FALSE) {
    $result = array(
        'result' => 'KO',
        'title' => '',
        'key' => '',
        'class' => ''
    );
    
    die(json_encode($result));
}

// Check for valid input parameters
$parentID = intval(filter_input(INPUT_POST, 'parentID', FILTER_SANITIZE_NUMBER_INT));
$tagID = filter_input(INPUT_POST, 'tagID', FILTER_SANITIZE_NUMBER_INT);
$tagName = filter_input(INPUT_POST, 'tagName', FILTER_SANITIZE_STRING);
$tagLang = filter_input(INPUT_POST, 'tagLang', FILTER_SANITIZE_STRING);
if($parentID === FALSE || $tagName === FALSE || strlen($tagLang) === 0 || strlen($tagLang) > 2) {
    $result = array(
        'result' => 'KO',
        'title' => '',
        'key' => '',
        'class' => ''
    );
    
    die(json_encode($result));
}

// Switch between possible operations
$ctags = new \OCA\OCLife\hTags();

switch($tagOp) {
    case 'new': {
        $result = $ctags->newTag($tagLang, $tagName, $parentID);
        
        break;
    }
    
    case 'rename': {
        $tagData = array($tagLang => $tagName);
        $ctags->alterTag($tagID, $tagData);
        $result = $tagID;
        
        break;
    }
    
    case 'delete': {
        $result = $ctags->deleteTagAndChilds(intval($tagID));
        
        break;
    }
}

// Publish the op result
if($result === FALSE) {
    $result = array(
        'result' => 'KO',
        'title' => '',
        'key' => '',
        'class' => ''
    );
} else {
    $result = array(
        'result' => 'OK',
        'title' => $tagName,
        'key' => $result,
        'class' => 'global'
    );
}

echo json_encode($result);
