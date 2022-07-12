<?php

function getKeyStats($gameHistoryData)
{
    $statsByChamp = getStatsByChamp($gameHistoryData);

    $averageWinrate = getAverageWinrate($gameHistoryData);

    $keyStats = array(
        "statsByChamp" => $statsByChamp,
        "winrate" => $averageWinrate[0],
        "games" => $averageWinrate[1]
    );

    return $keyStats;
}

function getStatsByChamp($gameHistoryData)
{
    $statsByChamp = array();

    foreach ($gameHistoryData as $singleGame) {
        if(array_key_exists("duration", $singleGame))
            if($singleGame['duration'] < 300){

        }

        else if(array_key_exists($singleGame['name'], $statsByChamp)) {
            ++$statsByChamp[
                $singleGame['name']]['played'];

            $statsByChamp[
                $singleGame['name']]['win'] += $singleGame['win'];

            $statsByChamp[
                $singleGame['name']]['averageKills'] += $singleGame['kills'];

            $statsByChamp[
                $singleGame['name']]['averageDeaths'] += $singleGame['deaths'];

            $statsByChamp[
                $singleGame['name']]['averageAssists'] += $singleGame['assists'];
        } else {
            $statsByChamp[
                $singleGame['name']]['played'] = 1;

            $statsByChamp[
                $singleGame['name']]['win'] = $singleGame['win'];

            $statsByChamp[
                $singleGame['name']]['averageKills'] = $singleGame['kills'];

            $statsByChamp[
                $singleGame['name']]['averageDeaths'] = $singleGame['deaths'];

            $statsByChamp[
                $singleGame['name']]['averageAssists'] = $singleGame['assists'];
        }
    }

    foreach ($statsByChamp as $champName => $stats) {
        $statsByChamp[
            $champName]['winrate'] = round(($stats['win'] / $stats['played']) * 100, 2);

        $statsByChamp[
            $champName]['averageKills'] = round($stats['averageKills'] / $stats['played']);

        $statsByChamp[
            $champName]['averageDeaths'] = round($stats['averageDeaths'] / $stats['played']);

        $statsByChamp[
            $champName]['averageAssists'] = round($stats['averageAssists'] / $stats['played']);
    }

    // Sort by champ played
    uasort($statsByChamp, function($a, $b) {
        return (($a['played'] < $b['played']) ? 1 : -1);
    });
    
    return $statsByChamp;
}

function getAverageWinrate($gameHistoryData)
{
    $totalWin = 0;
    $totalGame = sizeof($gameHistoryData);

    foreach ($gameHistoryData as $value) {
        if(array_key_exists("win", $value)){
            if($value['win']){
                ++$totalWin;
            }
        }
    }

    return array(round(($totalWin / $totalGame) * 100, 2), $totalGame);
}