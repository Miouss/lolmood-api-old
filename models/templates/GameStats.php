<?php

function getGameStats($puuid, $gameStats, $itemsData){
    $participantId = null;
    $participantItemsPurchased = array();
    $participantSkillsOrder = "";
    $participantEvolvesOrder = "";


    foreach ($gameStats["info"]["participants"] as $participant) {
        if ($participant["puuid"] === $puuid) {
            $participantId = $participant["participantId"];
            break;
        }
    }

    foreach ($gameStats["info"]["frames"] as $frame) {
        foreach ($frame["events"] as $frameEvents) {
            if (array_key_exists("participantId", $frameEvents)) {
                if ($frameEvents["participantId"] === $participantId) {
                    switch ($frameEvents["type"]) {
                        case "ITEM_PURCHASED":
                            array_push($participantItemsPurchased, array("itemId" => $frameEvents["itemId"], "timestamp" => $frameEvents["timestamp"]));
                        break;
                        case "SKILL_LEVEL_UP":
                            $skillsNumber = '';

                            if ($frameEvents["skillSlot"] === 1) {
                                $skillsNumber = '1';
                            } elseif ($frameEvents["skillSlot"] === 2) {
                                $skillsNumber = '2';
                            } elseif ($frameEvents["skillSlot"] === 3) {
                                $skillsNumber = '3';
                            }  else {
                                $skillsNumber = '4';
                            }

                            if($frameEvents["levelUpType"] === "NORMAL"){
                                $participantSkillsOrder .= $skillsNumber;
                            }
                            else{
                                $participantEvolvesOrder .= $skillsNumber;
                            }

                        break;
                        case "ITEM_UNDO":
                            array_pop($participantItemsPurchased);
                        break;
                        default:
                    }
                }
            }
        }
    }

    $startItems = array();
    $itemsOrder = array();

    foreach ($participantItemsPurchased as $item) {
            if ($item["timestamp"] < 60000 && $itemsData[$item["itemId"]]["gold"]["total"] <= 500 && $itemsData[$item["itemId"]]["gold"]["total"] > 0) {
                array_push($startItems, $item["itemId"]);
            } else {
                if($itemsData[$item["itemId"]]["gold"]["total"] > 1599){
                    if($itemsData[$item["itemId"]]["gold"]["purchasable"] === true){
                            if ($itemsData[$item["itemId"]]["gold"]["total"] > 1599) {
                                array_push($itemsOrder, $item["itemId"]);
                            }
                    }
                    else if($itemsData[$item["itemId"]]["gold"]["purchasable"] === true){
                        array_push($itemsOrder, $item["itemId"]);
                    }   
                }
                else if(array_key_exists("from", $itemsData[$item["itemId"]])){
                        if($itemsData[$item["itemId"]]["from"][0] === "1001"){
                            array_push($itemsOrder, $item["itemId"]);
                        } 
                }
            }
    }

    if($participantEvolvesOrder === ''){
        $participantEvolvesOrder = '0';
    }

    return array("startItems" => $startItems, "completedItems" => $itemsOrder, "skillsOrder" => $participantSkillsOrder, "evolvesOrder" => $participantEvolvesOrder);
}
