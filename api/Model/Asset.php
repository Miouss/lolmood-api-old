<?php

class Asset{
    private $conn;

    private $table = 'asset';

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAssetId($assetId){
        $sqlQuery = "SELECT
                        id
                    FROM 
                        $this->table 
                    WHERE 
                        identifier = $assetId";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        return ($stmt->rowCount() > 0 ? $stmt->fetch() : false);
    }

    public function setAsset($assetId){
        $sqlQuery = "INSERT INTO
                        $this->table (identifier)
                    VALUES( 
                        $assetId
                        )";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
    }
}