<?php

class Positioning{
    private $conn;

    private $table = 'positioning';

    public function __construct($db){
        $this->conn = $db;
    }

    public function getPositioningId($positioning){
        $sqlQuery = "SELECT
                        id
                    FROM 
                        $this->table 
                    WHERE 
                        lane = '$positioning'";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        return ($stmt->rowCount() > 0 ? $stmt->fetch() : false);
    }

    public function setPositioning($positioning){
        $sqlQuery = "INSERT INTO
                        $this->table (lane)
                    VALUES( 
                        '$positioning'
                        )";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
    }
}