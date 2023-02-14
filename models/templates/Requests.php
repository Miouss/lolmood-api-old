<?php

$apiKey = "RGAPI-85820c78-d922-403c-9782-caaa8f71f83a";

define('API-KEY', $apiKey);

define('Account', $_SERVER['HTTP_HOST'] . '/api/Controller/AccountController.php?action=');
define('GameInfo', $_SERVER['HTTP_HOST'] . '/api/Controller/GameInfoController.php?action=');


function apiGetRequest(&$curl, $apiUrl, $apiKeyNeeded = false, $hasToThrowExeption = true) {
    $curl = curl_init($apiUrl);

    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    if($apiKeyNeeded){
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'X-Riot-Token: ' . constant('API-KEY'),
        ]); 
    }

    $dataRequested = json_decode(curl_exec($curl), true);

    if(checkError($curl, $hasToThrowExeption)){
        $dataRequested = null;
    }

    return $dataRequested;
}

function apiPostRequest($requestUrl, $action, $data) {
    $curl = curl_init(constant($requestUrl) . $action);
 
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($data)));

    curl_exec($curl);

    $responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

    if(curl_errno($curl)){
        $errorMessage =  'Post request failed -> Error Code ' . $responseCode;
        curl_close($curl);
        throw new Exception($errorMessage);
    };

    curl_close($curl);
}

function checkError($curl, $hasToThrowExeption){
    if(curl_errno($curl)){ 
        if(!$hasToThrowExeption){
            return true;
        }   

        $errorMessage = "Api call failed -> ";

        switch(preg_replace('/[^0-9]/', '', curl_error($curl))){
            case "400" : $errorMessage .= "Bad request, check the queries";
            break;
            case "403" : $errorMessage .= "Forbidden";
            break;
            case "404" : $errorMessage .= "Data not found";
            break;
            case "429" : $errorMessage = "Rate limit of the api key exceeded, wait 3 minutes maximum to let the rate limit reseted";
            break;
        }

        curl_close($curl);
        throw new Exception($errorMessage);
    }

    return false;
}