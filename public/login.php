<?php
include(__DIR__ . "/../init.php");

if(!isset($_GET['code'])) {
    http_response_code(307);
    header('Location: '.MAJESTICLOUD_URI.'/oauth/authorize.php?client_uuid='.MAJESTICLOUD_CLIENT_ID.'&redirect_uri='.urlencode(ENVIRONMENT_ROOT.'/login.php'));
} else {
    SessionUtils::init_session($_GET['code']);
    
    $db = new Database();
    $db->insert_or_update_user($_SESSION["user_uuid"], $_SESSION["token"]);

    http_response_code(307);
    header('Location: /index.php');
}