<?php
/**
 * Majestic Start - Hourly Feed Update
 * Helper file to launch the hourly-feed-update in environments not allowing command arguments
 *
 * Use: php hourly-feed-update.php
 *
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 * @license MIT
 * @version 5.0.0
 */

use MajesticStart\Cron\HourlyFeedUpdate;

require_once __DIR__ . "/../autoload.php";

// Require that this script is ran from a CLI, not a webserver
if (php_sapi_name() !== "cli") {
    die("This script must be ran from a CLI, not a webserver.");
}

// Run the task
// The arguments are passed to the task as an array
$task = new HourlyFeedUpdate();
$task->invoke();
