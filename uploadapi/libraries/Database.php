<?php

class Database {
    
    public $dbConnection;
    
    public function __construct() {
        try {
            $this->dbConnection = new PDO("mysql:host=" . SERVER_NAME . ";dbname=" . DATABASE, USER_NAME, PASSWORD);
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $ex) {
            return $ex;
        }
    }
    
    // Function to insert data into table
    public function insert($query, $params) {
        try {
            $stmt = $this->dbConnection->prepare($query);
            foreach($params as $key => $value) {
                $stmt->bindParam($key, $value);
            }
            return $stmt->execute();
        } catch(Exception $ex) {
            return $ex;
        }
    }
    
    // Function to get all data from table
    public function fetchAll($query) {
        try {
            $stmt = $this->dbConnection->prepare($query);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
            return $stmt->fetchAll();
        } catch(Exception $ex) {
            return $ex;
        }
    }

}
