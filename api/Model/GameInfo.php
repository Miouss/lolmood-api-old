<?php

class GameInfo{
    private $conn;

    private $game_info_table = 'game_info';
    
    private $game_table = 'game';
    private $account_table = 'account';
    private $champ_table = 'champ';
    private $positioning_table = 'positioning';
    private $asset_table = 'asset';

    public function __construct($db){
        $this->conn = $db;
    }

      
    public function setGameInfo($data){
        $sqlQuery = $this->setGameInfoQuery($data);

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
    }

    private function setGameInfoQuery($data){
        $query = array("columns" => array(), "values" => array());

        $this->pushSetGameInfoQueryArray($query, "game_id", $data['gameId']);
        $this->pushSetGameInfoQueryArray($query, "account_id", $data['accountId']);
        $this->pushSetGameInfoQueryArray($query, "champ_id", $data['champId']);
        $this->pushSetGameInfoQueryArray($query, "positioning_id", $data['positioningId']);
        $this->pushSetGameInfoQueryArray($query, "win", $data['win']);
        $this->pushSetGameInfoQueryArray($query, "kills", $data['kills']);
        $this->pushSetGameInfoQueryArray($query, "deaths", $data['deaths']);
        $this->pushSetGameInfoQueryArray($query, "assists", $data['assists']);
        $this->pushSetGameInfoQueryArray($query, "multikills", $data['multikills']);
        $this->pushSetGameInfoQueryArray($query, "primaryStyle_id", $data['primaryStyleId']);
        $this->pushSetGameInfoQueryArray($query, "subStyle_id", $data['subStyleId']);
        $this->pushSetGameInfoQueryArray($query, "perk_id", $data['perkId']);

        for($i = 0; $i <= 4; $i++){
            $this->pushSetGameInfoQueryArray($query, "rune" . strval($i) . "_id", $data['runes'][$i]);
        }

        for($i = 0; $i <= 2; $i++){
            $this->pushSetGameInfoQueryArray($query, "statsMod" . strval($i) . "_id", $data['statsMods'][$i]);
        }

        for($i = 0; $i <= 5; $i++){
            $this->pushSetGameInfoQueryArray($query, "item" . strval($i) . "_id", $data['items'][$i]);
        }

        $this->pushSetGameInfoQueryArray($query, "summoner1_id", $data['summoner1Id']);
        $this->pushSetGameInfoQueryArray($query, "summoner2_id", $data['summoner2Id']);
        $this->pushSetGameInfoQueryArray($query, "skills_order", $data['skillsOrder']);
        $this->pushSetGameInfoQueryArray($query, "evolves_order", $data['evolvesOrder']);

        if(!empty($data['startItems'])){
              for($i = 0; $i < sizeof($data['startItems']); $i++){
            $this->pushSetGameInfoQueryArray($query, "start_item" . strval($i) . "_id", $data['startItems'][$i]);
            }
        }
      
        if(!empty($data['completedItems'])){
            for($i = 0; $i < sizeof($data['completedItems']); $i++){
                if($i === 6){
                    break;
                }

                $this->pushSetGameInfoQueryArray($query, "completed_item" . strval($i) . "_id", $data['completedItems'][$i]);
            }
        }

        $columns = '(';

        foreach($query['columns'] as $column){
            $columns .= $column . ',';
        }

        $columns = rtrim($columns, ',');
        $columns .= ')';


        $values = '(';

        foreach($query['values'] as $value){
            $values .= $value . ',';
        }

        $values = rtrim($values, ',');

        $values .= ')';

        
        return "INSERT INTO $this->game_info_table $columns VALUES $values";
    }
    
    private function pushSetGameInfoQueryArray(&$array, $columns, $value){
        array_push($array["columns"], $columns);
        array_push($array["values"], $value);
    }

    public function getGameInfo($gameId, $puuid){
        $sqlQuery = $this->getGameInfoQuery($gameId, $puuid);

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        return (($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : array('Error' => "api key rate limit exceeded, game information can't be retrieved"));
    }

    public function getGameInfoQuery($gameId, $puuid, $isGameInfoRequest = true, $champName = null){
        $selectQuerry = "";
        $innerJoinQuerry = "";

        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_table, "patch", "game_id");

        if($isGameInfoRequest){
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_table, "identifier");
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_table, "duration");
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->champ_table, "name", "champ_id");
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "multikills");
            for($i = 0; $i <= 5; $i++){
                $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "item" . strval($i), "identifier", "item" . strval($i) . "_id");
            }
        }else{
            $innerJoinQuerry .= "INNER JOIN $this->champ_table ON $this->game_info_table.champ_id = $this->champ_table.id ";
        }

        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->positioning_table, "lane", "positioning_id");

        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "skills_order");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "evolves_order");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "win");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "kills");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "deaths");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "assists");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->game_info_table, "skills_order");

        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "primaryStyle", "identifier", "primaryStyle_id");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "subStyle", "identifier", "subStyle_id");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "perk", "identifier", "perk_id");

        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "summoner1", "identifier", "summoner1_id");
        $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "summoner2", "identifier", "summoner2_id");

        for($i = 0; $i <= 4; $i++){
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "rune" . strval($i), "identifier", "rune" . strval($i) . "_id");
        }

        for($i = 0; $i <= 2; $i++){
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "statsMod" . strval($i), "identifier", "statsMod" . strval($i) . "_id");
        }

        for($i = 0; $i <= 5; $i++){
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "startItem" . strval($i), "identifier", "start_item" . strval($i) . "_id");
        }

        for($i = 0; $i <= 5; $i++){
            $this->setGetGameInfoQuerry($selectQuerry, $innerJoinQuerry, $this->asset_table, "completedItem" . strval($i), "identifier", "completed_item" . strval($i) . "_id");
        }

        $innerJoinQuerry .= "INNER JOIN $this->account_table ON $this->game_info_table.account_id = $this->account_table.id ";

        $selectQuerry = rtrim($selectQuerry, ', ');

        $condition = '';

        if($isGameInfoRequest){
            $condition = "$this->account_table.puuid = '$puuid' AND $this->game_table.identifier = '$gameId'";
        }else{
            $condition = "$this->champ_table.name = '$champName'";
        }
        
        return "SELECT $selectQuerry FROM $this->game_info_table  $innerJoinQuerry WHERE $condition";
    }

    private function setGetGameInfoQuerry(&$selectQuerry, &$innerJoinQuerry, $table, $selectId, $id = null, $alias = null){
        if($id === null){
            $selectQuerry .= "$table.$selectId, ";
        } else if($alias === null){
            $selectQuerry .= "$table.$selectId, ";
            $innerJoinQuerry .= "INNER JOIN $table ON $this->game_info_table.$id = $table.id ";
        } else{
            $selectQuerry .= "$selectId.$id as $selectId, ";
            $innerJoinQuerry .= "LEFT JOIN $table $selectId ON $this->game_info_table.$alias = $selectId.id ";
        }
    }

    public function getChampStats($champName){
        $sqlQuery = $this->getGameInfoQuery(null, null, false, $champName);

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        return (($stmt->rowCount() > 0) ? $stmt->fetchALL(PDO::FETCH_ASSOC) : null);
    }
}