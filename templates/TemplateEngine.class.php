<?php

/**
 * Impression des templates
 */
class TemplateEngine
{
    private const TEMPLATEDIR = __DIR__ . "/html";

    private static function template($template_name, $template_params = [])
    {
        $template_html = file_get_contents(self::TEMPLATEDIR . "/" . $template_name . ".html");
        foreach ($template_params as $key => $value) {
            $template_html = str_replace("{{" . $key . "}}", $value, $template_html);
        }

        return $template_html;
    }

    public static function head($title = "Majestic Start", $css = [])
    {
        return self::template("head", [
            "title" => $title,
            "additional_tags" => implode(PHP_EOL, array_map(function ($item) {
                return '<link rel="stylesheet" href="' . htmlspecialchars($item) . '">';
            }, $css))
        ]);
    }

    public static function header(?string $title)
    {
        if (!isset($title)) $title = datefmt_format(datefmt_create("fr-FR", IntlDateFormatter::FULL, IntlDateFormatter::NONE), new DateTime()); // date('l d F o');

        global $errors;
        if (ENVIRONMENT == "prod") {
            $errors_dump = "<script>";
            foreach ($errors as $error_str) {
                $errors_dump .= 'console.warn("' . htmlspecialchars($error_str) . '");' . PHP_EOL;
            }
            $errors_dump .= "</script>";
        } else {
            $errors_dump = "";
        }

        try {
            $profilePicture = SessionUtils::profile_picture();
        } catch (Exception $e) {
            $profilePicture = '';
        }

        $account_menu = "";
        if (defined("MAJESTICLOUD_ENABLE") && MAJESTICLOUD_ENABLE == false) {
            $account_menu = '<div></div>';
        } else if (!defined("MAJESICLOUD_ENABLE") || MAJESTICLOUD_ENABLE == true) {
            $account_menu = SessionUtils::is_logged_in()
                ? self::template("header_session_on", [
                    "user_name" => $_SESSION["user"]["name"],
                    "user_email" => $_SESSION["user"]["primary_email"],
                    "user_photo" => $profilePicture
                ])
                : self::template("header_session_off");
        }

        return self::template("header", [
            "title" => $title,
            "errors_dump" => $errors_dump,
            "majesticloud_account_menu" => $account_menu
        ]);
    }

    public static function footer()
    {
        return self::template("footer", [
            "year" => date("Y")
        ]);
    }

    public static function error($errstr)
    {
        return self::template("error", [
            "errstr" => nl2br($errstr)
        ]);
    }
}
