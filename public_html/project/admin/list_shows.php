<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");
//JN426 4/26/24
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

    //build search form
    $form = [
        ["type" => "text", "name" => "title", "placeholder" => "Show Title", "label" => "Show Title", "include_margin" => false],
        
        ["type" => "text", "name" => "imdb_rating", "placeholder" => "Show Rating", "label" => "Show Rating", "include_margin" => false],
        ["type" => "text", "name" => "rated", "placeholder" => "Show Audience", "label" => "Show Audience", "include_margin" => false],
        ["type" => "text", "name" => "genres", "placeholder" => "Genre(s)", "label" => "Genre(s)","include_margin" => false],
        ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["title" => "Title", "genres" => "Genre(s)", "imdb_rating" => "Rating", "rated" => "rated"], "include_margin" => false],
        ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],
    
        ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
    ];

error_log("Form data: " . var_export($form, true));

$total_records = get_total_count("`Shows`");
$query = "SELECT id, title, genres, imdb_id, imdb_rating, rated FROM `Shows` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    redirect($session_key);
} else {
    $session_data = session_load($session_key);
}
//JN426 4/26/24
if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) {
    if ($session_data) {
        $_GET = $session_data;
    }
}
if (count($_GET) > 0) {
    session_save($session_key, $_GET);
    $keys = array_keys($_GET);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }

    //title
    $title = se($_GET, "title", "", false);
    if (!empty($title)) {
        $query .= " AND title like :title";
        $params[":title"] = "%$title%";
    }

    $genres = se($_GET, "genres", "", false);
    if (!empty($genres) && $genres != "") {
        $query .= " AND genres like :genres";
        $params[":genres"] = "%$genres%";
    }

    $imdb_rating = se($_GET, "imdb_rating", "", false);
    if (!empty($imdb_rating) && $imdb_rating != "") {
        $query .= " AND imdb_rating like :imdb_rating";
        $params[":imdb_rating"] = "%$imdb_rating%";
    }

    $rated = se($_GET, "rated", "", false);
    if (!empty($rated) && $rated != "") {
        $query .= " AND rated like :rated";
        $params[":rated"] = "%$rated%";
    }

     //sort and order
    $sort = se($_GET, "sort", "date", false);
    if (!in_array($sort, ["title", "genres", "imdb_rating", "rated"])) {
        $sort = "date";
    }
    $order = se($_GET, "order", "desc", false);
    if (!in_array($order, ["asc", "desc"])) {
        $order = "desc";
    }

    //IMPORTANT make sure you fully validate/trust $sort and $order (sql injection possibility)
    $query .= " ORDER BY $sort $order";
    //limit
    try {
        $limit = (int)se($_GET, "limit", "10", false);
    } catch (Exception $e) {
        $limit = 10;
    }
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
    //IMPORTANT make sure you fully validate/trust $limit (sql injection possibility)
    $query .= " LIMIT $limit";
}



/************************************************************** */
//JN426 4/26/24
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
    "view_url" => get_url("admin/view_show.php"),
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