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
// Check if app enabled and user logged in
\OCP\JSON::checkAppEnabled('oclife');
\OCP\User::checkLoggedIn();

// Handle translations
$l = new \OC_L10N('oclife');

// Revert parameters from ajax
$filePath = filter_input(INPUT_POST, 'filePath', FILTER_SANITIZE_STRING);

// Check if multiple file has been choosen
if(substr($filePath, -1) === '/') {
    $thumbPath = OCP\Util::linkToAbsolute('oclife', 'getThumbnail.php', array('filePath' => $filePath));
    $preview = '<img style="border: 1px solid black; display: block;" src="' . $thumbPath . '" />';
    
    $infos = '<strong>' . $l->t('Multiple files selected') . '</strong>';

    $result = array('preview' => $preview, 'infos' => $infos, 'fileid' => -1);

    print json_encode($result);
    die();
}

// Begin to collect files informations
/*
 *  $fileInfos contains:
 * Array ( [fileid] => 30 
 * [storage] => home::qsecofr 
 * [path] => files/Immagini/HungryIla.png 
 * [parent] => 18 
 * [name] => HungryIla.png 
 * [mimetype] => image/png 
 * [mimepart] => image 
 * [size] => 3981786 
 * [mtime] => 1388521137 
 * [storage_mtime] => 1388521137 
 * [encrypted] => 1 
 * [unencrypted_size] => 3981786 
 * [etag] => 52c326b169ba4
 * [permissions] => 27 ) 
 */
$fileInfos = \OC\Files\Filesystem::getFileInfo($filePath);

$thumbPath = OCP\Util::linkToAbsolute('oclife', 'getThumbnail.php', array('filePath' => $filePath));
$preview = '<img style="border: 1px solid black; display: block;" src="' . $thumbPath . '" />';

$infos = array();
$infos[] = '<strong>' . $l->t('File name') . ': </strong>' . $fileInfos['name'];
$infos[] = '<strong>MIME: </strong>' . $fileInfos['mimetype'];
$infos[] = '<strong>' . $l->t('Size') . ': </strong>' . \OCA\OCLife\utilities::formatBytes($fileInfos['size'], 2, TRUE);
$infos[] = '<strong>' . $l->t('When added') . ': </strong>' . \OCP\Util::formatDate($fileInfos['storage_mtime']);
$infos[] = '<strong>' . $l->t('Encrypted? ') . '</strong>' . (($fileInfos['encrypted'] === TRUE) ? $l->t('Yes') : $l->t('No'));

if($fileInfos['encrypted']) {
    $infos[] = '<strong>' . $l->t('Unencrypted size') . ': </strong>' . \OCA\OCLife\utilities::formatBytes($fileInfos['unencrypted_size'], 2, TRUE);
}

// Output the result!
$htmlInfos = implode('<br />', $infos);

$result = array('preview' => $preview, 'infos' => $htmlInfos, 'fileid' => $fileInfos['fileid']);

print json_encode($result);
