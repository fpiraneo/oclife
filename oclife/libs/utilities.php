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
    public static function cleanupForDelete($params) {
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
    
    /**
     * Get all files ID of the indicated user
     * @param string $user Username
     * @return array ID of all the files
     */
    public static function getFileList($user) {
        $result = array();
        
        $userStorage = 'home::' . $user;
        $sql = 'SELECT numeric_id FROM *PREFIX*storages WHERE id=?';
        $args = array($userStorage);
        $query = \OCP\DB::prepare($sql);
        $resRsrc = $query->execute($args);
        
        while($row = $resRsrc->fetchRow()) {
            $storages[] = intval($row['numeric_id']);
        }        
        
        // At least one storage needed
        if(count($storages) === 0) {
            return -1;
        }
        
        // For each storage, get the files data (fileid, path)
        foreach($storages as $storageID) {
            $sql = 'SELECT fileid, path, name FROM *PREFIX*filecache WHERE storage=?';
            $args = array($storageID);
            $query = \OCP\DB::prepare($sql);
            $resRsrc = $query->execute($args);

            while($row = $resRsrc->fetchRow()) {
                $fileid = intval($row['fileid']);
                $filepath = $row['path'];
                $filename = $row['name'];
                
                $result[$fileid] = array('id' => $fileid, 'path' => $filepath, 'name' => $filename);
            }
        }
        
        return $result;
    }
    
    /**
     * Return the files info (id, name and path) for a given file(s) id
     * @param string $user Username
     * @param array $filesID IDs of the file to look at
     * @return array Associative array with required infos
     */
    public static function getFileInfoFromID($user, $filesID) {
        if(!is_array($filesID)) {
            return -1;
        }
        
        $emptyFile = array('id'=>'', 'path'=>'', 'name'=>'');
        $usersFile = utilities::getFileList($user);
        
        if($usersFile === -1) {
            return -2;
        }
        
        // Loop through the provided file ID and return all result
        $result = array();
        
        foreach($filesID as $fileID) {
            if(isset($usersFile[$fileID])) {
                $result[$fileID] = $usersFile[$fileID];
            } else {
                $result[$fileID] = $emptyFile;
            }
        }
        
        return $result;
    }
    
    /**
     * Prepare an image tile
     * @param array $fileData File data
     * @return string
     */
    public static function prepareTile($fileData) {
        // $fileData = array('id'=>'', 'path'=>'', 'name'=>'')
        $result = '<div>';
        $result .= '<div>' . $fileData['name'] . '</div>';
        $result .= '</div>';
        
        return $result;
    }
}
