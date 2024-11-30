<?php
include(__DIR__ . "/../../init.php");

header('Content-Type: application/json');

if (!SessionUtils::is_logged_in()) {
    http_response_code(401);
    die;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $bookmark = [
            "uuid" => null,
            "name" => $_POST["name"],
            "url" => $_POST["url"],
            "icon" => BookmarkUtils::iconFrom($_POST["url"]),
            "user_id" => $_SESSION["user_uuid"]
        ];
        $model = model('BookmarkModel');
        $model->insert_one($bookmark);
        $bookmark["uuid"] = $model->insert_id();
        http_response_code(201);
        echo json_encode($bookmark);
        break;
    case 'DELETE':
        if (model('BookmarkModel')->delete([
            "uuid" => $_REQUEST['uuid'],
            "user_id" => $_SESSION["user_uuid"]
        ])) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        break;
    default:
        http_response_code(405);
        break;
}
