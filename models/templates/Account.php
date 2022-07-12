<?php

function getAccount($host){
    $riotApiGetAccount = 'https://' . $host . '.api.riotgames.com/lol/summoner/v4/summoners/by-name/' . urlencode($_GET["summonerName"]);
    $databaseApi = $_SERVER['HTTP_HOST'] . '/api/Controller/AccountController.php?action=read&puuid=';

    $updatedAccount = apiGetRequest($curl, $riotApiGetAccount, true);

    $riotApiGetRank = "https://" . $host . ".api.riotgames.com/lol/league/v4/entries/by-summoner/" . $updatedAccount["id"];

    $updateRank = apiGetRequest($curl, $riotApiGetRank, true);

    foreach($updateRank as $queueType){
        if($queueType["queueType"] === "RANKED_SOLO_5x5"){
            $updatedAccount["rank"] = $queueType["tier"];
            $updatedAccount["tier"] = $queueType["rank"];
            $updatedAccount["lp"] = $queueType["leaguePoints"];
            $updatedAccount["games"] = $queueType["wins"] + $queueType["losses"];
            $updatedAccount["wins"] = $queueType["wins"];
            break;
        }
    }

    if(curl_errno($curl)){
        $errorMessage = 'Riot Api Account Data request -> ' . curl_error($curl);
        curl_close($curl);
        throw new Exception($errorMessage);
    }
    curl_close($curl);

    $databaseApi .= $updatedAccount['puuid'];

    $accountStored = apiGetRequest($curl, $databaseApi);

    if(curl_errno($curl)){
        apiPostRequest('Account', 'create', $updatedAccount);
    } else{
        $updatedData = array();

        if(($updatedAccount['name'] != $accountStored['name']) or
            ($updatedAccount['profileIconId'] != $accountStored['profile_icon_id']) or
             ($updatedAccount['summonerLevel'] != $accountStored['level']))
            {
                $updatedData['name'] = $updatedAccount['name'];
                $updatedData['profileIconId'] = $updatedAccount['profileIconId'];
                $updatedData['summonerLevel'] = $updatedAccount['summonerLevel'];
                $updatedData['puuid'] = $updatedAccount['puuid'];
                
                apiPostRequest('Account', 'update', $updatedData);
        }
    }

    return apiGetRequest($curl, $databaseApi);
}