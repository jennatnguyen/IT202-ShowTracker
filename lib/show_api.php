<?php 
function fetch_show_by_title($title) {
    $data = ["title" => $_GET["title"]];
    $endpoint = "https://movies-tv-shows-database.p.rapidapi.com/";
    $isRapidAPI = true;
    $rapidAPIHost = "movies-tv-shows-database.p.rapidapi.com";
    $extra_headers["Type"] = "get-shows-by-title";
    $result = get($endpoint, "SHOW_API_KEY", $data, $extra_headers, $isRapidAPI, $rapidAPIHost);
    return $result;
}
?>