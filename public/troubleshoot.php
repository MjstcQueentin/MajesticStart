<?php 
include(__DIR__ . "/../init.php");
SessionUtils::get_user_data();
$db = new Database();
$db->insert_or_update_user($_SESSION["user_uuid"], $_SESSION["token"]);
?>
<pre><?=print_r($_SESSION, true); ?></pre>