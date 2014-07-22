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
$filePath = filter_input(INPUT_GET, 'filePath', FILTER_SANITIZE_STRING);

// Get current user
$user = \OCP\User::getUser();

// Build user's view path
$viewPath = '/' . $user . '/files';

// Build the placeholder's path - Image to show in case we don't have the thumbnail
$placeHolderPath = __DIR__ . '/img/noImage.png';

// Build thumb path
if(isset($filePath)) {
	$filePathInfo = pathinfo($filePath);
	$previewPath = \OC_User::getHome($user) . '/oclife/previews/' . $user;
	$previewDir = $previewPath . $filePathInfo['dirname'];
	$thumbPath = $previewPath . $filePathInfo['dirname'] . '/tmp_' . time() . '.png';

	// Check and eventually prepare preview directory
	if(!is_dir($previewDir)) {
		mkdir($previewDir, 0755, true);
	}

	// Check if thumbnail exist, create it otherwise
	$imgHandler = new \OCA\OCLife\ImageHandler();
	$imgHandler->setHeight(800);
	$imgHandler->setWidth(600);
	$imgHandler->setBgColorFromValues(0, 0, 0);

	$imgHandler->generateImageThumbnail($viewPath, $filePath, $thumbPath);

	$previewPath = (is_file($thumbPath)) ? $thumbPath : $placeHolderPath;
} else {
	$previewPath = $placeHolderPath;
}

// Output the preview
$mtime = filemtime($previewPath);
$size = filesize($previewPath);
$mime = \OC_Helper::getMimetype($previewPath);

\OCP\Response::enableCaching();
\OCP\Response::setLastModifiedHeader($mtime);

header('Content-Length: ' . $size);
header('Content-Type: ' . $mime);

$fp = @fopen($previewPath, 'rb');
@fpassthru($fp);

// Remove preview file once used
if(is_file($thumbPath)) {
	unlink($thumbPath);
}
