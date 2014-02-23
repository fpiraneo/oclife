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

/**
 * Handle all data into the masterDocuments collection
 * NOTE: All informations stored on the document will be defined in the 'fileExplorer' class.
 * The only constraint is the presence of 'tags' field.
 */

namespace OCA\OCLife;
class ImageHandler {
    private $handableImageType;
    private $width;
    private $height;
    private $destPath;
    private $bgColor;
    private $htmlBgColor;
    private $imagick;
    
    /**
     * Initialize Image data handler
     */
    function __construct() {
        // Check if ImageMagick is enabled; use standard GD otherwise
        $useImageMagick = intval(\OCP\Config::getAppValue('oclife', 'useImageMagick'));
        $this->imagick = extension_loaded('imagick') && $useImageMagick === 1;
        
        if($this->imagick) {
            $this->handableImageType = array('gif', 'jpeg', 'jpg', 'png', 'bmp', 'xbm', 'nef', 'cr2', 'tif', 'pcd');
        } else {
            $this->handableImageType = array('gif', 'jpeg', 'jpg', 'png', 'bmp', 'xbm');
        }
        
        // Set default background color as black
        $this->htmlBgColor = '#000000';
        $this->setColorFromHTML($this->htmlBgColor);
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getDestPath() {
        return $this->destPath;
    }

    public function setWidth($width) {
        $this->width = $width;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function setDestPath($destPath) {
        if(!file_exists($this->destPath)) {
            return FALSE;
        } else {        
            if(substr($destPath, -1) !== '/') {
                $destPath = $destPath . '/';
            }
            
            $this->destPath = $destPath;
            return TRUE;
        }
    }

    /**
     * Get actual image background color
     * @return array Array containing red, green and blue color level
     */
    public function getBgColor() {
        return $this->bgColor;
    }

    /**
     * Set actual background color starting from red, gren and blue levels
     * @param integer $red Red level (0...255)
     * @param integer $green Green level (0...255)
     * @param integer $blue Blue level (0...255)
     * @return boolean
     */
    public function setBgColorFromValues($red, $green, $blue) {
        if(!($this->checkColorLevel($red) && $this->checkColorLevel($green) && $this->checkColorLevel($blue))) {
            return FALSE;
        }
        
        $this->bgColor = array('red' => $red, 'green' => $green, 'blue' => $blue);
        return TRUE;
    }
    
    /**
     * Check if given level color is in correct range
     * @param type $level
     * @return type
     */
    private function checkColorLevel($level) {
        return ($level >= 0 && $level <= 255);
    }
    
    /**
     * Generate thumbs from an array of files path
     * @param array $srcFiles Source file array path
     * @return array Array of results, one for each file
     */
    public function generateThumbsFromArray($srcFiles) {
        $results = array();
        
        // Check source file
        if(!is_array($srcFiles)) {
            // Must be an array
            return FALSE;
        }
                
        // Process images
        foreach ($srcFiles as $file) {
            $resultCode = $this->generateImageThumbnail($file);
            
            $resRow['filename'] = $file;
            $resRow['result'] = $resultCode;
            
            $results[] = $resRow;
        }
        
        return $results;
    }
        
    /**
     * Return types of images that can handled
     * @return array
     */
    public function getHandableImageType() {
        return $this->handableImageType;
    }
    
    /**
     * Returns TRUE if image can be handled, FALSE otherwise
     * @param string $imageType Extension of image
     * @return boolean
     */
    public function isImageTypeHandable($imageData) {
        $imageType = substr($imageData['fileName'], -3);
        
        return in_array(trim(strtolower($imageType)), $this->handableImageType); 
    }

    /**
     * Check if a valid color has been set
     * @return boolean Return TRUE if background color is valid, FALSE otherwise
     */
    private function checkValidColor() {
        if(!is_array($this->bgColor)) {
            return FALSE;
        }
        
        if(count($this->bgColor) != 3) {
            return FALSE;
        }
        
        // All checks passed
        return TRUE;
    }
    
    /**
     * Set the actual background color starting from HTML value
     * @param string $color HTML color code
     * @return boolean true if ok, false otherwise
     */
    public function setColorFromHTML($color) {
        if(substr($color, 0, 1) != "#") {
            return false;
        }
        
        $red = hexdec(substr($color, 1, 2));
        $green = hexdec(substr($color, 3, 2));
        $blue = hexdec(substr($color, 5, 2));
        
        $this->setBgColorFromValues($red, $green, $blue);
        $this->htmlBgColor = $color;
        return true;
    }
    
    /**
     * Check and eventually generate thumbnail
     * @param string $srcImagePath
     * @return boolean True if exist or has been created successfully
     */
    public function checkThumbnail($srcImagePath) {
        $fileName = pathinfo($srcImagePath, PATHINFO_FILENAME) . '.png';
        $thumbPath = $this->destPath . $fileName;
        
        if(file_exists($thumbPath)) {
            return TRUE;
        } else {
            return $this->generateImageThumbnail($srcImagePath);
        }
    }
    
    /**
     * Get the thumbnail of the image
     * @param string $thumbImagePath
     */
    public function getThumbnail($thumbImagePath) {
        $fp = @fopen($thumbImagePath, 'rb');
        $mtime = filemtime($thumbImagePath);
        $size = filesize($thumbImagePath);
        $mime = \OC_Helper::getMimetype($thumbImagePath);

        if ($fp) {
            \OCP\Response::enableCaching();
            \OCP\Response::setLastModifiedHeader($mtime);
            header('Content-Length: ' . $size);
            header('Content-Type: ' . $mime);

            fpassthru($fp);
        } else {
            \OC_Response::setStatus(\OC_Response::STATUS_NOT_FOUND);
        }        
    }
    
    /**
     * Generate thumbnail of an image
     * @param string $srcImagePath Source image path
     * @param string $dstImagePath Destination image path
     * @return boolean TRUE image generated successfully, FALSE otherwise
     */
    public function generateImageThumbnail($viewPath, $srcImagePath, $dstImagePath) {
        // Check for a valid color set
        if(!$this->checkValidColor()) {           
             return FALSE;
        }

        // Check for a valid width and height
        if(!isset($this->width) || !isset($this->height)) {
            return FALSE;
        }

        if($this->width < 10 || $this->height < 10) {
            return FALSE;
        }
        
        if($this->imagick) {
            $result = $this->generateImageThumbnailIM($viewPath, $srcImagePath, $dstImagePath);
        } else {
            $result = $this->generateImageThumbnailGD($viewPath, $srcImagePath, $dstImagePath);
        }
        
        return $result;
    }

    /**
     * Generate thumbnail of an Image with GD
     * @param string $viewPath Source view path
     * @param string $srcImagePath Source image path relative to the ownCloud fakeroot
     * @param string $dstImagePath Destination image path
     * @return boolean TRUE image generated successfully, FALSE otherwise
     */
   private function generateImageThumbnailGD($viewPath, $srcImagePath, $dstImagePath)
   {
        $view = new \OC\Files\View($viewPath);
        $handle = $view->fopen($srcImagePath, 'r');
        $image = new \OCP\Image($handle);
        fclose($handle);

        if (!$image->valid()) {
            return FALSE;
        }
        
        $image->fixOrientation();
        $image->resize($this->width);

        $imageRsrc = $image->resource();

        $height = $image->height();
        $width = $image->width();

        $widthOffset = intval(($this->width - $width) / 2);
        $heightOffset = intval(($this->height - $height) / 2);

        $thumbGDImage = imagecreatetruecolor($this->width, $this->height);

        // Fill with background color
        $bgColor = imagecolorallocate($thumbGDImage, $this->bgColor['red'], $this->bgColor['green'], $this->bgColor['blue']);
        imagefilledrectangle($thumbGDImage, 0, 0, $this->width, $this->height, $bgColor);

        imagecopyresampled($thumbGDImage, $imageRsrc, $widthOffset, $heightOffset, 0, 0, $width, $height, $width, $height);

        imagepng($thumbGDImage, $dstImagePath, 7);
        imagedestroy($thumbGDImage);

        return TRUE;       
   }

    /**
     * Generate thumbnail of an Image with ImageMagick
     * @param string $viewPath Source view path
     * @param string $srcImagePath Source image path relative to the ownCloud fakeroot
     * @param string $dstImagePath Destination image path
     * @return boolean TRUE image generated successfully, FALSE otherwise
     */
   private function generateImageThumbnailIM($viewPath, $srcImagePath, $dstImagePath)
   {
        $view = new \OC\Files\View($viewPath);
        $handle = $view->fopen($srcImagePath, 'r');
        
        // In case we was unable to open the file, forfait
        if($handle === FALSE) {
            return FALSE;
        }
        
        try {
            $imageHandler = new \Imagick();
            $imageHandler->readimagefile($handle);
        } catch (Exception $exc) {
            return FALSE;
        }

        // Get number of images and choose the best for the resolution of the thumbnail
        //$imgsNumber = $imageHandler->getnumberimages();
        $imageHandler->setiteratorindex(0);
        
        // Compute aspect ratio
        $srcImgGeometry = $imageHandler->getImageGeometry();
        $srcAspectRatio = $srcImgGeometry['width'] / $srcImgGeometry['height'];
       
        $thumb_aspect_ratio = $this->width / $this->height;
       
        if ($srcImgGeometry['width'] <= $this->width && $srcImgGeometry['height'] <= $this->height) {
            $thumbImageWidth = $srcImgGeometry['width'];
            $thumbImageHeight = $srcImgGeometry['height'];
        } elseif ($thumb_aspect_ratio > $srcAspectRatio) {
            $thumbImageWidth = intval($this->height * $srcAspectRatio);
            $thumbImageHeight = $this->height;
        } else {
            $thumbImageWidth = $this->width;
            $thumbImageHeight = intval($this->width / $srcAspectRatio);
        }
       
        // Create new image structure
        $verticalOffset = intval(($this->height - $thumbImageHeight) / 2);
        $horizontalOffset = intval(($this->width - $thumbImageWidth) / 2);
       
        // Copy and save generated thumbnail
        $imageHandler->thumbnailImage($thumbImageWidth, $thumbImageHeight);
        $imageHandler->borderimage($this->htmlBgColor, $horizontalOffset, $verticalOffset);
        
        $imageHandler->setImageFormat('png');
        
        try{
            $imageHandler->writeimage($dstImagePath);
        } catch(Exception $ex) {
            return FALSE;
        }

        return TRUE;
   }
}
