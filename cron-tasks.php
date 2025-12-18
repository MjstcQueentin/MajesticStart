<?php

/**
 * Mise à jour automatique des sources d'informations dans Majestic Start
 * @copyright 2025 Quentin Pugeat
 * @license MIT License
 */

$log_path = __DIR__ . "/writable/logs/cron-task-" . date("Y-m-d") . ".log";
$log = fopen($log_path, "a");

include(__DIR__ . "/init.php");
set_error_handler(function (int $errno, string $errstr, ?string $errfile, ?int $errline) use (&$log) {
    error_log(sprintf("%s in %s:%d", $errstr, $errfile, $errline));
    if ($log) fwrite($log, date('Y-m-d H:i:s') . " $errstr ($errfile:$errline)" . PHP_EOL);
});
set_exception_handler(function ($ex) use (&$log) {
    error_log(sprintf("%s in %s:%d", $ex->getMessage(), $ex->getFile(), $ex->getLine()));
    if ($log) fwrite($log, date('Y-m-d H:i:s') . " " . str_replace(PHP_EOL, " ", $ex->getMessage()) . PHP_EOL);
});

DatabaseConnection::instance()->start_transaction();
model('NewsPostModel')->delete_all();

// Lire les fluxs un par un
$newsFeeds = model('NewsFeedModel')->select_all();
foreach ($newsFeeds as $newsFeed) {
    try {
        // Lire et parser le flux RSS
        $rss = NewsAggregator::load_rss($newsFeed['uuid'], $newsFeed['rss_feed_url']);
        $transformed = NewsAggregator::transform($rss->channel->item, $newsFeed['uuid']);

        // Insérer les articles nouvellement découverts dans la base de données
        $inserted = model('NewsPostModel')->insert($transformed);

        // Marquer la source comme étant en fonction
        model('NewsFeedModel')->update_one($newsFeed['uuid'], ["access_ok" => 1]);
    } catch (Exception $ex) {
        // En cas de problème avec un flux, marquer la source comme étant en panne
        model('NewsFeedModel')->update_one($newsFeed['uuid'], ["access_ok" => 0]);
        if ($log) fwrite($log, date('Y-m-d H:i:s') . " " . str_replace(PHP_EOL, " ", $ex->getMessage()) . PHP_EOL);
    }
}

DatabaseConnection::instance()->commit();
if ($log) fclose($log);
