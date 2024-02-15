<?php

/**
 * Mise à jour automatique des sources d'informations dans Majestic Start
 * @copyright 2024 Quentin Pugeat
 * @license MIT License
 */

include(__DIR__ . "/init.php");

$db = new Database();

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
        }
    }

    NewsAggregator::aggregate($category["uuid"], $categories[$category_key]["news"]);
}
