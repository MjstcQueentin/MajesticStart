<?php
include(__DIR__ . "/../../autoload.php");

$id = $_GET["id"];
if (empty($id)) {
    http_response_code(400);
    return;
}

$search_engine = model("SearchEngineModel")->select_one($id);
if (empty($id)) {
    http_response_code(404);
    return;
}

if (MajesticStart\Core\Session::isLoggedIn()) {
    model("UserModel")->update_one($_SESSION["user_uuid"], [
        "set_searchengine" => $id
    ]);
}

header('Content-Type: application/json');
echo json_encode($search_engine);
