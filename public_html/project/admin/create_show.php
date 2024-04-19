<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
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
        } else if ($action === "add") {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ["title", "release_date", "imdb_id"])) {
                    unset($_POST[$k]);
                }
                $quote = $_POST;
                error_log("Cleaned up POST: " . var_export($quote, true));
            }
        }
    } else {
        flash("You must provide a title", "warning");
    }
    //insert data
    try {
        //optional options for debugging and duplicate handling
        $opts =
            ["debug" => true, "update_duplicate" => false, "columns_to_update" => []];
        $result = insert("Shows", $result, $opts);
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
//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Add or Fetch Show</h3>
    <ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" href="#" style="color:white; background-color: #991010;" onclick="switchTab('add')" >Fetch</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" style="color:white; background-color: #C61515" onclick="switchTab('fetch')">Add</a>
    </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "search", "name" => "title", "placeholder" => "Show Title","rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value"=>"fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit"]); ?>
        </form>
    </div>

    <div id="add" style="display: none;" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "text", "name" => "title", "placeholder" => "Show Title", "label" => "Show Title", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "date", "name" => "title", "placeholder" => "Release Date", "label" => "Release Date","rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "title", "placeholder" => "IMDB ID", "label" => "IMDB ID", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "title", "placeholder" => "Show Description", "label" => "Show Description", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "title", "placeholder" => "Show Rating", "label" => "Show Rating", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "title", "placeholder" => "Show Popularity", "label" => "Show Popularity", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "title", "placeholder" => "Poster","label" => "Poster", "rules"=>["required" => "required"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action", "value"=>"create"]); ?>
            <?php render_button(["text" => "Add", "type" => "submit"]); ?>
        </form>
    </div>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>