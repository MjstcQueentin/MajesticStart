<?php

namespace MajesticStart\Libraries;

use Exception;
use RuntimeException;

/**
 * @link https://openweathermap.org/api
 * @version 2.5
 */
class OpenWeatherMap
{
    /**
     * Checl if OpenWeatherMap is ready to run on Majestic Start
     */
    public static function isConfigured(): bool
    {
        return !empty(config("openWeatherMapApiKey"));
    }

    /**
     * 5 day forecast is available at any location on the globe. It includes weather forecast data with 3-hour step.
     * @link https://openweathermap.org/forecast5
     *
     * @param string $lat Latitude
     * @param string $lon Longitude
     * @param string $lang Language
     * @return array
     */
    public static function getFiveDaysForecast($lat, $lon, $lang = "fr"): array
    {
        if (!$this->isConfigured()) {
            throw new Exception("OpenWeatherMap is not configured");
        }

        $lang = urlencode($lang);
        $lat = urlencode($lat);
        $lon = urlencode($lon);

        $ch = curl_init("https://api.openweathermap.org/data/2.5/forecast?lang=$lang&lat=$lat&lon=$lon&units=metric&appid=" . config("openWeatherMapApiKey"));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
        ]);

        $chreturn = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            throw new RuntimeException(curl_error($ch));
        }
        curl_close($ch);

        $forecast = json_decode($chreturn, true);

        return $forecast;
    }
}
