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

    if (isset($result)) {
        foreach($result as $index=>$show) {

            foreach($show as $key=>$value){

                    if($key === "release_date" && $value === "0000-00-00") {
                        $result[$index][$key] = null;
                        
                    }
                    
                   // var_export($show); 
                    
            }//end of inner foreach
        }//end of foreach
    } //end of if
    
    return $result;
}

function fetch_show_by_id($id) {
    $data = ["seriesid" => $id];
    $endpoint = "https://movies-tv-shows-database.p.rapidapi.com/";
    $isRapidAPI = true;
    $rapidAPIHost = "movies-tv-shows-database.p.rapidapi.com";
    $extra_headers["Type"] = "get-show-details";
    $result = get($endpoint, "SHOW_API_KEY", $data, $extra_headers, $isRapidAPI, $rapidAPIHost);

    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    }
     else {
        $result = [];
    }

    foreach($result as $key=>$value) {
        if(!in_array($key, ["rated","genres" ,"imdb_rating", "description"])) {
            unset($result[$key]);
        }
    }//end of second foreach

    if (isset($result["genres"]) && is_array($result["genres"])) {
        $result["genres"] = join(', ', $result["genres"]);
    }

    return $result;
}


?>