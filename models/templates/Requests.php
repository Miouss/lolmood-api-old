<?php

$apiKey = "RGAPI-8c8d890d-ffe8-45fc-bb7a-5806b9b3a582";

define('API-KEY', $apiKey);

define('Account', $_SERVER['HTTP_HOST'] . '/api/Controller/AccountController.php?action=');
define('GameInfo', $_SERVER['HTTP_HOST'] . '/api/Controller/GameInfoController.php?action=');


function apiGetRequest(&$curl, $apiUrl, $apiKeyNeeded = false) {
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

    return json_decode(curl_exec($curl), true);
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