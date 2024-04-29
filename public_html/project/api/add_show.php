<?php
// This is an internal API endpoint to receive data and do something with it
// this is not a standalone page
//Note: no nav.php here because this is a temporary stop, it's not a user page
require(__DIR__ . "/../../../lib/functions.php");
session_start();
if (isset($_GET["show_id"]) && is_logged_in()) {
    //TODO implement purchase logic (for now it's all free)
    $db = getDB();
    $query = "INSERT INTO `UserShows` (user_id, show_id) VALUES (:user_id, :show_id)";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id" => get_user_id(), ":show_id" => $_GET["show_id"]]);
        flash("Successfully added to watchlist", "success");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] === 1062) {
            flash("This show isn't available", "danger");
        } else {
            flash("Unhandled error occurred", "danger");
        }
        error_log("Error adding show: " . var_export($e, true));
    }
}

//for now I'll redirect, but if I later use AJAX I need to send a reply instead
redirect("shows.php");