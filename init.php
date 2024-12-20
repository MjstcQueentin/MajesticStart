<?php
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params(604800);
session_start();

global $errors;
$errors = [];

// Defines Majestic Start's version number
defined('MAJESTIC_START_VERSION') || define('MAJESTIC_START_VERSION', '4.3.0.0');

require_once(__DIR__ . "/config/config.php");
require_once(__DIR__ . "/database/DatabaseQuerier.class.php");
require_once(__DIR__ . "/engine/BookmarkUtils.class.php");
require_once(__DIR__ . "/engine/Session.class.php");
require_once(__DIR__ . "/engine/NewsAggregator.class.php");
require_once(__DIR__ . "/engine/OpenWeatherMap.class.php");
require_once(__DIR__ . "/templates/TemplateEngine.class.php");

set_error_handler(function (int $errno, string $errstr, string $errfile = null, int $errline = null, array $errcontext = null) {
    global $errors;
    $errors[] = "$errstr in $errfile:$errline";
});

set_exception_handler(function ($ex) {
    http_response_code(500);
    echo TemplateEngine::error($ex->__toString());
});

/**
 * Get a model instance
 * @param class-string<DatabaseQuerier>|string $modelName
 * @return DatabaseQuerier|null
 * @phpstan-return ($modelName is class-string<DatabaseQuerier> ? ModelTemplate : object|null)
 */
function model(string $modelName)
{
    require_once(__DIR__ . "/database/models/$modelName.class.php");
    return new $modelName();
}

function to_ago_str($timestamp)
{
    $now = time();
    $diff = $now - $timestamp;
    if ($diff < 60) return "Il y a $diff seconde" . ($diff > 1 ? "s" : "");

    $minutes = round($diff / 60);
    if ($minutes < 60) return "Il y a $minutes minute" . ($minutes > 1 ? "s" : "");

    $hours = round($diff / 60 / 60);
    if ($hours < 24) return "Il y a $hours heure" . ($hours > 1 ? "s" : "");

    $days = round($diff / 60 / 60 / 24);
    if ($days < 30) return "Il y a $days jour" . ($days > 1 ? "s" : "");

    $months = round($diff / 60 / 60 / 24 / 12);
    return "Il y a $months mois";
}

function to_short_ago_str($timestamp)
{
    $now = time();
    $diff = $now - $timestamp;
    if ($diff < 60) return $diff . "s";

    $minutes = round($diff / 60);
    if ($minutes < 60) return $minutes . "m";

    $hours = round($diff / 60 / 60);
    if ($hours < 24) return $hours . "h";

    $days = round($diff / 60 / 60 / 24);
    if ($days < 30) return $days . "j";

    $months = round($diff / 60 / 60 / 24 / 12);
    return "$months mois";
}
