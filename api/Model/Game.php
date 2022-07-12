<?php

class Game{
    private $conn;

    private $table = 'game';

    public function __construct($db){
        $this->conn = $db;
    }

    public function setGame($gameId, $patch, $duration){
        $sqlQuery = "INSERT INTO
                        $this->table
                        (
                            identifier,
                            patch,
                            duration
                        )
                    VALUES(
                        '$gameId',
                        '$patch',
                        $duration
                    )";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();     
    }

    public function getGameId($gameId){
        $sqlQuery = "SELECT
                        id
                    FROM 
                        $this->table 
                    WHERE 
                        identifier = '$gameId'";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        return ($stmt->rowCount() > 0 ? $stmt->fetch() : false);
    }

    public function isGameIdStored($gameId){
        $sqlQuery = "SELECT 
                            id
                        FROM 
                            $this->table
                        WHERE
                            identifier = '$gameId'";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        return (($stmt->rowCount() > 0) ? true : false);
    }
}