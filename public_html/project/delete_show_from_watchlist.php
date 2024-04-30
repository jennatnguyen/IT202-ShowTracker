<?php

session_start();
require(__DIR__ . "/../../lib/functions.php");

$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid id passed to delete", "danger");
    redirect("admin/watchlist_associations.php");
}

$db = getDB();
$query = "DELETE FROM `UserShows` WHERE show_id = :id AND user_id = :user_id";
try {
    // Fetch user_id from session jn426 4/30/24
    $user_id = get_user_id();
    
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id, ":user_id" => $user_id]);
    flash("Deleted record with id $id from the watchlist", "success");
} catch (Exception $e) {
    error_log("Error deleting show $id from watchlist: " . $e->getMessage());
    flash("Error deleting record", "danger");
}
redirect("admin/watchlist_associations.php");