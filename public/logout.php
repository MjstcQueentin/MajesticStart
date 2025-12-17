<?php
include(__DIR__ . "/../init.php");

// Handle account deletion
if(isset($_GET['deleteaccount']) && $_GET['deleteaccount'] == '1') {
    model("UserModel")->delete_one($_SESSION["user_uuid"]);
}

if(SessionUtils::is_logged_in()) {
    SessionUtils::destroy();
}
http_response_code(307);
header('Location: /index.php');