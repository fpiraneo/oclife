<?php

/**
 * Main operations
 *
 * @author fpiraneo
 */
class life {
    
    /**
     * Master DB interface
     * @var type 
     */
    private $db;
    
    /**
     * Configuration handler
     * @var type 
     */
    private $config;
    
    /**
     * File explorer
     * @var type 
     */
    private $explorer;

    /**
     * Tags handler
     * @var tagsHandler 
     */
    private $tags;
    
    /**
     * Images handler
     * @var imageHandler 
     */
    private $images;
    
    /**
     * Full DB name where informations are stored
     * @var String
     */
    private $dbName;
    
    /**
     * Constructor - Initialize basic objects like:
     * - db access;
     * - reads configurations
     * - tags handler;
     * - full DB name
     * @var String Full path of configuration file
     */
    function __construct($configFilePath) {
        // Open configuration first
        $this->config = new configHandler($configFilePath);

        // Get DB name
        $this->dbName = $this->config->getProperty('dbPrefix') . 'lifeDB';
        
        // Open what's needed now
        $this->db = new masterDBInterface($this->config->getProperty('dbUsername'), $this->config->getProperty('dbPasswd'), $this->config->getProperty('dbHost'), $this->config->getProperty('dbPort'), $this->config->getProperty('dbPrefix'));
        $this->tags = new tagsHandler($this->dbName, $this->db->getConnection());
        $this->images = new imageHandler($this->dbName, $this->db->getConnection());
    }

    /**
     * Check for new files into loading directory and optionally load it into data base
     * @param Boolean $loadInDB TRUE loads found files into database and moves file from loading dir into data dir
     * @return array Full data of found / inserted file
     */
    function checkForNewFiles($loadInDB) {
        $explorer = new fileExplorer();
        
        $filesInIncoming = $explorer->browseDirectory($this->config->getProperty('IncomingDir'), TRUE, TRUE);
        
        if($loadInDB) {
            $insertionResult = array();
            $thumbBorderColor = $this->images->generateColorFromHTML($this->config->getProperty('thumbsBorderColor'));
            $dataDir = $this->config->getProperty('DataDir');
            $thumbDir = $this->config->getProperty('DataDir') . $this->config->getProperty('thumbDir');
            $thumbWidth = $this->config->getProperty('thumbsWidth');
            $thumbHeight = $this->config->getProperty('thumbsHeight');

            foreach($filesInIncoming as $fileToHandle) {
                $filesInserted = $this->images->doInsert($fileToHandle);
                
                if($filesInserted[0]['result']['doInsert']['code'] == 0) {
                    $thumbGenerated = $this->images->generateThumbsFromArray($filesInserted, $thumbDir, $thumbWidth, $thumbHeight, $thumbBorderColor);
                    $fileInsertionResult = $explorer->moveInDataDir($thumbGenerated, $dataDir);
                    $insertionResult[] = $fileInsertionResult[0];
                }
            }

            return $insertionResult;
        } else {
            return $filesInIncoming;
        }
        
    }

    /**
     * Returns actual configHandler object
     * @return configHandler
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Returns actual tagsHandler object
     * @return tagsHandler
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Returns actual imageHandler object
     * @return imageHandler
     */
    public function getImages() {
        return $this->images;
    }

}
