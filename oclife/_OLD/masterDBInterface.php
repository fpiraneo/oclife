<?php

/**
 * Just open the connection for the database
 *
 * @author fpiraneo
 */
class masterDBInterface {
    private $username;
    private $passwd;
    private $host;
    
    private $dbPrefix;
    private $connection;
    private $db;
    
    private $errState;
    private $lastError;
    
    private $dbName;
    
    /**
     * Opens connection to database with given parameters
     * @param String $username Username to handle database
     * @param String $passwd Password to handle database
     * @param String $host Host where DB is
     * @param String $port Port where DB will answer
     * @param String $dbPrefix Prefix of DB name - Useful to store multiple instances on same DB
     */
    function __construct($username, $passwd, $host, $port, $dbPrefix) {
        $this->username = $username;
        $this->passwd = $passwd;
        $this->host = $host . ':' . $port;
        $this->dbPrefix = $dbPrefix;
        $this->dbName = $this->dbPrefix . 'life_Master';
        
        try {
            $this->connection = new Mongo($this->host);
            $this->db = $this->connection->selectDB($this->dbName);

            $this->errState = FALSE;
        }
        catch(MongoException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get actual error state
     * @return Bool TRUE = Error
     */
    public function getErrorState() {
        return $this->errState;
    }
    
    /**
     * Get actual error message
     * @return String Error message
     */
    public function lastMessage() {
        return $this->lastError;
    }
    
    /**
     * Get actual DB connection
     * @return type
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Get actual DB reference
     * @return type
     */
    public function getDb() {
        return $this->db;
    }
    
    protected function handleException($e) {
            $this->errState = TRUE;
            $this->lastError = $e->getMessage();        
    }
}
