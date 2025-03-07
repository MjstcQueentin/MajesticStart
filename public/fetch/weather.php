<?php
include(__DIR__ . "/../../init.php");

if (!OpenWeatherMap::isConfigured()) {
    http_response_code(500);
    exit;
}

if (!isset($_GET["lat"]) || !isset($_GET["lon"])) {
    http_response_code(400);
    exit;
}

$forecast = OpenWeatherMap::getFiveDaysForecast($_GET["lat"], $_GET["lon"]);
$return = [
    "city" => $forecast["city"]["name"],
    "country" => $forecast["city"]["country"],
    "forecast" => []
];

foreach ($forecast["list"] as $item) {
    $translated_item = [
        "day" => datefmt_format(datefmt_create("fr-FR", IntlDateFormatter::RELATIVE_MEDIUM, IntlDateFormatter::NONE), DateTime::createFromFormat('U', $item["dt"])), //DateTime::createFromFormat('U', $item["dt"])->format('l d'),
        "temp" => $item["main"]["temp"],
        "weather" => $item["weather"][0]["description"],
        "background" => "",
        "item_nb" => 1
    ];

    if (in_array($translated_item["day"], array_column($return["forecast"], "day"))) {
        $key = array_search($translated_item["day"], array_column($return["forecast"], "day"));
        $return["forecast"][$key]["temp"] += $translated_item["temp"];
        $return["forecast"][$key]["item_nb"] += 1;
        continue;
    }

    switch (substr($item['weather'][0]['icon'], 0, 2)) {
        case '01':
            $translated_item['background'] = '/assets/weather-backgrounds/sunny.jpg';
            break;
        case '02':
            $translated_item['background'] = '/assets/weather-backgrounds/cloudy.jpg';
            break;
        case '03':
            $translated_item['background'] = '/assets/weather-backgrounds/cloudy.jpg';
            break;
        case '04':
            $translated_item['background'] = '/assets/weather-backgrounds/grey.jpg';
            break;
        case '09':
            $translated_item['background'] = '/assets/weather-backgrounds/raining.jpg';
            break;
        case '10':
            $translated_item['background'] = '/assets/weather-backgrounds/raining.jpg';
            break;
        case '11':
            $translated_item['background'] = '/assets/weather-backgrounds/storm.jpg';
            break;
        case '13':
            $translated_item['background'] = '/assets/weather-backgrounds/snowing.jpg';
            break;
        case '50':
            $translated_item['background'] = '/assets/weather-backgrounds/grey.jpg';
            break;
    }

    $return["forecast"][] = $translated_item;
}

foreach ($return["forecast"] as $key => $forecast_item) {
    $return["forecast"][$key]["temp"] = intval($forecast_item["temp"] / $forecast_item["item_nb"]);
    unset($return["forecast"][$key]["item_nb"]);
}

header("Content-Type: application/json");
echo json_encode($return);
