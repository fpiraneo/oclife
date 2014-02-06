<?php

/**
 * Access owncloud data base
 *
 * @author fpiraneo
 */

namespace oclife;

class queryOCDB {
    /**
     * get files on ownCloud and return data on array
     */
    public function getFiles($fileSystem) {
        $result = array();
        
        // *PREFIX* is being replaced with the ownCloud installation prefix
        $sql = file_get_contents(__DIR__ . '/filesQuery.sql');
        $args = array($fileSystem);
        
        $query = \OCP\DB::prepare($sql);
        $resRsrc = $query->execute($args);
        while($row = $resRsrc->fetchRow()) {
            $result[] = $row;
        }
        
        return $result;
    }
}
