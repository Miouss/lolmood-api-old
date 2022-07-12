<?php

include_once '../config/database.php';

spl_autoload_register(function($className){
    include_once '../Model/' . $className . '.php';
});

$Account = new Account($db);

switch($_GET['action']){
    case 'create': create();
    break;
    case 'read' : read();
    break;
    case 'update': update();
    break;
}

function create()
{
    global $Account;

    $data = [
        "puuid" => $_POST['puuid'],
        "name" => $_POST['name'],
        "level" => (int) $_POST['summonerLevel'],
        "profile_icon_id" => (int) $_POST['profileIconId'],
    ];

    if(isset($_POST['rank'])){
        $data["rank"] = $_POST['rank'];
        $data["tier"] = $_POST['tier'];
        $data["lp"] = $_POST['lp'];
        $data["games"] = $_POST['games'];
        $data["wins"] = $_POST['wins'];
    }

    $Account->setAccount($data);
}
    

function read()
{   
    global $Account;

    if (isset($_GET['puuid'])) {
        $AccountStoredData = $Account->isAccountExists($_GET['puuid']);

        echo (($AccountStoredData) ? json_encode($AccountStoredData) : http_response_code(404));
    }
}

function update() 
{   
    global $Account;

    $Account->updateAccount(
        $_POST['puuid'],
        $_POST['name'],
        $_POST['summonerLevel'],
        $_POST['profileIconId']
    );
}