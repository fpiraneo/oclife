<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of configHandler
 *
 * @author fpiraneo
 */
class configHandler {
    private $configPath;
    private $configData;
    private $configValid;
    
    function __construct($configPath) {
        $this->configPath = $configPath;
        $this->configData = array();
        
        if(is_readable($this->configPath)) {
            $configRef = fopen($this->configPath, "r");
            
            if($configRef === FALSE) {
                $this->configValid = FALSE;
            } else {
                $this->configValid = TRUE;

                while ($rawLine = fgets($configRef)) {
                    $line = trim($rawLine);
                    
                    $validLine = substr($line, 0, 1) != "#" & $line != '';
                    
                    if($validLine) {
                        $configElements = explode("=", $line);
                        if(count($configElements) == 2) {
                            $this->configData[trim($configElements[0])] = trim($configElements[1]);
                        }
                    }
                }                
            }
            
            // Check and eventually create directory structure
            $this->createDirStructure();
        }
    }

    public function __toString() {
        $result = "";
        
        foreach ($this->configData as $key => $value) {
            $result .= sprintf("%s = %s\n", $key, $value);
        }
        
        return $result;
    }

    /**
     * Return a value of a property
     * @param type $propName
     * @return String
     */
    public function getProperty($propName) {
        if(isset($this->configData[$propName])) {
            return $this->configData[$propName];
        } else {
            return FALSE;
        }
    }

    public function getArrayFromList($propName) {
        $propValue = $this->getProperty($propName);
        
        if($propValue === FALSE) {
            return FALSE;
        } else {
            $propParts = explode(',', $propValue);
            
            $result = array();
            foreach($propParts as $value) {
                $result[] = trim($value);
            }
        }
        
        return result;
    }
    
    /**
     * Create full directory structure
     */
    private function createDirStructure() {
        // Document paht
        $myPath = $this->getProperty('DataDir');
        if(!is_dir($myPath)) {
            $result = mkdir($myPath, 0700, TRUE);
        } else {
            $result = TRUE;
        }
        
        if(!$result) {
            return 1;
        }
        
        // Thumbnail path
        $myPath .= $this->getProperty('thumbDir');
        if(!is_dir($myPath)) {
            $result = mkdir($myPath, 0700, TRUE);
        } else {
            $result = TRUE;
        }
        
        if(!$result) {
            return 2;
        }
        
        // Incoming path
        $myPath = $this->getProperty('IncomingDir');
        if(!is_dir($myPath)) {
            $result = mkdir($myPath, 0777, TRUE);
        } else {
            $result = TRUE;
        }
        
        if(!$result) {
            return 3;
        }
        
        // Everything good
        return 0;
    }
}
