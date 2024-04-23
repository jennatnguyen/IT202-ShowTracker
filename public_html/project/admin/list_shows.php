<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

//if the field is NULL, fetch it
/*$result = [];
if (isset($_GET["imbd_id"])) {

    $data = ["imbd_id" => $_GET["imbd_id"]];
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
}

    if (isset($result)) : 
     foreach($result as $index=>$id) : 

         foreach($id as $key=>$value): 
            if (!in_array($k, ["description", "irating", "popularity"])) {
                $opts = ["debug" => false, "update_duplicate" => true, "columns_to_update"=>[]];
                var_export($query);
                $query = insert("Shows", $result, $opts); 
                var_export($query); 
            }
                
         endforeach;  
     endforeach; 
    endif; */

$query = "SELECT id, title, release_date, imdb_id, description, irating, popularity FROM `Shows` ORDER BY created DESC LIMIT 25";
$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching shows " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

$table = [
    "data" => $results, "title" => "Latest Shows", "ignored_columns" => ["id"],
    "edit_url" => get_url("admin/edit_show.php"),
    "delete_url" => get_url("admin/delete_show.php")
];
?>

<div class="container-fluid">
    <h3>List Shows</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">

            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>

        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_table($table); ?>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>