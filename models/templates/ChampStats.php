<?php

function getChampStats($champName)
{
    $champStats = apiGetRequest($curl, $_SERVER['HTTP_HOST'] . "/api/Controller/GameInfoController.php?action=read&champName=" . $champName, true);
    return $champStats;
}

function sortStatsArrays(&$statsArrays, $type, $multipleArray = false)
{
    $uniqueStatsArrays = [];

    $totalPlayed = sizeof($statsArrays);

    foreach ($statsArrays as $key => &$statsArray) {
        $arrayExist = false;

        unset($statsArrays[$key]);

        if ((is_array($statsArray[0]) ? in_array(0, $statsArray[0]) : $statsArray[0] === 0)) {
            $arrayExist = true;
            --$totalPlayed;
        } else {
            foreach ($uniqueStatsArrays as &$uniqueStatsArray) {
                if ($uniqueStatsArray[$type] === $statsArray[0]) {
                    $arrayExist = true;

                    ++$uniqueStatsArray['played'];
                    $uniqueStatsArray['win'] += $statsArray[1];

                    break;
                }
            }
        }

        if (!$arrayExist) {
            array_push($uniqueStatsArrays, array($type => $statsArray[0], "played" => 1, "win" => $statsArray[1]));
        }
    }

    uasort($uniqueStatsArrays, function ($a, $b) {
        return ($a['played'] > $b['played']) ? -1 : 1;
    });

    if (sizeof($uniqueStatsArrays) > 0) {
        $mostPlayed[0] = $uniqueStatsArrays[array_key_first($uniqueStatsArrays)];
        $mostPlayed[0]["playRate"] = round($mostPlayed[0]["played"] / $totalPlayed * 100, 2);

        if ($multipleArray) {
            $keys = array_keys($uniqueStatsArrays);

            for ($i = 1; $i < sizeof($uniqueStatsArrays); $i++) {
                if ($i === 3) {
                    break;
                }

                $mostPlayed[$i] =  $uniqueStatsArrays[$keys[$i]];
                $mostPlayed[$i]["playRate"] = round($mostPlayed[$i]["played"] / $totalPlayed * 100, 2);
            }
        }

        $playedReference = $mostPlayed[0]['played'] / 4;

        uasort($uniqueStatsArrays, function ($a, $b) use ($playedReference) {
            if ($b['played'] >= $playedReference && $b['win'] / $b['played'] * 100 >= 50) {
                return ($a['win'] / $a['played'] > $b['win'] / $b['played']) ? -1 : 1;
            } else {
                return -1;
            }
        });

        $mostWinrate[0] = $uniqueStatsArrays[array_key_first($uniqueStatsArrays)];
        $mostWinrate[0]["winRate"] = round($mostWinrate[0]["win"] / $mostWinrate[0]["played"] * 100, 2);

        $mostPlayed[0]["played"] = $totalPlayed;

        if ($multipleArray) {
            $keys = array_keys($uniqueStatsArrays);

            for ($i = 1; $i < sizeof($uniqueStatsArrays); $i++) {
                if ($i === 3) {
                    break;
                }

                $mostWinrate[$i] = $uniqueStatsArrays[$keys[$i]];
                $mostWinrate[$i]["winRate"] = round($mostPlayed[$i]["win"] / $mostPlayed[$i]["played"] * 100, 2);

                $mostPlayed[$i]["played"] = $totalPlayed;
            }
        }

        return array("mostPlayed" => $mostPlayed, "mostWinrate" => $mostWinrate);
    } else {
        return "No stats were found";
    }
}

function sortSkills(&$skillOrder, $type)
{
    $totalPlayed = sizeof($skillOrder);
    $playedReference = $totalPlayed;

    $uniqueSkillsOrder = [];

    $minLght = ($type === "skills") ? 6 : 2;

    foreach ($skillOrder as $key => &$singleSkillsOrder) {
        $skilllOrderExist = false;
        unset($skillOrder[$key]);

        foreach ($uniqueSkillsOrder as &$singleUniqueSkillsOrder) {
            if (strncmp($singleUniqueSkillsOrder["order"], $singleSkillsOrder[0], $minLght) === 0) {
                if (strlen($singleUniqueSkillsOrder["order"]) < strlen($singleSkillsOrder[0])) {
                    $singleUniqueSkillsOrder["order"] = $singleSkillsOrder[0];
                }

                $singleUniqueSkillsOrder["win"] += $singleSkillsOrder[1];
                $singleUniqueSkillsOrder["played"]++;
                $skilllOrderExist = true;
            }
        }

        if (!$skilllOrderExist) {
            array_push($uniqueSkillsOrder, array("order" => $singleSkillsOrder[0], "played" => 1, "win" => $singleSkillsOrder[1]));
        }
    }

    uasort($uniqueSkillsOrder, function ($a, $b) use ($playedReference) {
        if ($b['played'] >= $playedReference && $b['win'] / $b['played'] * 100 >= 50) {
            return ($a['win'] / $a['played'] > $b['win'] / $b['played']) ? -1 : 1;
        } else {
            return -1;
        }
    });

    $mostPlayed = $uniqueSkillsOrder[array_key_first($uniqueSkillsOrder)];

    uasort($uniqueSkillsOrder, function ($a, $b) use ($playedReference) {
        if ($b['played'] >= $playedReference && $b['win'] / $b['played'] * 100 >= 50) {
            return ($a['win'] / $a['played'] > $b['win'] / $b['played']) ? -1 : 1;
        } else {
            return -1;
        }
    });

    $mostWinrate = $uniqueSkillsOrder[array_key_first($uniqueSkillsOrder)];

    return array("mostPlayed" => $mostPlayed, "mostWinrate" => $mostWinrate);
}
