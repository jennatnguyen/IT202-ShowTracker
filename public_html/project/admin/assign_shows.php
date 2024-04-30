<?php
// Note: We need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

// Attempt to apply show associations
if (isset($_POST["users"]) && isset($_POST["shows"])) {
    $user_ids = $_POST["users"];
    $show_ids = $_POST["shows"];
    if (empty($user_ids) || empty($show_ids)) {
        flash("Both users and shows need to be selected", "warning");
    } else {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO UserShows (user_id, show_id) VALUES (:uid, :sid)
            ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)");
        foreach ($user_ids as $uid) {
            foreach ($show_ids as $sid) {
                try {
                    // Check if the association already exists
                    $stmt_check = $db->prepare("SELECT * FROM UserShows WHERE user_id = :uid AND show_id = :sid");
                    $stmt_check->execute([":uid" => $uid, ":sid" => $sid]);
                    $existing_association = $stmt_check->fetch(PDO::FETCH_ASSOC);
                    if ($existing_association) {
                        // If the association exists, remove it
                        $stmt_delete = $db->prepare("DELETE FROM UserShows WHERE user_id = :uid AND show_id = :sid");
                        $stmt_delete->execute([":uid" => $uid, ":sid" => $sid]);
                        flash("Removed show association", "info");
                    } else {
                        // If the association doesn't exist, insert it
                        $stmt->execute([":uid" => $uid, ":sid" => $sid]);
                        flash("Updated show association", "success");
                    }
                } catch (PDOException $e) {
                    flash(var_export($e->errorInfo, true), "danger");
                }
            }
        }
    }
}

// Get active shows
$active_shows = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, title FROM Shows LIMIT 25");
try {
    $stmt->execute();
    $active_shows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

// Fetch all users
$users = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, username FROM Users LIMIT 25");
try {
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

// Search for user by username if provided
$username = "";
if (isset($_POST["username"])) {
    $username = se($_POST, "username", "", false);
    if (!empty($username)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username FROM Users WHERE username LIKE :username LIMIT 25");
        try {
            $stmt->execute([":username" => "%$username%"]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
}

// Fetch all shows
$shows = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, title FROM Shows LIMIT 25");
try {
    $stmt->execute();
    $shows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

// Search for show by title if provided
$show_title = "";
if (isset($_POST["show_title"])) {
    $show_title = se($_POST, "show_title", "", false);
    if (!empty($show_title)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, title FROM Shows WHERE title LIKE :title LIMIT 25");
        try {
            $stmt->execute([":title" => "%$show_title%"]);
            $shows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
}
?>

<div class="container-fluid">
    <h1>Assign Shows</h1>
    <form method="POST">
        <div class="row mb-3">
            <div class="col">
                <?php render_input(["type" => "search", "name" => "username", "placeholder" => "Username Search", "value" => $username]); ?>
            </div>
            <div class="col">
                <?php render_input(["type" => "search", "name" => "show_title", "placeholder" => "Show Title Search", "value" => $show_title]); ?>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <?php render_button(["text" => "Search", "type" => "submit"]); ?>
            </div>
        </div>
    </form>
    <form method="POST">
        <?php if (isset($username) && !empty($username)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
        <?php endif; ?>
        <?php if (isset($show_title) && !empty($show_title)) : ?>
            <input type="hidden" name="show_title" value="<?php se($show_title, false); ?>" />
        <?php endif; ?>
        <table class="table">
            <thead>
                <th>Users</th>
                <th>Shows to Assign</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="table">
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>
                                        <?php render_input(["type" => "checkbox", "id" => "user_" . se($user, 'id', "", false), "name" => "users[]", "label" => se($user, "username", "", false), "value" => se($user, 'id', "", false)]); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                    <td>
                        <?php foreach ($shows as $show) : ?>
                            <div>
                                <?php render_input(["type" => "checkbox", "id" => "show_" . se($show, 'id', "", false), "name" => "shows[]", "label" => se($show, "title", "", false), "value" => se($show, 'id', "", false)]); ?>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php render_button(["text" => "Assign Shows", "type" => "submit", "color" => "secondary"]); ?>
    </form>
</div>

<?php
// Note: We need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>
