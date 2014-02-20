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

$ctags = new OCA\OCLife\hTags();
$tagData = $ctags->getAllTags('xx');

$searchKey = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_STRING);

$result = array();

foreach($tagData as $tag) {
    if(is_null($searchKey) || $searchKey === FALSE || $searchKey === '') {
        $result[] = new OCA\OCLife\tag($tag['tagid'], $tag['descr']);
    } else {
        if(strpos($tag['descr'], $searchKey) !== FALSE) {
            $result[] = new OCA\OCLife\tag($tag['tagid'], $tag['descr']);
        }
    }
}

$jsonTagData = json_encode((array) $result);
echo $jsonTagData;