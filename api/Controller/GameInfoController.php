<?php

include_once '../config/database.php';

spl_autoload_register(function($className){
    include_once '../Model/' . $className . '.php';
});

$Game = new Game($db);
$GameInfo = new GameInfo($db);


switch ($_GET['action']) {
    case 'create':
        $Positioning = new Positioning($db);
        $Account = new Account($db);
        $Champ = new Champ($db);
        $Asset = new Asset($db);

        create();
    break;

    case 'read':
        read();
    break;
}

function create()
{
    global $GameInfo, $Game, $Champ, $Positioning, $Asset, $Account;
    $data = array();

    $champId = collectId($Champ, $_POST['ChampName']);
    
    $accountId = collectId($Account, $_POST['AccountPuuid']);

    $gameId = collectId($Game, $_POST['GameIdentifier'], $_POST['patch'], $_POST['duration']);

    $positioningId = collectId($Positioning, $_POST['PositioningLane']);

    $primaryStyleId = collectId($Asset, $_POST['primaryStyle']);
    $subStyleId = collectId($Asset, $_POST['subStyle']);
    $perkId = collectId($Asset, $_POST['runes'][0]);

    $runes = array();

    for($i = 1; $i <= 5; $i++){
        array_push($runes, collectId($Asset, $_POST['runes'][$i]));
    }

    $items = array();

    for($i = 0; $i <= 5; $i++){
        array_push($items, collectId($Asset, $_POST['items'][$i]));
    }

    $statsMods = array();

    for($i = 0; $i <= 2; $i++){
        array_push($statsMods, collectId($Asset, $_POST['statsMods'][$i]));
    }

    if(!empty($_POST["gameStats"]["startItems"])){
        $startItems = array();

        for($i = 0; $i < sizeof($_POST["gameStats"]["startItems"]); $i++){
            array_push($startItems, collectId($Asset, $_POST["gameStats"]["startItems"][$i]));
        }
    
        setData($data, "startItems", $startItems);
    }



    if(!empty($_POST["gameStats"]["completedItems"])){
        $completedItems = array();

        for($i = 0; $i < sizeof($_POST["gameStats"]["completedItems"]); $i++){
            array_push($completedItems, collectId($Asset, $_POST["gameStats"]["completedItems"][$i]));
        }
    
        setData($data, "completedItems", $completedItems);
    }

    $summoner1Id = collectId($Asset, $_POST['summoner1']);
    $summoner2Id = collectId($Asset, $_POST['summoner2']);


    setData($data, "gameId", $gameId);
    setData($data, "accountId", $accountId);
    setData($data, "champId", $champId);
    setData($data, "positioningId", $positioningId);
    setData($data, "skillsOrder", $_POST['gameStats']['skillsOrder']);
    setData($data, "evolvesOrder", $_POST['gameStats']['evolvesOrder']);
    setData($data, "win", $_POST['win']);
    setData($data, "kills", $_POST['kills']);
    setData($data, "deaths", $_POST['deaths']);
    setData($data, "assists", $_POST['assists']);
    setData($data, "multikills", $_POST['multikills']);
    setData($data, "primaryStyleId", $primaryStyleId);
    setData($data, "subStyleId", $subStyleId);
    setData($data, "perkId", $perkId);
    setData($data, "runes", $runes);
    setData($data, "items", $items);
    setData($data, "statsMods", $statsMods);
    setData($data, "summoner1Id", $summoner1Id);
    setData($data, "summoner2Id", $summoner2Id);

    $GameInfo->setGameInfo($data);
}

function read()
{
    global $GameInfo, $Game;

    if(isset($_GET['champName'])){
        echo json_encode($GameInfo->getChampStats($_GET['champName']));
    }else if (isset($_GET['puuid'])) {
       echo json_encode($GameInfo->getGameInfo($_GET['gameId'], $_GET['puuid']));
    } else {
        echo $Game->isGameIdStored($_GET['gameId']);
    }
}

function setData(&$data, $key, $value){
    $data[$key] = $value;
}

function collectId($Class, $postData, $postData2 = null, $postData3 = null){
    $className = get_class($Class);
    $getId = 'get' . $className . 'Id';
    $set = 'set'. $className;

    $id = $Class->$getId($postData);

    if(!$id){
        if($postData3 != null){
            $Class->$set($postData, $postData2, $postData3);
        }

        else{
            $Class->$set($postData);
        }
        
        return $Class->$getId($postData)['id'];
    }

    return $id['id'];
}