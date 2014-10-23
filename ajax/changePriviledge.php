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
\OCP\JSON::checkLoggedIn();

$tagID = filter_input(INPUT_POST, 'tagID', FILTER_SANITIZE_NUMBER_INT);
$priviledge = filter_input(INPUT_POST, 'setPriviledge', FILTER_SANITIZE_STRING);
$tagOwnerToSet = filter_input(INPUT_POST, 'tagOwner', FILTER_SANITIZE_STRING);

// At least tagID and the priviledge or the owner has to be set to perform a valid operation
if(!isset($tagID) || (!isset($priviledge) && !isset($tagOwnerToSet))) {
    $result = json_encode(array('result'=>'KO'));
    die($result);
}

// If we have to perform an owner change and we're not admin then forfait
// NOTE: Disabling the owner menu from javascript is fine but we need a function
// to check if logged user is an admin
if(isset($tagOwnerToSet) && !OC_User::isAdminUser(OC_User::getUser())) {
    $result = json_encode(array('result'=>'NOTALLOWED', 'newpriviledges' => '', 'newowner' => ''));
    die($result);
}

// Perform the requested operation
$ctags = new \OCA\OCLife\hTags();

$user = \OCP\User::getUser();
$tagOwner = $ctags->getTagOwner($tagID);

if($ctags->writeAllowed($tagID, $user) || $user === $tagOwner) {
    if(isset($priviledge)) {
        // Set priviledges
        $newPriviledges = $ctags->setTagPermission($tagID, $priviledge);
        $newOwner = '';
    } else {
        // Set owner
        $newOwner = $ctags->setTagOwner($tagID, $tagOwnerToSet);
        $newPriviledges = '';
    }

    $result = json_encode(array('result'=>'OK', 'newpriviledges' => $newPriviledges, 'newowner' => $newOwner));    
} else {
    $result = json_encode(array('result'=>'NOTALLOWED', 'newpriviledges' => '', 'newowner' => ''));
}

echo $result;