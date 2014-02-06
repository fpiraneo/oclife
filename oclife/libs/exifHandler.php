<?php

/**
 * EXIF related utilities
 *
 * @author fpiraneo
 */

namespace oclife\exif;

class exifHandler {
    private $imagePath;
    private $exifData;
    
    /**
     * Read image exif data
     * @param string $imagePath Path of the image
     * @throws Exception
     */
    function __construct($imagePath) {
        $this->imagePath = $imagePath;
        
        if(is_readable($this->imagePath)) {       
            $this->exifData = exif_read_data($this->imagePath, 0, true);
        } else {
            throw new Exception('Provided path is not accessible - Check permissions');
        }
    }

    /**
     * Return actual rotation of image
     * @return int Degrees of rotation
     */
    public function getRotation() {
      if(!empty($this->exifData['IFD0']['Orientation'])) { 
            switch($this->exifData['IFD0']['Orientation']) { 
                case 8: 
                    return 90;
                case 3: 
                    return 180;
                case 6: 
                    return -90;
                default :
                    return 0;
            }           
        }

        return 0;
    }

    /**
     * Return actual EXIF data array
     * @return string
     */
    public function getExifData() {
        return $this->exifData;
    }


    

}
