<?php
//note we need to go up 1 more directory
//JN425 4/26/24
require(__DIR__ . "/../../../partials/nav.php");

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
            //    $quote["is_api"] = 1;
            }
        } else if ($action === "create") {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ["title", "release_date", "description","rated","imdb_rating", "genres"])) {
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
        //JN426 4/26/24
        $opts =
            ["debug" => true, "update_duplicate" => true, "columns_to_update" => []];
        $result = insert("Shows", $quote, $opts);
        if (!$result) {
            flash("Unhandled error", "warning");
        } else {
            flash("Created record with id " . var_export($result, true), "success");
        }
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, false));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if ($e2->errorInfo[1] == 1062) {
            flash("An entry for this title already exists for today", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, false));
        flash("Invalid data records", "danger");
    }
}

//TODO handle manual create stock
?>


<script>//JN426 4/26/24
function validation(form) {
    let title = form.title.value;
    let genres = form.genres.value;
    let rated = form.rated.value;
    let imdb_rating = form.imdb_rating.value;
    let description = form.description.value;
    let valid = true;

    if(title.length == 0) {
        flash("Must include title", "warning");
        valid = false;
    }

    if(genres.length == 0) {
        flash("Must include genre", "warning");
        valid = false;
    }

    if((imdb_rating.length == 0)) {
        flash("Must include rating", "warning");
        valid = false;
    }

    if(isNaN(imdb_rating)) {
        flash("Rating must be a number", "warning");
        valid = false;
    }

    if((description.length == 0)) {
        flash("Must include description", "warning");
        valid = false;
    }
    
    return valid;
}
</script>

<?php
if (isset($_POST["create"])){
    $title = se($_POST, "title", "", false);
    $genres = se($_POST, "genres", "", false);
    $rated = se($_POST, "rated", "", false);
    $imdb_rating = se($_POST, "imdb_rating", "", false);
    $description = se($_POST, "description", "", false);
    //JN426 4/26/24
    $hasError = false;

    if (empty($title)) {
        flash("Title must not be empty", "danger");
        $hasError = true;
    }

    if (empty($genres)) {
        flash("genre must not be empty", "danger");
        $hasError = true;
    }
    if (empty($rated)) {
        flash("audience rating must not be empty", "danger");
        $hasError = true;
    }

    if (empty($imdb_rating)) {
        flash("rating must not be empty", "danger");
        $hasError = true;
    }
    if (empty($description)) {
        flash("description password must not be empty", "danger");
        $hasError = true;
    }
    
    if (!$hasError) {
        //TODO 4
        
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Shows (title, genres, rated, imdb_rating, description) VALUES(:title, :genres, :rated, :imdb_rating, :description)");
        try {
            $stmt->execute([":title" => $title, ":genres" => $genres, ":rated" => $rated, ":imdb_rating" => $imdb_rating, ":description" => $description]);
            flash("Successfully created!", "success");
        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
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
<!--JN426 4/26/24-->
    <div id="add" style="display: none;" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "text", "name" => "title", "placeholder" => "Show Title", "label" => "Show Title", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "date", "name" => "release_date", "placeholder" => "Release Date", "label" => "Release Date","rules"=>["required" => "required"]]); ?>
           
            <?php render_input(["type" => "text", "name" => "description", "placeholder" => "Show Description", "label" => "Show Description", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "imdb_id", "placeholder" => "Show Rating (/10)", "label" => "Show Rating", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "rated", "placeholder" => "Show Audience (E.G. TV-PG)", "label" => "Show Audience", "rules"=>["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "genres", "placeholder" => "Genres", "label" => "Genre(s)", "rules"=>["required" => "required"]]); ?>

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