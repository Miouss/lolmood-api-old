<?php

class GameInfo
{
    private $conn;

    private $game_info_table = 'game_info';

    private $game_table = 'game';
    private $account_table = 'account';
    private $champ_table = 'champ';
    private $positioning_table = 'positioning';
    private $asset_table = 'asset';

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function setGameInfo($data)
    {
        $sqlQuery = $this->getSetGameInfoQuery($data);

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
    }

    private function getSetGameInfoQuery($data)
    {
        $query = array("columns" => array(), "values" => array());

        $gameInfoData = array(
            "game_id" => $data['gameId'],
            "account_id" => $data['accountId'],
            "champ_id" => $data['champId'],
            "positioning_id" => $data['positioningId'],
            "win" => $data['win'],
            "kills" => $data['kills'],
            "deaths" => $data['deaths'],
            "assists" => $data['assists'],
            "multikills" => $data['multikills'],
            "primaryStyle_id" => $data['primaryStyleId'],
            "subStyle_id" => $data['subStyleId'],
            "perk_id" => $data['perkId'],
            "summoner1_id" => $data['summoner1Id'],
            "summoner2_id" => $data['summoner2Id'],
            "skills_order" => $data['skillsOrder'],
            "evolves_order" => $data['evolvesOrder']
        );

        for ($i = 0; $i <= 4; $i++) {
            $gameInfoData["rune" . strval($i) . "_id"] = $data['runes'][$i];
        }

        for ($i = 0; $i <= 2; $i++) {
            $gameInfoData["statsMod" . strval($i) . "_id"] = $data['statsMods'][$i];
        }

        for ($i = 0; $i <= 5; $i++) {
            $gameInfoData["item" . strval($i) . "_id"] = $data['items'][$i];
        }

        if (!empty($data['startItems'])) {
            for ($i = 0; $i < sizeof($data['startItems']); $i++) {
                $gameInfoData["start_item" . strval($i) . "_id"] = $data['startItems'][$i];
            }
        }

        if (!empty($data['completedItems'])) {
            for ($i = 0; $i < sizeof($data['completedItems']); $i++) {
                if ($i === 6) {
                    break;
                }
                $gameInfoData["completed_item" . strval($i) . "_id"] = $data['completedItems'][$i];
            }
        }

        $this->setGameInfoQuery($query, $gameInfoData);

        $columns = '(';
        $values = '(';

        $this->buildMySQLQuery($query["columns"], $columns);
        $this->buildMySQLQuery($query["values"], $values);

        return "INSERT INTO $this->game_info_table $columns VALUES $values";
    }

    private function setGameInfoQuery(&$query, $gameInfoData)
    {
        foreach ($gameInfoData as $dataColumn => $dataValue) {
            array_push($query["columns"], $dataColumn);
            array_push($query["values"], $dataValue);
        }
    }

    private function buildMySQLQuery($query, &$field)
    {
        foreach ($query as $value) {
            $field .= $value . ',';
        }

        $field = rtrim($field, ',');
        $field .= ')';
    }

    public function getGameInfo($gameId, $puuid)
    {
        $sqlQuery = $this->getGameInfoQuery($gameId, $puuid);

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();
        
        return (($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC) : array('Error' => "api key rate limit exceeded, game information can't be retrieved"));
    }

    public function getGameInfoQuery($gameId, $puuid, $isGameInfoRequest = true, $champName = null)
    {
        $selectQuery = "";
        $innerJoinQuery = "";

        $gameInfoInfoQueryData = array();

        function addField(&$gameInfoInfoQueryData, ...$data)
        {
            array_push($gameInfoInfoQueryData, $data);
        }

        addField($gameInfoInfoQueryData, $this->game_table, "patch", "game_id");

        if ($isGameInfoRequest) {
            addField($gameInfoInfoQueryData, $this->game_table, "identifier");
            addField($gameInfoInfoQueryData, $this->game_table, "duration");
            addField($gameInfoInfoQueryData, $this->champ_table, "name", "champ_id");
            addField($gameInfoInfoQueryData, $this->game_info_table, "multikills");

            for ($i = 0; $i <= 5; $i++) {
                addField($gameInfoInfoQueryData, $this->asset_table, "item" . strval($i), "identifier", "item" . strval($i) . "_id");
            }
        } else {
            $innerJoinQuery .= "INNER JOIN $this->champ_table ON $this->game_info_table.champ_id = $this->champ_table.id ";
        }

        addField($gameInfoInfoQueryData, $this->positioning_table, "lane", "positioning_id");

        addField($gameInfoInfoQueryData, $this->game_info_table, "skills_order");
        addField($gameInfoInfoQueryData, $this->game_info_table, "evolves_order");
        addField($gameInfoInfoQueryData, $this->game_info_table, "win");
        addField($gameInfoInfoQueryData, $this->game_info_table, "kills");
        addField($gameInfoInfoQueryData, $this->game_info_table, "deaths");
        addField($gameInfoInfoQueryData, $this->game_info_table, "assists");
        addField($gameInfoInfoQueryData, $this->game_info_table, "skills_order");

        addField($gameInfoInfoQueryData, $this->asset_table, "primaryStyle", "identifier", "primaryStyle_id");
        addField($gameInfoInfoQueryData, $this->asset_table, "subStyle", "identifier", "subStyle_id");
        addField($gameInfoInfoQueryData, $this->asset_table, "perk", "identifier", "perk_id");

        addField($gameInfoInfoQueryData, $this->asset_table, "summoner1", "identifier", "summoner1_id");
        addField($gameInfoInfoQueryData, $this->asset_table, "summoner2", "identifier", "summoner2_id");

        for ($i = 0; $i <= 4; $i++) {
            addField($gameInfoInfoQueryData, $this->asset_table, "rune" . strval($i), "identifier", "rune" . strval($i) . "_id");
        }

        for ($i = 0; $i <= 2; $i++) {
            addField($gameInfoInfoQueryData, $this->asset_table, "statsMod" . strval($i), "identifier", "statsMod" . strval($i) . "_id");
        }

        for ($i = 0; $i <= 5; $i++) {
            addField($gameInfoInfoQueryData, $this->asset_table, "startItem" . strval($i), "identifier", "start_item" . strval($i) . "_id");
        }

        for ($i = 0; $i <= 5; $i++) {
            addField($gameInfoInfoQueryData, $this->asset_table, "completedItem" . strval($i), "identifier", "completed_item" . strval($i) . "_id");
        }

        $this->buildGetGameInfoQuery($selectQuery, $innerJoinQuery, $gameInfoInfoQueryData);

        $condition = '';

        if ($isGameInfoRequest) {
            $condition = "$this->account_table.puuid = '$puuid' AND $this->game_table.identifier = '$gameId'";
        } else {
            $condition = "$this->champ_table.name = '$champName'";
        }
        
        return "SELECT $selectQuery FROM $this->game_info_table $innerJoinQuery WHERE $condition";
    }

    private function buildGetGameInfoQuery(&$selectQuery, &$innerJoinQuery, $dataArray)
    {
        foreach ($dataArray as $data) {
            $table = $data[0];
            $selectId = $data[1];
            $id = array_key_exists(2, $data) ? $data[2] : null;
            $alias = array_key_exists(3, $data) ? $data[3] : null;

            if ($id === null) {
                $selectQuery .= "$table.$selectId, ";
            } else if ($alias === null) {
                $selectQuery .= "$table.$selectId, ";
                $innerJoinQuery .= "INNER JOIN $table ON $this->game_info_table.$id = $table.id ";
            } else {
                $selectQuery .= "$selectId.$id as $selectId, ";
                $innerJoinQuery .= "LEFT JOIN $table $selectId ON $this->game_info_table.$alias = $selectId.id ";
            }
        }
        
        $selectQuery = rtrim($selectQuery, ', ');

        $innerJoinQuery .= "INNER JOIN $this->account_table ON $this->game_info_table.account_id = $this->account_table.id"; 
    }

    public function getChampStats($champName)
    {
        $sqlQuery = $this->getGameInfoQuery(null, null, false, $champName);

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute();

        return (($stmt->rowCount() > 0) ? $stmt->fetchALL(PDO::FETCH_ASSOC) : null);
    }
}
