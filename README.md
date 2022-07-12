# lol-mood-api
Lol mood api is an API Rest underdeveloppement using Riot Public API to retrieve key stats from a player of the game League of Legends

## Test the API yourself locally
### Use a Xammp server
I personnaly use Laragon, you can download it here : https://laragon.org/

Make sure to use the lastest version of PHP : [https://windows.php.net/download/](Windows) [https://www.php.net/downloads.php](Other OS)

### Create de the database

To create the database, use the script in api/config/database.sql

### Test Endpoints

2 endpoints available :

localhost?summonnerName=?&region=?&count=?

* Retrive games history of a summoner (player) in a specific region (ex: EUW), the query 'count' is optionnal, it's to specfie how many history games to retrive at once (10 by default 49 max) and longer the number is, longer the loading is.
This endpoint makes a call at the Riot Public API (the call takes some times, it's normal), then store the data on a MySQL DB and finnaly display the result in a JSON.

* You can test the endpoint with the following data : localhost?summonerName=Skillpadre&region=EUW


localhost?champName=?
* To work, this endpoint need at least one call of the previous endpoint before, it retrieves all games of a specific champion (character) stored in the DB and gives back 2 kind of data as json : Most Played BUILD of each items and Most Winrate BUILD of each items

* You can test the endpoint after the first one by making this call : localhost?champName=MissFortune
