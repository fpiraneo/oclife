<?php

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
$fp = @fopen($previewPath, 'rb');
$mtime = filemtime($previewPath);
$size = filesize($previewPath);
$mime = \OC_Helper::getMimetype($previewPath);

\OCP\Response::enableCaching();
\OCP\Response::setLastModifiedHeader($mtime);
header('Content-Length: ' . $size);
header('Content-Type: ' . $mime);

fpassthru($fp);
