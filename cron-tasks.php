<?php

/**
 * Mise à jour automatique des sources d'informations dans Majestic Start
 * @copyright 2024 Quentin Pugeat
 * @license MIT License
 */

include(__DIR__ . "/init.php");

$db = new Database();

$log_path = __DIR__ . "/cron-tasks.log";
$log = fopen($log_path, "a");

// Mise à jour des informations dans Majestic Start
$categories = $db->select_newscategories();
foreach ($categories as $category_key => $category) {
    $categories[$category_key]["news"] = [];
    $categories[$category_key]["sources"] = $db->select_newssources($category["uuid"]);
    foreach ($categories[$category_key]["sources"] as $source_key => $source) {
        try {
            $rss = NewsAggregator::load_rss($source['uuid'], $source['rss_feed_url']);
            $categories[$category_key]["news"] = array_merge($categories[$category_key]["news"], NewsAggregator::transform($rss["channel"]["item"], $source));
        } catch (Exception $ex) {
            fwrite(STDERR, $ex->__toString());
            if ($log) fwrite($log, date('Y-m-d') . ": " . str_replace(PHP_EOL, " ", $ex->__toString()) . PHP_EOL);
        }
    }

    NewsAggregator::aggregate($category["uuid"], $categories[$category_key]["news"]);
}

// Nettoyage du cache des images
// Supprimer mes images plus vieilles qu'un jour
$imageCachePath = __DIR__ . "/public/assets/image-cache";
foreach (scandir($imageCachePath) as $fileName) {
    if (filemtime($imageCachePath . "/" . $fileName) < time() - (24 * 60 * 60)) {
        unlink($imageCachePath . "/" . $fileName);
    }
}

if ($log) fclose($log);
