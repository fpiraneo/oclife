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
 * EXIF related utilities
 *
 * @author fpiraneo
 */

namespace \OCA\OCLife;

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
