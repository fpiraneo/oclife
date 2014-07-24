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

if($tagID === FALSE || $priviledge === FALSE) {
    die('KO');
}

$ctags = new \OCA\OCLife\hTags();

$user = \OCP\User::getUser();
$tagOwner = $ctags->tagOwner($tagid);

if($ctags->writeAllowed($tagid, $user) || $user === $tagOwner) {
	$ctags->setTagPermission($tagID, $priviledge);

	echo 'OK';
} else {
	echo 'NOTALLOWED';
}

