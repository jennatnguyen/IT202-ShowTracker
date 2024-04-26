<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    //die(header("Location: $BASE_PATH" . "/home.php"));
    redirect("home.php");
}
?>

<?php

//TODO handle show fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $title =  strtoupper(se($_POST, "title", "", false));
    $quote = [];
    if ($title) {
        if ($action === "fetch") {
            $result = fetch_show_by_title($title);
            error_log("Data from API" . var_export($result, true));
            if ($result) {
                $quote = $result;
                $quote["is_api"] = 1;
            }
        }  else {
        flash("You must provide a title", "warning");
    }
    //insert data
    try {
        //optional options for debugging and duplicate handling
        $opts =
            ["debug" => true, "update_duplicate" => false, "columns_to_update" => []];
        $result = insert("Shows", $quote, $opts);
        if (!$result) {
            flash("Unhandled error", "warning");
        } else {
            flash("Created record with id " . var_export($result, true), "success");
        }
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if ($e2->errorInfo[1] == 1062) {
            flash("An entry for this title already exists for today", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
    }
}
}
//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Fetch Show</h3>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "search", "name" => "title", "placeholder" => "Show Title","rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value"=>"fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit"]); ?>
        </form>
    </div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>