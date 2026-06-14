<?php

namespace MajesticStart\View;

use DateTime;
use Exception;
use IntlDateFormatter;
use MajesticStart\Core\Session;

/**
 * Engine used to build HTML code sent out to the browser.
 */
class TemplateEngine
{
    /**
     * Loads a template file and replaces placeholders with the given parameters.
     *
     * @param string $template_name
     * @param array $template_params
     * @return string
     * @throws Exception if the template file is not found
     */
    private static function template($template_name, $template_params = []): string
    {
        $templatePath = __DIR__ . "/html/{$template_name}.html";
        if (!file_exists($templatePath)) {
            throw new Exception("Template {$template_name} not found");
        }

        $template_html = file_get_contents($templatePath);
        foreach ($template_params as $key => $value) {
            $template_html = str_replace("{{" . $key . "}}", $value, $template_html);
        }

        return $template_html;
    }

    /**
     * Generates the HTML head section with the given title and additional CSS files.
     *
     * @param string $title
     * @param array $css
     * @return string
     */
    public static function head($title = "Majestic Start", $css = [])
    {
        return self::template("head", [
            "title" => $title,
            "additional_tags" => implode(
                PHP_EOL,
                array_map(function ($item) {
                    return '<link rel="stylesheet" href="' . htmlspecialchars($item) . '">';
                }, $css),
            ),
        ]);
    }

    /**
     * Generates the HTML header section with the given title.
     *
     * @param string|null $title
     * @return string
     */
    public static function header(?string $title)
    {
        if (!isset($title)) {
            $title = datefmt_format(datefmt_create("fr-FR", IntlDateFormatter::FULL, IntlDateFormatter::NONE), new DateTime());
        }

        $account_menu = "";
        if (!config("majestiCloudEnabled")) {
            $account_menu = "<div></div>";
        } else {
            if (Session::isLoggedIn()) {
                try {
                    $profilePicture = Session::profilePicture();
                } catch (Exception $e) {
                    $profilePicture = "";
                }

                $account_menu = self::template("header_session_on", [
                    "user_name" => htmlspecialchars($_SESSION["user"]["name"]),
                    "user_email" => htmlspecialchars($_SESSION["user"]["primary_email"]),
                    "user_photo" => htmlspecialchars($profilePicture),
                ]);
            } else {
                $account_menu = self::template("header_session_off");
            }
        }

        return self::template("header", [
            "title" => $title,
            "majesticloud_account_menu" => $account_menu,
        ]);
    }

    /**
     * @return string
     */
    public static function footer()
    {
        return self::template("footer", [
            "year" => date("Y"),
        ]);
    }

    /**
     * Builds the error page
     *
     * @param string $errstr
     * @return string
     */
    public static function error($errstr)
    {
        return self::template("error", [
            "errstr" => nl2br($errstr),
        ]);
    }
}
