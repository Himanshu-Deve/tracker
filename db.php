<?php
// config/database.php

class Database {
    
    // private $host = "localhost";
    // private $username = "admin";
    // private $password = "Uw3(E^&LTUw@";
    // private $dbname = "ereal_state";
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    // private $dbname = "ereal_state";
    private $dbname = "attendance_system";
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            
            // Check connection
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to utf8
            $this->conn->set_charset("utf8");
            
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
        
        return $this->conn;
    }
    
    // Close connection
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Helper function to get database connection
function getDB() {
    $database = new Database();
    return $database->getConnection();
}
?>