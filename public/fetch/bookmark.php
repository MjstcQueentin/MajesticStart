<?php
include(__DIR__ . "/../../init.php");
$db = new Database();

header('Content-Type: application/json');

if (!SessionUtils::is_logged_in()) {
    http_response_code(401);
    die;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $bookmark = $db->insert_bookmark(
            $_POST["name"],
            $_POST["url"],
            BookmarkUtils::iconFrom($_POST["url"]),
            $_SESSION["user_uuid"]
        );
        http_response_code(201);
        echo json_encode($bookmark);
        break;
    case 'DELETE':
        if ($db->delete_bookmark($_REQUEST['uuid'], $_SESSION["user_uuid"])) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        break;
    default:
        http_response_code(405);
        break;
}
