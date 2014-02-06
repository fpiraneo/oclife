<?php

/**
 * Handle hierarchical structure of tags; in DB the structure of tag will be:
 * array(
 *      _id=>ObjectId('01234abcd'),
 *      humanReadable=>array(
 *                     'EN'=>'Tag description',
 *                     'IT'=>'Descrizione della tag',
 *                     'FR'=>'Description de la tag'
 *                 )
 *       child=>array(
 *                       '12345',
 *                       'abcde'
 *                   )
 * )
 *
 * @author fpiraneo
 */
class tagsHandler {
    private $dbName;
    private $connection;    
    private $colName;
    
    /**
     * Constructor
     * @param type $dbName
     * @param type $connection
     */
    function __construct($dbName, $connection) {
        $this->dbName = $dbName;
        $this->connection = $connection;
        
        $this->colName = "tagsTree";
        
        // Check if 'root' tag exists on db, create it otherwise
        if($this->searchTag('_root', '_root') == NULL) {
            $this->newTag('_root', '_root');
        } 
    }
    
    /**
     * Returns the tag ID of an existing tag
     * @param type $tagDescr may be an array
     * @param type $tagLang language code (EN, IT, FR, ...)
     */
    public function searchTag($tagDescr, $tagLang) {
        if(is_array($tagDescr)) {
            $parameters = array("humanReadable.$tagLang" => array('$in'=>$tagDescr));
        } else {
            $parameters = array(
                'humanReadable' => array(
                    $tagLang=>$tagDescr
                    )
                );
        }
            
        $coll = $this->connection->selectCollection($this->dbName, $this->colName);

        $cursor = $coll->find($parameters);

        if($cursor->count() == 0) {
            return NULL;
        } 

        $result = array();

        foreach($cursor as $id => $data) {
            $result[] = $data;
        }

        return $result;
    }
    
    /**
     * Search a tag from it's ID
     * @param type $tagID
     * @return MongoCursor
     */
    public function searchTagFromID($tagID) {
        $parameters = array('_id' => new MongoId($tagID));
        
        $coll = $this->connection->selectCollection($this->dbName, $this->colName);

        $cursor = $coll->find($parameters);

        if($cursor->count() == 0) {
            return NULL;
        }
        
        $result = $cursor->getNext();

        return $result;
    }

    /**
     * Create a new tag with the provided parameters - Return the tag ID
     * @param type $tagDescr
     * @param type $tagLang
     */
    public function newTag($tagDescr, $tagLang) {
        $coll = $this->connection->selectCollection($this->dbName, $this->colName);

        $document = array(
            'humanReadable' => array(
                $tagLang=>$tagDescr
            ),
            'child' => array()
        );

        $coll->insert($document, array('safe'=>true));

        // If we are creating a tag other than "_root",
        // as default set current tag as a child of 'root'
        if($tagDescr != '_root' && $tagLang != '_root') {
            $this->setTagChild('_root', $tagDescr, $tagLang);
        }
    }

    /**
     * Alter data of an existing tag
     * @param type $tagId
     * @param type $tagData
     * @return type
     */
    public function alterTag($data) {
        if(!is_array($data)) {
            throw new Exception('Provided parameters must be an array.');
        }

        $coll = $this->connection->selectCollection($this->dbName, $this->colName);
        $coll->update(array('_id'=>$data['_id']), $data);

        return !$this->errState;        
    }
    
    /**
     * Returns an array of all existing tags
     * @param array $tagLang
     */
    public function getAllTags($tagLang) {
        $coll = $this->connection->selectCollection($this->dbName, $this->colName);

        $parameters = array('_id'=>TRUE, $tagLang=>TRUE);
        $cursor = $coll->find();
        $cursor->fields($parameters);

        if(count($cursor) == 0) {
            return NULL;
        } 

        return $cursor;
    }
    
    /**
     * Returns tags starting with given string
     * @param type $tagLang
     * @param type $startTag
     * @return array
     */
    public function getTag($tagLang, $startTag) {
        if($startTag == NULL) {
            return $this->getAllTags($tagLang);
        }
        
        $coll = $this->connection->selectCollection($this->dbName, $this->colName);

        $parameters = array('_id'=>TRUE, $tagLang=>TRUE);
        $cursor = $coll->find(array(''=>new MongoRegex("/" . $startTag . "\A/i")));
        $cursor->fields($parameters);

        if(count($cursor) == 0) {
            return NULL;
        } 

        return $cursor;        
    }
    
    /**
     * Return a tree array with all the tags starting from $startTag
     * @param String $startTag Tag description to start the tree
     * @param String $tagLang Tag language of $startTag
     * @return array
     */
    public function getTagTree($startTag, $tagLang) {
        if($tagLang == '' && $startTag == '') {
            $searchTagDescr = '_root';
            $searchTagLang = '_root';
        } else {
            $searchTagDescr = $startTag;
            $searchTagLang = $tagLang;            
        }
        
        $startTag = $this->searchTag($searchTagDescr, $searchTagLang);
        
        if(count($startTag) != 1) {
            throw new Exception('Invalid result number returned looking for tree start tag data.');
        }
        
        $result = $this->getTagTreeFromID((String) $startTag[0]['_id'], $tagLang);
        
        return $result;
    }
    
    /**
     * Get the tag tree starting from tag with given ID
     * @param string $ID Tag ID where to start the tree
     * @param string $lang Language of the human readable field
     * @return string The tag tree result
     * @throws Exception Occours when multiple tags with same descriptions are found
     */
    public function getTagTreeFromID($ID, $lang) {
        if($ID == NULL) {
            $rootTagData = $this->searchTag('_root', '_root');
            $searchID = (String) $rootTagData[0]['_id'];
        } else {
            $searchID = $ID;
        }
        
        $tagData = $this->searchTagFromID($searchID);
        
        // Insert ID of tag as key
        $result['_id'] = $ID;
        
        // Populate human readable
        $result['humanReadable'] = $tagData['humanReadable'][$lang];
        
        // Populate tag childs
        $tagChilds = array();
        
        foreach ($tagData['child'] as $id => $data) {
            $tagChilds[] = $this->getTagTreeFromID($id, $lang);
        }
        
        $result['child'] = $tagChilds;
        
        return $result;
    }

    /**
     * Set the child of a tag
     * @param String $parent Plain language (EN, IT, FR, ...) description of parent tag
     * @param String $child Plain language (EN, IT, FR, ...) description of child tag
     * @param String $lang Language ID (EN, IT, FR, ...) of both tag - Exception '_root' tag only usable as $parent tag
     */
    public function setTagChild($parent, $child, $lang) {
        $coll = $this->connection->selectCollection($this->dbName, $this->colName);

        $parentLang = ($parent == '_root') ? '_root' : $lang;
        
        $parentData = $this->searchTag($parent, $parentLang);
        if($parentData == NULL || count($parentData) > 1) {
            throw new Exception("Bad or no parent data found - $parent");
        }

        $childData = $this->searchTag($child, $lang);
        if($childData == NULL || count($childData) > 1) {
            throw new Exception("Bad or no child data found - $child");
        }

        $childID = (String) $childData[0]['_id'];
        $parentData[0]['child'][$childID] = TRUE;

        $coll->update(array('_id'=>$parentData[0]['_id']), $parentData[0]);

        return TRUE;                
    }
    
    /**
     * Remove the child of a tag
     * @param String $parent
     * @param String $child
     * @param String $lang
     * @return Boolean TRUE if successfull
     */
    public function removeTagChild($parent, $child, $lang) {
        $coll = $this->connection->selectCollection($this->dbName, $this->colName);

        $parentLang = ($parent == '_root') ? '_root' : '_root';
        
        $parentData = $this->searchTag($parent, $parentLang);
        if($parentData == NULL || count($parentData) > 1) {
            throw new Exception("Bad or no parent data found - $parent");
        }

        $childData = $this->searchTag($child, $lang);
        if($childData == NULL || count($childData) > 1) {
            throw new Exception("Bad or no child data found - $child");
        }

        $childID = (String) $childData[0]['_id'];
        unset($parentData[0]['child'][$childID]);

        $coll->update(array('_id'=>$parentData[0]['_id']), $parentData[0]);

        return TRUE;        
    }
    
    /**
     * Change the parent of a tag
     * @param String $oldParent if NULL assume it's "_root"
     * @param String $newParent if NULL assume it's "_root"
     * @param String $child Tag to be changed
     * @param String $lang Language of the tag
     */
    public function changeTagChild($oldParent, $newParent, $tagDescr, $tagLang) {
        
        $actOldParent = ($oldParent == NULL) ? '_root' : $oldParent;
        $actNewParent = ($newParent == NULL) ? '_root' : $newParent;
        
        try {
            $this->removeTagChild($actOldParent, $tagDescr, $tagLang);
        } catch (Exception $exc) {
            throw new Exception("While modifing old parent - $exc");
        }

        try {
            $this->setTagChild($actNewParent, $tagDescr, $tagLang);
        } catch (Exception $exc) {
            throw new Exception("While modifing new parent - $exc");
        }

    }
}
