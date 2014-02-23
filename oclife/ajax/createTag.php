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

$ctags = new \OCA\OCLife\hTags();

$newTagID = $ctags->newTag($tagLang, $tagName, $parentID);

if($newTagID === FALSE) {
    echo 'KO-1';
} else {
    echo 'OK-' . $newTagID;
}