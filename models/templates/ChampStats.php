<?php

function getChampStats($champName){
    $champStats = apiGetRequest($curl, $_SERVER['HTTP_HOST'] . "/api/Controller/GameInfoController.php?action=read&champName=" . $champName, true);
    return $champStats;
}

function sortStatsArray(&$statsArray, $type, $multipleArray = false){
    $uniqueArray = [];

    $totalPlayed = sizeof($statsArray);

    foreach($statsArray as $key => &$singleArray){
        $arrayExist = false;

        unset($statsArray[$key]);

        if((is_array($singleArray[0]) ? in_array(0, $singleArray[0]) : $singleArray[0] === 0)){
            $arrayExist = true;
            --$totalPlayed;
        }else{
            foreach($uniqueArray as &$array){
                if($array[$type] === $singleArray[0]){
                    $arrayExist = true;

                    ++$array['played'];
                    $array['win'] += $singleArray[1]; 
                
                    break;
                }
            }
        }

        if(!$arrayExist){
            array_push($uniqueArray, array($type => $singleArray[0], "played" => 1, "win" => $singleArray[1]));
        }
    }

    uasort($uniqueArray, function($a, $b){
        return ($a['played'] > $b['played']) ? -1 : 1;
    });

    if(sizeof($uniqueArray) > 0){
        $mostPlayed[0] = $uniqueArray[array_key_first($uniqueArray)];
        $mostPlayed[0]["playRate"] = round($mostPlayed[0]["played"] / $totalPlayed * 100, 2);

        if($multipleArray){
            $keys = array_keys($uniqueArray);

            for($i = 1; $i < sizeof($uniqueArray); $i++){
                if($i === 3){
                    break;
                }

                $mostPlayed[$i] =  $uniqueArray[$keys[$i]];
                $mostPlayed[$i]["playRate"] = round($mostPlayed[$i]["played"] / $totalPlayed * 100, 2);
            }
        }

        $playedReference = $mostPlayed[0]['played'] / 4;

        uasort($uniqueArray, function($a, $b) use ($playedReference){
            if($b['played'] >= $playedReference && $b['win']/ $b['played'] * 100 >= 50){
                return ($a['win']/$a['played'] > $b['win']/ $b['played']) ? -1 : 1;
            }else{
                return -1;
            }
        });



        $mostWinrate[0] = $uniqueArray[array_key_first($uniqueArray)];
        $mostWinrate[0]["winRate"] = round($mostPlayed[0]["win"] /$mostPlayed[0]["played"] * 100, 2);

        $mostPlayed[0]["played"] = $totalPlayed;
        
        if($multipleArray){
            $keys = array_keys($uniqueArray);

            for($i = 1; $i < sizeof($uniqueArray); $i++){
                if($i === 3){
                    break;
                }

                $mostWinrate[$i] = $uniqueArray[$keys[$i]];
                $mostWinrate[$i]["winRate"] = round($mostPlayed[$i]["win"] /$mostPlayed[$i]["played"] * 100, 2);
        
                $mostPlayed[$i]["played"] = $totalPlayed;
            }
        }
        
        return array("mostPlayed" => $mostPlayed, "mostWinrate" => $mostWinrate);
    }else{
        return "No stats were found";
    }

}

function sortSkillsOrder(&$skillOrder){
    $totalPlayed = sizeof($skillOrder);
    $playedReference =  $totalPlayed;

    $uniqueSkillsOrder = [];

    foreach($skillOrder as $key => &$singleSkillsOrder){
        $skilllOrderExist = false;
        unset($skillOrder[$key]);

        foreach($uniqueSkillsOrder as &$singleUniqueSkillsOrder){
            if(strncmp($singleUniqueSkillsOrder["order"], $singleSkillsOrder[0], 6) === 0){
                if(strlen($singleUniqueSkillsOrder["order"]) < strlen($singleSkillsOrder[0])){
                    $singleUniqueSkillsOrder["order"] = $singleSkillsOrder[0];            
                }

                $singleUniqueSkillsOrder["win"] += $singleSkillsOrder[1];
                $singleUniqueSkillsOrder["played"]++;
                $skilllOrderExist = true;      
            }
        }

        if(!$skilllOrderExist){
            array_push($uniqueSkillsOrder, array("order" => $singleSkillsOrder[0], "played" => 1, "win" => $singleSkillsOrder[1]));
        }
    }

    uasort($uniqueSkillsOrder, function($a, $b) use ($playedReference){
        if($b['played'] >= $playedReference && $b['win']/ $b['played'] * 100 >= 50){
            return ($a['win']/$a['played'] > $b['win']/ $b['played']) ? -1 : 1;
        }else{
            return -1;
        }
    });

    $mostPlayed = $uniqueSkillsOrder[array_key_first($uniqueSkillsOrder)];

    uasort($uniqueSkillsOrder, function($a, $b) use ($playedReference){
        if($b['played'] >= $playedReference && $b['win']/ $b['played'] * 100 >= 50){
            return ($a['win']/$a['played'] > $b['win']/ $b['played']) ? -1 : 1;
        }else{
            return -1;
        }
    });

    $mostWinrate = $uniqueSkillsOrder[array_key_first($uniqueSkillsOrder)];

    return array("mostPlayed" => $mostPlayed, "mostWinrate" => $mostWinrate);
}