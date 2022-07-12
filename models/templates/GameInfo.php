<?php

$count = isset($_GET["count"]) ? $_GET["count"] : 1;

define('count', $count); // number of match to get
define('queueId', 420); // queueId for picking match 420 = ranked 5v5 soloQ

function getGameHistoryData($puuid, $region)
{
    $gamesIdsList =  getGamesIdsList($puuid, $region);

    $gameHistoryData =  [];

    $count = constant("count");

    if (is_array($gamesIdsList)) {

        foreach ($gamesIdsList as $gameId) {
            (isGameIdStored($gameId) ? array_push($gameHistoryData, getGameInfo($gameId, $puuid))
                : (setGameInfo($gameId, $region) ? array_push($gameHistoryData, getGameInfo($gameId, $puuid)) : $gameHistoryData["error"] = $count));

            if(array_key_exists("error", $gameHistoryData)){
                break;
            }

            $count--;
        }
    }

    return (empty($gameHistoryData) ? throw new Exception("Match Id List couldn't be retrieve because rate limit of the api key exceeded, wait 3 minutes maximum to let the rate limit reseted") : $gameHistoryData);
}

function setGameInfo($gameId, $region)
{
    $itemsData = json_decode(file_get_contents("./models/item.json"), true)["data"];
    $gameStats = apiGetRequest($curl, "https://" . $region . ".api.riotgames.com/lol/match/v5/matches/" . $gameId . "/timeline", true, false);

    if($gameStats === null){
        return false;
    }

    $riotApi  = 'https://' . $region . '.api.riotgames.com/lol/match/v5/matches/' . $gameId;
    $gameData = apiGetRequest($curl, $riotApi, true, false);

    if($gameData === null){
        return false;
    }

    $participants = $gameData['metadata']['participants'];
    $patchArray = explode(".", $gameData['info']['gameVersion']);

    $patch = $patchArray[0] . '.' . $patchArray[1];
    $i = 0;

    foreach ($participants as $participant) {
        $participantGameData = $gameData['info']['participants'][$i];

        if ($participantGameData['pentaKills']) {
            $multikills = 1;
        } else if ($participantGameData['quadraKills']) {
            $multikills = 2;
        } else if ($participantGameData['tripleKills']) {
            $multikills = 3;
        } else {
            $multikills = 0;
        }

        $win = $participantGameData['win'];

        $items = array();

        for ($j = 0; $j <= 5; $j++) {
            array_push($items, $participantGameData["item" . strval($j)]);
        }

        $runes = array();

        for ($j = 0; $j <= 5; $j++) {
            if ($j < 4) {
                array_push($runes, $participantGameData['perks']['styles'][0]['selections'][$j]['perk']);
            } else {
                array_push($runes, $participantGameData['perks']['styles'][1]['selections'][$j - 4]['perk']);
            }
        }

        $statsMods = array();

        for ($j = 0; $j <= 2; $j++) {
            switch ($j) {
                case 0:
                    $type = "offense";
                    break;
                case 1:
                    $type = "flex";
                    break;
                case 2:
                    $type = "defense";
                    break;
            }
            array_push($statsMods, $participantGameData['perks']['statPerks'][$type]);
        }

        $participantGameStats = getGameStats($participant, $gameStats, $itemsData);

        $data = array(
            'GameIdentifier' => $gameId,
            'patch' => $patch,
            'duration' => $gameData['info']['gameDuration'],
            'AccountPuuid' => $participant,
            'ChampName' => $participantGameData['championName'],
            'PositioningLane' => $participantGameData['teamPosition'],
            'win' => $win,
            'kills' => $participantGameData['kills'],
            'deaths' => $participantGameData['deaths'],
            'assists' => $participantGameData['assists'],
            'multikills' => $multikills,
            'primaryStyle' => $participantGameData['perks']['styles'][0]['style'],
            'subStyle' => $participantGameData['perks']['styles'][1]['style'],
            'runes' => $runes,
            'statsMods' => $statsMods,
            'items' => $items,
            'summoner1' => $participantGameData['summoner1Id'],
            'summoner2' => $participantGameData['summoner2Id'],
            'gameStats' => $participantGameStats
        );

        apiPostRequest('GameInfo', 'create', $data);

        ++$i;
    }

    return true;
}

function getGamesIdsList($summonerPuuid, $region)
{
    $riotApi = 'https://' . $region . '.api.riotgames.com/lol/match/v5/matches/by-puuid/' . $summonerPuuid . '/ids?queue=' . constant('queueId') . '&start=0&count=' . constant('count');

    return apiGetRequest($curl, $riotApi, true);
}

function isGameIdStored($gameId)
{
    $databaseApi = $_SERVER['HTTP_HOST'] . '/api/Controller/GameInfoController.php?action=read&gameId=' . $gameId;

    return apiGetRequest($curl, $databaseApi);
}

function getGameInfo($gameId, $puuid)
{
    $databaseApi = $_SERVER['HTTP_HOST'] . '/api/Controller/GameInfoController.php?action=read&puuid=' . $puuid . '&gameId=' . $gameId;

    return apiGetRequest($curl, $databaseApi);
}
