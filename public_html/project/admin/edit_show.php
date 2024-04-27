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
//TODO handle show fetch
if (isset($_POST["title"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["title", "release_date", "imdb_id", "description", "rated", "imdb_rating", "genres"])) {
            unset($_POST[$k]);
        }
        $quote = $_POST;
        error_log("Cleaned up POST: " . var_export($quote, true));
    }
    //insert data
    $db = getDB();
    $query = "UPDATE `Shows` SET ";

    $params = [];
    //per record
    foreach ($quote as $k => $v) {

        if ($params) {
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of sql injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Updated record ", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

$show = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT title, release_date, imdb_id, description, rated, imdb_rating, genres FROM `Shows` WHERE id = :id";
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
if ($show) {
    $form = [
        ["type" => "text", "name" => "title", "placeholder" => "Show Title", "label" => "Show Title", "rules"=>["required" => "required"]],
        ["type" => "date", "name" => "release_date", "placeholder" => "Release Date", "label" => "Release Date","rules"=>["required" => "required"]],
        
        ["type" => "text", "name" => "description", "placeholder" => "Show Description", "label" => "Show Description", "rules"=>["required" => "required"]],
        ["type" => "text", "name" => "imdb_rating", "placeholder" => "Show Rating", "label" => "Show Rating", "rules"=>["required" => "required"]],
        ["type" => "text", "name" => "rated", "placeholder" => "Show Audience", "label" => "Show Audience", "rules"=>["required" => "required"]],
        ["type" => "text", "name" => "genres", "placeholder" => "Genre(s)","label" => "Genre(s)", "rules"=>["required" => "required"]],

    ];
    $keys = array_keys($show);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $show[$v["name"]];
        }
    }
}
//TODO handle manual create show
?>

<script>
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
if (isset($_POST["title"]) && isset($_POST["genres"]) && isset($_POST["rated"]) && isset($_POST["imdb_rating"]) && isset($_POST["description"])){
    $title = se($_POST, "title", "", false);
    $genres = se($_POST, "genres", "", false);
    $rated = se($_POST, "rated", "", false);
    $imdb_rating = se($_POST, "imdb_rating", "", false);
    $description = se($_POST, "description", "", false);
    //TODO 3
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
        flash("description must not be empty", "danger");
        $hasError = true;
    }
    
    if (!$hasError) {
        //TODO 4
        $params = [
            ":title" => $title, 
            ":genres" => $genres, 
            ":rated" => $rated, 
            ":imdb_rating" => $imdb_rating, 
            ":description" => $description
        ];

        $db = getDB();
        $stmt = $db->prepare("UPDATE Shows SET title = :title, genres = :genres, rated = :rated, imdb_rating = :imdb_rating, description = :description WHERE id = :id");
        try {
            $stmt->execute([$params]);
            flash("Successfully updated!", "success");
        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
} ?>

<div class="container-fluid">
    <h3>Edit Show</h3>
    <div>
        <a href="<?php echo get_url("admin/list_shows.php"); ?>" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST">
        <?php foreach ($form as $k => $v) {

            render_input($v);
        } ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Update"]); ?>
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>