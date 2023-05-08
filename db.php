<?php
class DataBase {
    private $host;
    private $username;
    private $password;
    private $database;
  
    public function __construct($host, $username, $password, $database) {
      $this->host = $host;
      $this->username = $username;
      $this->password = $password;
      $this->database = $database;
    }
  
    public function connect() {
      try {
         $connection = new PDO('mysql:host=' .$this->host .';dbname=' . $this->database, $this->username, $this->password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         
        return $connection;
      }  catch (\Exception $e) {
        echo "Database Error: " . $e->getMessage();
      }
    }
  }
 
?>