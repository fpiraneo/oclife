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

require __DIR__ . '/libs/imageHandler.php';

// Revert parameters from ajax
$fileID = intval(filter_input(INPUT_GET, 'fileid', FILTER_SANITIZE_NUMBER_INT));

// Get current user
$user = OCP\User::getUser();

// Get file path
$filePath = \OC\Files\Filesystem::getPath($fileID);

// prepend path to share
$ownerView = new \OC\Files\View('/' . $user . '/');

$myDir = \OC_User::getHome($user);

// Build source path
$imgPath = $myDir . '/files' . $filePath;

// Build thumb path
$previewPath = \OC_User::getHome($user) . '/oclife/previews/' . $user . $filePath;

// Build the placeholder's path - Image to show in case we don't have the thumbnail
$placeHolderPath = __DIR__ . '/img/noImage.png';

// Get information about the path
$previewPathInfo = pathinfo($previewPath);
$previewDir = $previewPathInfo['dirname'];

// Check and eventually prepare preview directory
if (!is_dir($previewDir)) {
        mkdir($previewDir, 0755, true);
}

// Check if thumbnail exist, create it otherwise
$imgHandler = new \oclife\imagehandler\ImageHandler();

if(!file_exists($previewPath)) {
    // Create thumbnail
    $imgHandler->setHeight(320);
    $imgHandler->setWidth(320);
    $imgHandler->setBgColorFromValues(255, 255, 255);
    
    $view = new \OC\Files\View('/' . $user . '/files');
    $handle = $view->fopen($filePath, 'r');
    $image = new \OCP\Image($handle);
    fclose($handle);
    
    if ($image->valid()) {
        $image->fixOrientation();
        $image->resize(320);
        $image->save($previewPath);
    }
}

// Output the preview
$previewPath = (is_file($previewPath)) ? $previewPath : $placeHolderPath;
$fp = @fopen($previewPath, 'rb');
$mtime = filemtime($previewPath);
$size = filesize($previewPath);
$mime = \OC_Helper::getMimetype($previewPath);

\OCP\Response::enableCaching();
\OCP\Response::setLastModifiedHeader($mtime);
header('Content-Length: ' . $size);
header('Content-Type: ' . $mime);

fpassthru($fp);
