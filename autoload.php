<?php
/**
 * Majestic Start
 * Your go-to homepage with your RSS feeds, weather updates, bookmarks and search.
 *
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 * @license MIT
 * @version 5.0.0
 */

// Define constants
define("APPVERSION", "5.0.0.0");
define("WRITEPATH", realpath(__DIR__ . "/writable/"));

// Load application files
foreach (glob(__DIR__ . "/application/*/*.php") as $file) {
    require_once $file;
}

// Set exception and error handlers
set_exception_handler(function ($ex) {
    error_log("{$ex->getMessage()} in {$ex->getFile()}:{$ex->getLine()}");
    http_response_code(500);
    echo MajesticStart\View\TemplateEngine::error($ex->__toString());
});

set_error_handler(function (
    int $errno,
    string $errstr,
    ?string $errfile,
    ?int $errline,
) {
    throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
});

// Set session settings
session_start([
    "cookie_lifetime" => 604800,
    "cookie_secure" => true,
    "cookie_httponly" => true,
    "cookie_samesite" => "Lax",
]);

// Load config
if (is_file(__DIR__ . "/config.ini")) {
    MajesticStart\Config\Config::getInstance()->loadIni(
        __DIR__ . "/config.ini",
    );
}

/**
 * Returns the value of the specified configuration key.
 * @param string $key The configuration key.
 * @return mixed The value of the configuration key, or null if the key does not exist.
 */
function config(string $key): mixed
{
    $config = MajesticStart\Config\Config::getInstance();

    return isset($config->$key) ? $config->$key : null;
}

/**
 * Get a model instance
 * @template T
 * @param class-string<T> $name
 * @return T
 */
function model(string $name): MajesticStart\Database\DatabaseQuerier
{
    return MajesticStart\Database\DatabaseQuerier::model($name);
}

/**
 * Get the user agent string for the application.
 * @return string The user agent string.
 */
function user_agent(): string
{
    return sprintf(
        "curl/%s (MajesticStart/%s; +%s) Bot",
        curl_version()["version"],
        APPVERSION,
        config("rootUri"),
    );
}

/**
 * Convert a timestamp to a human-readable string representing the time elapsed since the timestamp.
 * @param int $timestamp The timestamp to convert.
 * @param bool $shorter Whether to use shorter strings (e.g. "1h" instead of "1 hour").
 * @return string The human-readable string representing the time elapsed since the timestamp.
 */
function to_ago_str(int $timestamp, bool $shorter = false): string
{
    $now = time();
    $diff = $now - $timestamp;

    if ($shorter) {
        if ($diff < 60) {
            return $diff . "s";
        }

        $minutes = floor($diff / 60);
        if ($minutes < 60) {
            return $minutes . "m";
        }

        $hours = floor($diff / 60 / 60);
        if ($hours < 24) {
            return $hours . "h";
        }

        $days = floor($diff / 60 / 60 / 24);
        if ($days < 30) {
            return $days . "j";
        }

        $months = floor($diff / 60 / 60 / 24 / 30);
        if ($months < 12) {
            return "$months mois";
        }

        $years = floor($diff / 60 / 60 / 24 / 365.25);
        return "$years an" . ($years > 1 ? "s" : "");
    } else {
        if ($diff < 60) {
            return "Il y a $diff seconde" . ($diff > 1 ? "s" : "");
        }

        $minutes = floor($diff / 60);
        if ($minutes < 60) {
            return "Il y a $minutes minute" . ($minutes > 1 ? "s" : "");
        }

        $hours = floor($diff / 60 / 60);
        if ($hours < 24) {
            return "Il y a $hours heure" . ($hours > 1 ? "s" : "");
        }

        $days = floor($diff / 60 / 60 / 24);
        if ($days < 30) {
            return "Il y a $days jour" . ($days > 1 ? "s" : "");
        }

        $months = floor($diff / 60 / 60 / 24 / 30);
        if ($months < 12) {
            return "Il y a $months mois";
        }

        $years = floor($diff / 60 / 60 / 24 / 365.25);
        return "Il y a $years année" . ($years > 1 ? "s" : "");
    }
}
