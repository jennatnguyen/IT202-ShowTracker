<?php

session_start();
require(__DIR__ . "/../../lib/functions.php");

$id = se($_GET, "id", -1, false);
if ($id < 1) {
    flash("Invalid id passed to delete", "danger");
    redirect("my_shows.php");
}

$db = getDB();
$query = "DELETE FROM `UserShows` WHERE show_id = :id";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":id" => $id]);
    flash("Deleted record with id $id", "success");
} catch (Exception $e) {
    error_log("Error deleting stock $id" . var_export($e, true));
    flash("Error deleting record", "danger");
}
redirect("my_shows.php"); //jn426 4/24/30