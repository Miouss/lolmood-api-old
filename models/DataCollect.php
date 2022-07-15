<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


$files = glob(__DIR__ . '/templates/*.php');

foreach ($files as $file) {
    require_once($file);
}

function displayArray($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

try {
    if (isset($_GET["champName"])) {
        $champStats = getChampStats($_GET["champName"]);

        if ($champStats === null) {
            echo json_encode("Champ name is incorrect");
        } else {

            $runesArray = [];
            $statsModsArray = [];
            $startItemsArray = [];
            $coreItemsArray = [];
            $fourthItemsArray = [];
            $fifthItemsArray = [];
            $sixthItemsArray = [];
            $skillsOrder = [];
            $sumsArray = [];

            foreach ($champStats as &$singleGame) {
                sortData($singleGame, false);
                array_unshift($singleGame['runes'], $singleGame['perk']);

                sort($singleGame['startItems']);

                array_unshift($singleGame['runes'], $singleGame['primaryStyle'], $singleGame['subStyle']);
                array_push($runesArray, array($singleGame['runes'], $singleGame['win']));
                array_push($statsModsArray, array($singleGame['statsMods'], $singleGame['win']));
                array_push($startItemsArray, array($singleGame['startItems'], $singleGame['win']));
                array_push($skillsOrder, array($singleGame['skills_order'], $singleGame['win']));
                array_push($sumsArray, array(array($singleGame['summoner1'], $singleGame['summoner2']), $singleGame['win']));

                $coreItems = array();

                for ($i = 0; $i < sizeof($singleGame['completedItems']); $i++) {
                    if ($i <= 2) {
                        array_push($coreItems, $singleGame['completedItems'][$i]);
                    } elseif ($i === 3) {
                        array_push($fourthItemsArray, array($singleGame['completedItems'][$i], $singleGame['win']));
                    } elseif ($i === 4) {
                        array_push($fifthItemsArray, array($singleGame['completedItems'][$i], $singleGame['win']));
                    } else {
                        array_push($sixthItemsArray, array($singleGame['completedItems'][$i], $singleGame['win']));
                    }
                }

                while (sizeof($coreItems) < 3) {
                    array_push($coreItems, 0);
                }

                array_push($coreItemsArray, array($coreItems, $singleGame['win']));
            }

            echo json_encode(array(
                "skills" => sortSkillsOrder($skillsOrder),
                "runes" => sortStatsArrays($runesArray, "runes"),
                "statsMods" => sortStatsArrays($statsModsArray, "statsMods"),
                "startItems" => sortStatsArrays($startItemsArray, "startItems"),
                "completedItems" => array(
                    "coreItems" => sortStatsArrays($coreItemsArray, "coreItems"),
                    "fourthItem" => sortStatsArrays($fourthItemsArray, "fourthItem", true),
                    "fifthItem" => sortStatsArrays($fifthItemsArray, "fifthItem", true),
                    "sixthItem" => sortStatsArrays($sixthItemsArray, "sixthItem", true)                    
                ),
                "summoners" => sortStatsArrays($sumsArray, "summoners"),
            ), true);
        }
    } else if (isset($_GET["summonerName"]) && isset($_GET["region"])) {
        switch ($_GET["region"]) {
            case "EUNE":
                $host = 'eun1';
                $region = 'europe';
                break;
            case "EUW":
                $host = 'euw1';
                $region = 'europe';
                break;
            case "NA":
                $host = 'na1';
                $region = 'americas';
                break;
            case "BR":
                $host = 'br1';
                $region = 'americas';
                break;
            case "LAN":
                $host = 'la1';
                $region = 'asia';
                break;
            case "LAS":
                $host = 'la2';
                $region = 'asia';
                break;
            case "KR":
                $host = 'kr';
                $region = 'asia';
                break;
            case "RU":
                $host = 'ru';
                $region = 'asia';
                break;
            case "JP":
                $host = 'jp1';
                $region = 'asia';
                break;
        }

        $account = getAccount($host);
        $gameHistoryData = getGameHistoryData($account['puuid'], $region);

        $errorMessage = "";

        if(array_key_exists("error", $gameHistoryData)){
            $errorMessage = $gameHistoryData["error"] . " games couldn't be retrived";
        }

        unset($gameHistoryData["error"]);
        unset($account['puuid']);

        $keyStats = getKeyStats($gameHistoryData);

        foreach ($gameHistoryData as &$singleGame) {
            sortData($singleGame);
        }

        $data = array(
            "Account" => $account,
            "GameHistoryData" => $gameHistoryData,
            "KeyStats" => $keyStats
        );

        if($errorMessage !== ""){
            $data["ErrorMessage"] = $errorMessage;
        }

        echo json_encode($data);
    } else {
        echo json_encode("Parameters missing");
    }
} catch (Exception $exception) {
    echo json_encode($exception->getMessage());
}

function sortData(&$singleGame, $isGameHistory = true)
{
    $runeArray = filterArray($singleGame, "rune");

    $statsModsArray = filterArray($singleGame, "statsMod");

    $startItemArray = filterArray($singleGame, "startItem");

    $completedItemArray = filterArray($singleGame, "completedItem");

    if ($isGameHistory) {
        $itemsEndGame = filterArray($singleGame, "item");
        $singleGame["items"] = $itemsEndGame;
    }

    $singleGame["runes"] = $runeArray;
    $singleGame["statsMods"] = $statsModsArray;
    $singleGame["startItems"] = $startItemArray;
    $singleGame["completedItems"] =  $completedItemArray;
}


function filterArray(&$array, $stringFilter)
{
    $uncleanedArray = array_filter($array, function ($var) use ($stringFilter) {
        return str_starts_with($var, $stringFilter) != null;
    }, ARRAY_FILTER_USE_KEY);

    $cleanedArray = array();

    foreach ($uncleanedArray as $key => $value) {
        unset($array[$key]);
        if ($value != null) {
            array_push($cleanedArray, $value);
        }
    }

    return $cleanedArray;
}