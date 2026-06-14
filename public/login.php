<?php
include __DIR__ . "/../autoload.php";

// When MajestiCloud is disabled, we redirect to the front page
if (!config("majestiCloudEnabled")) {
    http_response_code(307);
    header("Location: /index.php");
    exit();
}

if (!isset($_GET["code"])) {
    // Redirect to MajesticCloud

    http_response_code(307);
    header("Location: " . config("majestiCloudApiUri") . "/oauth/authorize.php?client_uuid=" . config("majestiCloudClientId") . "&redirect_uri=" . urlencode(config("rootUri") . "/login.php"));
} else {
    // Get token
    MajesticStart\Core\Session::initSession($_GET["code"]);

    model("UserModel")->insert_one([
        "majesticloud_user_id" => $_SESSION["user_uuid"],
        "majesticloud_session_token" => $_SESSION["token"],
    ]);

    http_response_code(307);
    header("Location: /index.php");
}
