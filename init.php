<?php
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params(604800);
session_start();

global $errors;
$errors = [];

// Defines Majestic Start's version number
defined('MAJESTIC_START_VERSION') || define('MAJESTIC_START_VERSION', '4.4.0.0');

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
 * @template T
 * @param class-string<T> $modelName
 * @return T
 */
function model(string $modelName): ?object
{
    require_once(__DIR__ . "/database/models/$modelName.class.php");
    $model = new $modelName();

    if ($model instanceof $modelName) {
        return $model;
    }

    return null;
}

function to_ago_str(int $timestamp, bool $shorter = false): string
{
    $now = time();
    $diff = $now - $timestamp;

    if ($shorter) {
        if ($diff < 60) return $diff . "s";

        $minutes = floor($diff / 60);
        if ($minutes < 60) return $minutes . "m";

        $hours = floor($diff / 60 / 60);
        if ($hours < 24) return $hours . "h";

        $days = floor($diff / 60 / 60 / 24);
        if ($days < 30) return $days . "j";

        $months = floor($diff / 60 / 60 / 24 / 30);
        if ($months < 12) return "$months mois";

        $years = floor($diff / 60 / 60 / 24 / 365.25);
        return "$years an" . ($years > 1 ? "s" : "");
    } else {
        if ($diff < 60) return "Il y a $diff seconde" . ($diff > 1 ? "s" : "");

        $minutes = floor($diff / 60);
        if ($minutes < 60) return "Il y a $minutes minute" . ($minutes > 1 ? "s" : "");

        $hours = floor($diff / 60 / 60);
        if ($hours < 24) return "Il y a $hours heure" . ($hours > 1 ? "s" : "");

        $days = floor($diff / 60 / 60 / 24);
        if ($days < 30) return "Il y a $days jour" . ($days > 1 ? "s" : "");

        $months = floor($diff / 60 / 60 / 24 / 30);
        if ($months < 12) return "Il y a $months mois";

        $years = floor($diff / 60 / 60 / 24 / 365.25);
        return "Il y a $years année" . ($years > 1 ? "s" : "");
    }
}
