<?php
/**
 * Majestic Start - Cron controller
 * Use this file with a CLI to run a CronTask.
 *
 * Use: php cron.php TaskName
 *
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 * @license MIT
 * @version 5.0.0
 */

require_once __DIR__ . "/autoload.php";

// Require that this script is ran from a CLI, not a webserver
if (php_sapi_name() !== "cli") {
    die("This script must be ran from a CLI, not a webserver.");
}

// A task name should be provided as the first argument
if (empty($argv) || empty($argv[1])) {
    die("No task name provided.");
}
$taskName = $argv[1];

// Check if the task exists. The task name should match a class name from the MajesticStart\Cron namespace
$taskClass = "MajesticStart\\Cron\\" . $taskName;
if (!class_exists($taskClass)) {
    die("Task not found.");
}

// Run the task
// The arguments are passed to the task as an array
$task = new $taskClass();
$task->invoke(count($argv) > 2 ? array_slice($argv, 2) : []);
