<?php 
function fetch_show_by_title($title) {
    $data = ["title" => $title];
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
    return $result;
}

function fetch_show_by_id($id) {
    $data = ["imdb_id" => $id];
    $endpoint = "https://movies-tv-shows-database.p.rapidapi.com/";
    $isRapidAPI = true;
    $rapidAPIHost = "movies-tv-shows-database.p.rapidapi.com";
    $extra_headers["Type"] = "get-show-details";
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
    return $result;
}
?>