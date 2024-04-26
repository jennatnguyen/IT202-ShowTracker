<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php
$id = se($_GET, "id", -1, false);

$show = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT * FROM `Shows` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $show = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    redirect("admin/list_shows.php");
}

if (isset($show["imdb_id"])) {
    $imbdid = se($show, "imdb_id", "", false);
    
    $quote = [];
        if ($imbdid) {

        //    error_log(var_export($imbdid, false));

            $result = fetch_show_by_id($imbdid);
             error_log("Data from API" . var_export($result, true));
            if ($result) {
                $quote = $result;
                $quote["is_api"] = 1;
                $opts = ["debug" => false, "update_duplicate" => true, "columns_to_update"=>[]];
              //  var_export($query);
                $query = update("Shows", $result, $id); 

            }
        }
}

foreach ($show as $key => $value) {
    if (is_null($value)) {
        $show[$key] = "N/A";
    }
}


?>
<div class="container-fluid">
    <h3><?php se($show, "", ""); ?></h3>

    <?php

    echo '<div style="text-align: center;">';
    echo "<h2>{$show['title']}</h2>";
    echo "<p><strong>Released:</strong> {$show['release_date']} </p>";
    echo "<p><strong>Audience:</strong> {$show['rated']}</p>";
    echo "<p><strong>Rating:</strong> {$show['imdb_rating']}</p>";
    echo "<p><strong>Description:</strong> {$show['description']}</p>";

    echo '</div>';
?>

    <div>
        <a href="<?php echo get_url("admin/list_shows.php"); ?>" class="btn btn-secondary">Back</a>
    </div>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>