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
        if (!in_array($k, ["title", "release_date", "imdb_id", "description", "irating", "popularity"])) {
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
    $query = "SELECT title, release_date, imdb_id, description, irating, popularity FROM `Shows` WHERE id = :id";
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
        ["type" => "text", "name" => "imbd_id", "placeholder" => "IMDB ID", "label" => "IMDB ID", "rules"=>["required" => "required"]],
        ["type" => "text", "name" => "description", "placeholder" => "Show Description", "label" => "Show Description", "rules"=>["required" => "required"]],
        ["type" => "text", "name" => "irating", "placeholder" => "Show Rating", "label" => "Show Rating", "rules"=>["required" => "required"]],
        ["type" => "text", "name" => "popularity", "placeholder" => "Show Popularity", "label" => "Show Popularity", "rules"=>["required" => "required"]],
        ["type" => "text", "name" => "poster", "placeholder" => "Poster","label" => "Poster Filename", "rules"=>["required" => "required"]],

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