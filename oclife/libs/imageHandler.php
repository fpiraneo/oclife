<?php

/**
 * Handle all data into the masterDocuments collection
 * NOTE: All informations stored on the document will be defined in the 'fileExplorer' class.
 * The only constraint is the presence of 'tags' field.
 *
 * @author fpiraneo
 */

namespace oclife\imagehandler;
require __DIR__ . '/../libs/exifHandler.php';

class ImageHandler {
    private $handableImageType;
    private $width;
    private $height;
    private $destPath;
    private $bgColor;
    
    /**
     * Initialize Image data handler
     * @param type $dbName Full name of database
     * @param type $connection Reference of db connection
     */
    function __construct() {
        $this->handableImageType = array('gif', 'jpeg', 'jpg', 'png');
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
        
        $this->bgColor = array($red, $green, $blue);
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
     * Generate thumbnail of an Image with GD
     * Based on a function taken at:
     * http://salman-w.blogspot.com/2008/10/resize-Images-using-phpgd-library.html
     * @param string $srcImagePath Source image path
     * @param string $dstImagePath Destination image path
     * @return boolean TRUE image generated successfully, FALSE otherwise
     */
   public function generateImageThumbnail($srcImagePath, $dstImagePath)
   {
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
       
       if(!is_readable($srcImagePath)) {
           return FALSE;
       }
       
       // Here begins the job!
       $info = \OCP\Image::getMimeTypeForFile($filePath);
       $imageInfo = getimagesize($srcImagePath);
       list($srcImageWidth, $srcImageHeight, $srcImageType) = $imageInfo;

    /*
     * string Image_type_to_mime_type ( int $Imagetype )
     *   Imagetype           Returned value
         IMAGETYPE_GIF 	Image/gif
         IMAGETYPE_JPEG 	Image/jpeg
         IMAGETYPE_PNG 	Image/png
         IMAGETYPE_SWF 	application/x-shockwave-flash
         IMAGETYPE_PSD 	Image/psd
         IMAGETYPE_BMP 	Image/bmp
         IMAGETYPE_TIFF_II (intel byte order) 	Image/tiff
         IMAGETYPE_TIFF_MM (motorola byte order) 	Image/tiff
         IMAGETYPE_JPC 	application/octet-stream
         IMAGETYPE_JP2 	Image/jp2
         IMAGETYPE_JPX 	application/octet-stream
         IMAGETYPE_JB2 	application/octet-stream
         IMAGETYPE_SWC 	application/x-shockwave-flash
         IMAGETYPE_IFF 	Image/iff
         IMAGETYPE_WBMP 	Image/vnd.wap.wbmp
         IMAGETYPE_XBM 	Image/xbm
         IMAGETYPE_ICO 	Image/vnd.microsoft.icon
     */
       
       switch ($srcImageType) {
           case IMAGETYPE_GIF:
               $srcGDImage = imagecreatefromgif($srcImagePath);
               break;
           case IMAGETYPE_JPEG:
               $srcGDImage = imagecreatefromjpeg($srcImagePath);
               break;
           case IMAGETYPE_PNG:
               $srcGDImage = imagecreatefrompng($srcImagePath);
               break;
           
           default :
               return FALSE;
       }

       if ($srcGDImage === false) {
           return FALSE;
       }
       
       // Determine EXIF rotation of image
       $exifData = new \oclife\exif\exifHandler($srcImagePath);
       $srcGDImage = imagerotate($srcGDImage, $exifData->getRotation(), 0);
       
       // If image is rotated, swap width and height
       if(abs($exifData->getRotation()) == 90) {
           $srcImageWidth;
           $srcImageHeight;
           list($srcImageWidth, $srcImageHeight) = array($srcImageHeight, $srcImageWidth);
        }
       
       // Compute aspect ratio
       $srcAspectRatio = $srcImageWidth / $srcImageHeight;
       
       $thumb_aspect_ratio = $width / $height;
       
       if ($srcImageWidth <= $width && $srcImageHeight <= $height) {
           $thumbImageWidth = $srcImageWidth;
           $thumbImageHeight = $srcImageHeight;
       } elseif ($thumb_aspect_ratio > $srcAspectRatio) {
           $thumbImageWidth = intval($height * $srcAspectRatio);
           $thumbImageHeight = $height;
       } else {
           $thumbImageWidth = $width;
           $thumbImageHeight = intval($width / $srcAspectRatio);
       }
       
        // Create new image structure
        $verticalOffset = intval(($height - $thumbImageHeight) / 2);
        $horizontalOffset = intval(($width - $thumbImageWidth) / 2);

        $thumbGDImage = imagecreatetruecolor($width, $height);
       
        // Fill with background color
        $bgColor = imagecolorallocate($thumbGDImage, $this->bgColor['red'], $this->bgColor['green'], $this->bgColor['blue']);
        imagefilledrectangle($thumbGDImage, 0, 0, $thumbImageWidth, $thumbImageHeight, $bgColor);

        // Copy and save generated thumbnail
        $fileName = pathinfo($srcImagePath);        
        
        $thumbImagePath = $fileName['dirname'] . $fileName['filename'] . '.png';
        imagecopyresampled($thumbGDImage, $srcGDImage, $horizontalOffset, $verticalOffset, 0, 0, $thumbImageWidth, $thumbImageHeight, $srcImageWidth, $srcImageHeight);        
        imagepng($thumbGDImage, $thumbImagePath, 2);

        // Final cleanups
        imagedestroy($srcGDImage);
        imagedestroy($thumbGDImage);

        return TRUE;
   }
}
