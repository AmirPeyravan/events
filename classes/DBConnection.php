<?php
class DBConnection{

    private $host = 'localhost';
    private $username = 'root';
    private $password = 'root';
    private $database = 'noornet_event_db';
    
    public $conn;
    
    public function __construct(){

        if (!isset($this->conn)) {
            
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if (!$this->conn) {
                echo 'Cannot connect to database server';
                exit;
            }           
        }    
        
    }
    public function __destruct(){
        $this->conn->close();
    }
}
?>