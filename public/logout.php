<?php
include(__DIR__ . "/../autoload.php");

// Handle account deletion
if(isset($_GET['deleteaccount']) && $_GET['deleteaccount'] == '1') {
    model("UserModel")->delete_one($_SESSION["user_uuid"]);
    model("BookmarkModel")->delete_all_of_user($_SESSION["user_uuid"]);
}

if(MajesticStart\Core\Session::isLoggedIn()) {
    MajesticStart\Core\Session::destroy();
}
http_response_code(307);
header('Location: /index.php');
