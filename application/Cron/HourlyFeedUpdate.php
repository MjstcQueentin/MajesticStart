<?php

namespace MajesticStart\Cron;

use Exception;
use MajesticStart\Core\NewsAggregator;
use MajesticStart\Database\DatabaseConnection;

/**
 * Mise à jour automatique des sources d'informations dans Majestic Start
 * @copyright 2025 Quentin Pugeat
 * @license MIT
 */
class HourlyFeedUpdate extends CronTask
{
    function __construct()
    {
        $this->cronTaskIdentifier = "hourly-feed-update";
    }

    public function invoke($args = []): void
    {
        DatabaseConnection::instance()->start_transaction();
        model("NewsPostModel")->delete_all();

        // Lire les fluxs un par un
        $newsFeeds = model("NewsFeedModel")->select_all();
        foreach ($newsFeeds as $newsFeed) {
            try {
                // Lire et parser le flux RSS
                $rss = NewsAggregator::load_rss($newsFeed["uuid"], $newsFeed["rss_feed_url"]);
                $transformed = NewsAggregator::transform($rss->channel->item, $newsFeed["uuid"]);

                // Insérer les articles nouvellement découverts dans la base de données
                $inserted = model("NewsPostModel")->insert($transformed);

                // Marquer la source comme étant en fonction
                model("NewsFeedModel")->update_one($newsFeed["uuid"], ["access_ok" => 1]);
            } catch (Exception $ex) {
                $this->log("error", $ex->getMessage());

                if(get_class($ex) != "ErrorException") {
                    // En cas de problème avec un flux, marquer la source comme étant en panne
                    model("NewsFeedModel")->update_one($newsFeed["uuid"], ["access_ok" => 0]);
                }
            }
        }

        DatabaseConnection::instance()->commit();
    }
}
