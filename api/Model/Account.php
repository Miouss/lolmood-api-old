<?php

class Account
{
    private $conn;
    private $table = 'account';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function setAccount($data)
    {   if(gettype($data) === "string"){
            $sqlQuery = "INSERT INTO $this->table (puuid) VALUES('$data')"; 
        }else{
            $keys = array_keys($data);

            $insertQuery = "";
    
            foreach($keys as $value){
                $insertQuery .= "$value, "; 
            }
    
            $insertQuery = rtrim($insertQuery, ', ');
    
            $valuesQuery = "";
    
            foreach($data as $value){
                if(gettype($value) === "string"){
                    $valuesQuery .= "'$value', ";
                }else{
                    $valuesQuery .= "$value, ";
                }
            }
    
            $valuesQuery = rtrim($valuesQuery, ', ');
    
            $sqlQuery = "INSERT INTO $this->table ($insertQuery) VALUES($valuesQuery)"; 
        }
        

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
    }

    public function getAccountId($puuid){
        $sqlQuery = "SELECT
                        id
                    FROM
                        $this->table
                    WHERE
                        puuid = '$puuid'";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        return (($stmt->rowCount() > 0) ? $stmt->fetch() : false);
    }

    public function isAccountExists($puuid)
    {
        $sqlQuery = "SELECT
                        name,
                        puuid,
                        level,
                        profile_icon_id,
                        `rank`,
                        tier,
                        lp,
                        games,
                        wins
                    FROM
                        $this->table
                    WHERE
                        puuid = '$puuid'";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        if($stmt->rowCount() > 0){
            $accountData = $stmt->fetch(PDO::FETCH_ASSOC);

            return $accountData;
        }


        return false;
    }
    
    public function updateAccount($puuid, $updatedName, $updatedLevel, $updatedProfileIconId)
    {
        $sqlQuery = "UPDATE
                        $this->table 
                    SET
                        name = '$updatedName',
                        level = $updatedLevel,
                        profile_icon_id = $updatedProfileIconId
                    WHERE 
                        puuid = '$puuid'";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
    }
}