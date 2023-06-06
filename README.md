# Video demonstration : https://streamable.com/zqwq4h

## Test the API yourself locally
### Use a Xammp server
I personnaly use Laragon, you can download it here : https://laragon.org/

Make sure to use at least PHP 8.0.0 : [Windows](https://windows.php.net/download/) - [Other OS](https://www.php.net/downloads.php)

### Create the database

To create the database, use the script in api/config/database.sql

### Test Endpoints

2 endpoints available :

localhost?summonnerName=?&region=?&count=?

* Retrive games history of a summoner (player) in a specific region (ex: EUW), the query 'count' is optionnal, it's to specfie how many history games to retrive at once (10 by default 99 max but you will be limited due to api key's rates limits) and longer the number is, longer the loading is.
This endpoint makes a call at the Riot Public API (the call takes some times, it's normal), then store the data on a MySQL DB and finnaly display the result in a JSON.

* You can test the endpoint with the following data : localhost?summonerName=SLY%20Wakz&region=EUW


localhost?champName=?
* To work, this endpoint need at least one call of the previous endpoint before, it retrieves all games of a specific champion (character) stored in the DB and gives back 2 kind of data as json : Most Played BUILD of each items and Most Winrate BUILD of each items

* You can test the endpoint after the first one by making this call : localhost?champName=Ezreal
