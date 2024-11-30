<?php

/**
 * Mise à jour automatique des sources d'informations dans Majestic Start
 * @copyright 2024 Quentin Pugeat
 * @license MIT License
 */

$log_path = __DIR__ . "/logs/cron-task-" . date("YmdHis") . "-" . getmypid() . ".log";
$log = fopen($log_path, "a");

include(__DIR__ . "/init.php");
set_error_handler(function (int $errno, string $errstr, string $errfile = null, int $errline = null, array $errcontext = null) use (&$log) {
    echo "$errstr ($errfile:$errline)".PHP_EOL;
    if ($log) fwrite($log, date('Y-m-d H:i:s') . " $errstr ($errfile:$errline)" . PHP_EOL);
});
set_exception_handler(function ($ex) use (&$log) {
    echo $ex->__toString() . PHP_EOL;
    if ($log) fwrite($log, date('Y-m-d H:i:s') . " " . str_replace(PHP_EOL, " ", $ex->getMessage()) . PHP_EOL);
});

// Mise à jour des informations dans Majestic Start
$categories = model('NewsCategoryModel')->select_all();
foreach ($categories as $category_key => $category) {
    $categories[$category_key]["news"] = [];
    $categories[$category_key]["sources"] = model('NewsSourceModel')->select_in_category($category["uuid"]);
    foreach ($categories[$category_key]["sources"] as $source_key => $source) {
        try {
            $rss = NewsAggregator::load_rss($source['uuid'], $source['rss_feed_url']);
            $categories[$category_key]["news"] = array_merge($categories[$category_key]["news"], NewsAggregator::transform($rss->channel->item, $source));
            model('NewsSourceModel')->update_one($source["uuid"], ["access_ok" => 1]);
        } catch (Exception $ex) {
            if ($source['access_ok'] == 1) {
                if ($log) fwrite($log, date('Y-m-d H:i:s') . " [" . $source['rss_feed_url'] . "] " . str_replace(PHP_EOL, " ", $ex->getMessage()) . PHP_EOL);
                model('NewsSourceModel')->update_one($source["uuid"], ["access_ok" => 0]);
            }
        }
    }

    NewsAggregator::aggregate($category["uuid"], $categories[$category_key]["news"]);
}

if ($log) fclose($log);
