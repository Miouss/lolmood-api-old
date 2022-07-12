<?php

class Champ{
    private $conn;

    private $table = 'champ';

    public function __construct($db){
        $this->conn = $db;
    }

    public function getChampId($champ){
        $sqlQuery = "SELECT
                        id
                    FROM 
                        $this->table 
                    WHERE 
                        name = '$champ'";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        return ($stmt->rowCount() > 0 ? $stmt->fetch() : false);
    }

    public function setChamp($champ){
        $sqlQuery = "INSERT INTO
                        $this->table (name)
                    VALUES( 
                        '$champ'
                        )";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
    }
}