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

OCP\JSON::checkAppEnabled('oclife');
OCP\JSON::checkLoggedIn();

// Revert parameters from ajax
$fileID = intval(filter_input(INPUT_GET, 'fileid', FILTER_SANITIZE_NUMBER_INT));

// Get current user
$user = \OCP\User::getUser();

// Get file path
$filePath = \OC\Files\Filesystem::getPath($fileID);

// Build user's view path
$viewPath = '/' . $user . '/files';

// Build the placeholder's path - Image to show in case we don't have the thumbnail
$placeHolderPath = __DIR__ . '/img/noImage.png';

// Build thumb path
$previewPath = \OC_User::getHome($user) . '/oclife/previews/' . $user . $filePath;
$previewPathInfo = pathinfo($previewPath);
$previewDir = $previewPathInfo['dirname'];
$thumbPath = $previewPathInfo['dirname'] . '/' . $previewPathInfo['filename'] . '.png';

// Check and eventually prepare preview directory
if (!is_dir($previewDir)) {
        mkdir($previewDir, 0755, true);
}

// Check if thumbnail exist, create it otherwise
if(!file_exists($thumbPath)) {
    $imgHandler = new \OCA\OCLife\ImageHandler();
    $imgHandler->setHeight(320);
    $imgHandler->setWidth(320);
    $imgHandler->setBgColorFromValues(0, 0, 0);
    
    $imgHandler->generateImageThumbnail($viewPath, $filePath, $thumbPath);
}

// Output the preview
$previewPath = (is_file($thumbPath)) ? $thumbPath : $placeHolderPath;
$mtime = filemtime($previewPath);
$size = filesize($previewPath);
$mime = \OC_Helper::getMimetype($previewPath);

\OCP\Response::enableCaching();
\OCP\Response::setLastModifiedHeader($mtime);

header('Content-Length: ' . $size);
header('Content-Type: ' . $mime);

$fp = @fopen($previewPath, 'rb');
@fpassthru($fp);
