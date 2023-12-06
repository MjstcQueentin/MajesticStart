<?php
include(__DIR__."/init.php");
$db = new Database();


$categories = $db->select_newscategories();

foreach ($categories as $category_key => $category) {
    if (!NewsAggregator::is_cached($category["uuid"])) {
        $categories[$category_key]["news"] = [];
        $categories[$category_key]["sources"] = $db->select_newssources($category["uuid"]);
        foreach ($categories[$category_key]["sources"] as $source_key => $source) {
            $rss = NewsAggregator::load_rss($source['uuid'], $source['rss_feed_url']);
            $categories[$category_key]["news"] = array_merge($categories[$category_key]["news"], NewsAggregator::transform($rss["channel"]["item"], $source));
        }

        NewsAggregator::aggregate($category["uuid"], $categories[$category_key]["news"]);
    }
}