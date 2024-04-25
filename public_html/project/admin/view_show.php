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
    $id = se($show, "imdb_id", "", false);
    
    $quote = [];
        if ($id) {
    
            $result = fetch_show_by_id($id);
            // error_log("Data from API" . var_export($result, true));
            if ($result) {
                foreach ($show["imdb_id"] as $index=>$id) {
                    foreach($id as $key=>$value) {
                        if(!in_array($key, ["popularity,","irating", "description"])) {
                            unset($show["imdb_id"][$k]);
                        }//end of if
                    }//end of second foreach
                }//end of foreach

                $quote = $result;
                $quote["is_api"] = 1;
                $opts = ["debug" => false, "update_duplicate" => true, "columns_to_update"=>[]];
                var_export($query);
                $query = insert("Shows", $result, $opts); 

            }
        }
}

/*foreach ($show as $index => $id) {

        foreach($id as $key=>$value) {
            if (is_null($id)) {
                //$data = ["id" => $show["imdb_id"]];
                $opts = ["debug" => false, "update_duplicate" => true, "columns_to_update"=>[]];
                var_export($query);
                $query = insert("Shows", $result, $opts); 
            }
        }

}*/

?>
<div class="container-fluid">
    <h3><?php se($show, "", ""); ?></h3>

    <?php

    echo '<div style="text-align: center;">';
    echo "<h2>{$show['title']}</h2>";
    echo "<p><strong>Released:</strong> {$show['release_date']} </p>";
    echo "<p><strong>Rating:</strong> {$show['irating']}</p>";
    echo "<p><strong>Popularity:</strong> {$show['popularity']}</p>";
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