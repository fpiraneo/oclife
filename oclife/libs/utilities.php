<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of utilities
 *
 * @author fpiraneo
 */

namespace OCA\OCLife;

class utilities {
    /**
     * Format a file size in human readable form
     * @param integer $bytes File size in bytes
     * @param integer $precision Decimal digits (default: 2)
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2, $addOriginal = FALSE) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

        $dimension = max($bytes, 0); 
        $pow = floor(($dimension ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 

        $dimension /= pow(1024, $pow);

        $result = round($dimension, $precision) . ' ' . $units[$pow];
        
        if($addOriginal === TRUE) {
            $result .= sprintf(" (%s bytes)", number_format($bytes));
        }
        
        return $result;
    }
    
    /**
     * Remove thumbnails and db entries for deleted files
     * @param type $params
     */
    
    static public function cleanupForDelete($params) {
        // Get full thumbnail path
        $path = $params['path'];
        $user = \OCP\USER::getUser();
        $previewDir = \OC_User::getHome($user) . '/oclife/previews/' . $user;
        $thumbPath = $previewDir . $path;
        
        // If thumbnail exists remove it
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }

        // Now remove all entry in DB for this file
        // -- Verificare che qui esista l'entry del file nel DB!!! :-///
        $fileInfos = \OC\Files\Filesystem::getFileInfo($path);
        if($fileInfos['fileid']) {
            $result = \OCA\OCLife\hTags::removeAllTagsForFile($fileInfos['fileid']);
        }
        return $result;
    }
    
}
