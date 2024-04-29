<?php
//note we need to go up 1 more directory
//jn426 4/26/24
require(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
?>

<?php
$id = se($_GET, "id", -1, false);

$show = [];
if ($id > -1) {
    //fetch jn426 4/26/24
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

        //    jn426 4/26/24

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
    //fetch jn426 4/26/24


?>
<div class="container-fluid">
    
    <div>
        <div>
            <a href="<?php echo get_url("shows.php"); ?>" class="btn btn-secondary">Back</a>
        </div>
        <div style="text-align: right;">
        <a href="<?php echo get_url("admin/edit_show.php?id=".$id); ?>" class="btn btn-secondary">Edit</a>
        <a href="<?php echo get_url("admin/delete_show.php?id=".$id); ?>" class="btn btn-secondary">Delete</a>
        <a href="<?php echo get_url("brokers.php"); ?>" class="btn btn-secondary">Back</a>
    </div>

    <?php render_single_show_card($show); ?>

</div>


    
</div>
</div>


<?php

require_once(__DIR__ . "/../../partials/flash.php");
?>