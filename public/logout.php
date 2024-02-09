<?php
include(__DIR__ . "/../init.php");

if(SessionUtils::is_logged_in()) {
    SessionUtils::destroy();
}
http_response_code(307);
header('Location: /index.php');