<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["title"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["title" => $_GET["title"]];
    $endpoint = "https://movies-tv-shows-database.p.rapidapi.com/";
    $isRapidAPI = true;
    $rapidAPIHost = "movies-tv-shows-database.p.rapidapi.com";
    $result = get($endpoint, "SHOW_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
        
        if (isset($result["tv_results"])){
            $result = $result["tv_results"];
        }
        else {
            flash("Show does not exist");
        }
    } else {
        $result = [];
    }
}

?>
<div class="container-fluid">
    <h1>Show Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Title</label>
            <input name="title" />
            <input type="submit" value="Fetch Show" />
        </div>
    </form>
    
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $show) : ?>
                <pre>
                    <?php var_export($show);?>
                </pre>
            <?php endforeach; ?> 
           
        <?php endif; ?>
    </div>
</div>
<?php

$db = getDB();
$opts = ["debug" => false, "update_duplicate" => false, "columns_to_update"=>[]];
$query = insert("Shows", $result, $opts);
var_export($query);
require(__DIR__ . "/../../partials/flash.php");