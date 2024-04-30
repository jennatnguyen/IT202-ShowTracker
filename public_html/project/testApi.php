<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["title"])) {

    $data = ["title" => $_GET["title"]];
    $endpoint = "https://movies-tv-shows-database.p.rapidapi.com/";
    $isRapidAPI = true;
    $rapidAPIHost = "movies-tv-shows-database.p.rapidapi.com";
    $extra_headers["Type"] = "get-shows-by-title";
    $result = get($endpoint, "SHOW_API_KEY", $data, $extra_headers, $isRapidAPI, $rapidAPIHost);

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
            <?php foreach($result as $index=>$show) : ?>

                <?php foreach($show as $key=>$value): ?>
                <pre>
                    <?php var_export($show); 

                        if($key === "release_date" && $value === "0000-00-00") {
                            $result[$index][$key] = null;
                        }
                        
                        ?>
                </pre>
                <?php endforeach; ?> <!-- inner foreach end -->
            <?php endforeach; ?> 
        <?php endif; ?>
    </div>
</div>
<?php

$db = getDB();
$opts = ["debug" => false, "update_duplicate" => true, "columns_to_update"=>[]];
$query = insert("Shows", $result, $opts);
var_export($query);

/*$extra_headers["Type"] = "get-show-details";
$data = ["imdb_id" => $_GET["imdb_id"], ];
$result = get($endpoint, "SHOW_API_KEY", $data, $extra_headers, $isRapidAPI, $rapidAPIHost);
$query = insert("Shows", $result, $opts);
var_export($query);*/

require(__DIR__ . "/../../partials/flash.php");
