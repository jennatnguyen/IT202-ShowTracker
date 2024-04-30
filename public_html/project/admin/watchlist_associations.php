<?php
require_once(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/functions.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_associations"])) {
    $username = isset($_GET['username']) ? $_GET['username'] : '';

    $db = getDB();
    $query = "DELETE FROM UserShows WHERE user_id IN (SELECT id FROM Users WHERE username = :username)";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([':username' => $username]);
        flash("Associations for user '$username' cleared successfully", "success");
        redirect("admin/watchlist_associations.php");
        exit;
    } catch (PDOException $e) {
        error_log("Error removing associations for user '$username': " . $e->getMessage());
        flash("An error occurred while removing associations", "danger");
    }
}


$username = isset($_GET['username']) ? $_GET['username'] : '';
$genres = isset($_GET['genres']) ? $_GET['genres'] : '';
$title = isset($_GET['title']) ? $_GET['title'] : '';
$released = isset($_GET['imdb_rating']) ? $_GET['imdb_rating'] : '';
$released = isset($_GET['rated']) ? $_GET['rated'] : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Default to 10
$limit = max(1, min($limit, 100));

$query = "SELECT s.id, title, genres, imdb_id, imdb_rating, rated,
COUNT(DISTINCT us.user_id) AS watchlist_count, u.username AS Username
FROM `UserShows` us
JOIN `Shows` s ON us.show_id = s.id 
JOIN Users u ON us.user_id = u.id
WHERE 1 = 1";
$params = [];
  //username
  $username = se($_GET, "username", "", false);
  if (!empty($username)) {
      $query .= " AND u.username like :username";
      $params[":username"] = "%$username%";
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

$query .= " GROUP BY s.id, title, genres, imdb_rating, rated, u.id, u.username
            ORDER BY s.created DESC LIMIT $limit";

// Execute the query
$db = getDB();
$stmt = $db->prepare($query);
try {
    $stmt->execute($params);
    $results = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching shows: " . $e->getMessage());
    flash("An error occurred while fetching shows", "danger");
}

$totalItemsQuery = "SELECT COUNT(*) AS total_items FROM UserShows";
$stmt = $db->prepare($totalItemsQuery);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalItemsCount = $row['total_items'];

?>

<div style="text-align: center;">
    <div class="container-fluid">
        <h2>Watchlist Associations</h2>
        <h5>Total Shows on Watchlists: <?php echo $totalItemsCount; ?></h5>
        <h5>Total Items On Page: <?php echo count($results); ?></h5>

        <div style="text-align: left;">
        <form method="GET" class="row g-3 justify-content-center">
            <div class="col-auto">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter username">
            </div>
            <div class="col-auto">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter title">
            </div>
            <div class="col-auto">
                <label for="genres" class="form-label">Genres</label>
                <input type="text" class="form-control" id="genres" name="genres" value="<?php echo htmlspecialchars($genres); ?>" placeholder="Enter genres">
            </div>
            <div class="col-auto">
                <label for="imdb_rating" class="form-label">Rating</label>
                <input type="text" class="form-control" id="imdb_rating" name="imdb_rating" value="<?php echo htmlspecialchars($imdb_rating); ?>" placeholder="Enter rating">
            </div>
            <div class="col-auto">
                <label for="rated" class="form-label">Audience</label>
                <input type="text" class="form-control" id="rated" name="rated" value="<?php echo htmlspecialchars($rated); ?>" placeholder="Enter audience">
            </div>
            <div class="col-auto">
                <label for="limit" class="form-label">Limit</label>
                <input type="number" class="form-control" id="limit" name="limit" value="<?php echo $limit; ?>" min="1" max="100" placeholder="Enter limit">
            </div>
            </div>
            <div class="col-auto">
                <div style="text-align: center;">
                    <button type="submit" class="btn btn-primary mb-3">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="post" style="text-align: center;">
        <button type="submit" name="remove_associations" class="btn btn-success mb-3">Clear Associations</button>
    </form>


        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Watchlist Count</th>
                    <th>Title</th>
                    <th>Genre(s)</th>
                    <th>Rating</th>
                    <th>Audience</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)) : ?>
                    <tr>
                        <td colspan="7">No results available.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($results as $show) : ?>
                        <tr>
                            <td>
                                <a href="../profile.php" class="btn btn-secondary"><?php echo htmlspecialchars($show['Username']); ?></a>
                            </td>
                            <td><?php echo $show['watchlist_count']; ?></td>
                            <td><?php echo htmlspecialchars($show['title']); ?></td>
                            <td><?php echo htmlspecialchars($show['genres']); ?></td>
                            <td><?php echo htmlspecialchars($show['imdb_rating']); ?></td>
                            <td><?php echo htmlspecialchars($show['rated']); ?></td>
                            <td>
                                <a href="../single_view.php?id=<?php echo $show['id']; ?>" class="btn btn-info">View Show</a>
                                <a href="<?php echo get_url('delete_show_from_watchlist.php?id=' . $show["id"]); ?>" class="btn btn-danger">Delete from Watchlist</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    

    <?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>

