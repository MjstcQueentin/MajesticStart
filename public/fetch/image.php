<?php
include(__DIR__ . "/../../init.php");

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(400);
    exit;
}
$url = $_GET["url"];

// Lire les URLs base64 directement
if (substr($url, 0, 5) == "data:") {
    $matches = [];
    preg_match_all("/^data:(.+);base64,(.+)$/", $url, $matches);

    $image = base64_decode($matches[2][0]);
    $contentType = $matches[1][0];

    header("Content-Type: $contentType");
    echo $image;
    exit;
}

// Tenter de lire le cache
$cacheFilename = base64_encode($url);
$cachePath = __DIR__ . "/../assets/image-cache/$cacheFilename.pic";
if (is_file($cachePath)) {
    $image = file_get_contents($cachePath);
    $contentType = mime_content_type($cachePath);
    header("Content-Type: $contentType");
    echo $image;
    exit;
}

try {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR => true
    ]);

    $image = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    file_put_contents($cachePath, $image);

    if (stripos($contentType, "image/") === false) {
        http_response_code(500);
        exit;
    }

    header("Content-Type: $contentType");
    echo $image;
} catch (Exception $ex) {
    http_response_code(500);
    exit;
}
