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
            $template_html = str_replace("{{".$key."}}", $value, $template_html);
        }

        return $template_html;
    }

    public static function head($title = "Majestic Start")
    {
        return self::template("head", [
            "title" => $title
        ]);
    }

    public static function header($title = null)
    {
        if(!isset($title)) $title = datefmt_format(datefmt_create("fr-FR", IntlDateFormatter::FULL, IntlDateFormatter::NONE), new DateTime()); // date('l d F o');
        
        global $errors;
        $errors_dump = "<script>";
        foreach($errors as $error_str) {
            $errors_dump .= 'console.warn("'.$error_str.'");'.PHP_EOL;
        }
        $errors_dump .= "</script>";


        return self::template((SessionUtils::is_logged_in() ? "header_session" : "header"), [
            "title" => $title,
            "errors_dump" => $errors_dump,
            "user_name" => SessionUtils::is_logged_in() ? $_SESSION["user"]["name"] : ""
        ]);
    }

    public static function footer()
    {
        return self::template("footer", [
            "year" => date("Y")
        ]);
    }

    public static function error($errstr) {
        return self::template("error", [
            "errstr" => nl2br($errstr)
        ]);
    }
}
