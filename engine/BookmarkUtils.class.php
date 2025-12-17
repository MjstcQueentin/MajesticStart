<?php

/**
 * Fonctions utiles pour la gestion des marque-pages.
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 */
class BookmarkUtils
{
    public static function iconFrom($url)
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $domain = parse_url($url, PHP_URL_HOST);
        $found_icons = [];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:115.0) Gecko/20100101 Firefox/120.0.1",
            CURLOPT_FAILONERROR => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        $html = curl_exec($ch);
        curl_close($ch);

        $page = new DOMDocument();
        @$page->loadHTML($html);
        $finder = new DOMXPath($page);

        // Tenter de trouver une apple-touch-icon
        $spaner = $finder->query('//link[@rel="apple-touch-icon"]');
        if (($spaner !== false && $spaner->count() > 0)) {
            for ($i = 0; $i < $spaner->count(); $i++) {
                $found_icons[] = self::toAbsolutePath($spaner->item($i)->getAttribute('href'), $scheme, $domain);
            }
        }

        // Tenter de lire le webmanifest
        $spaner = $finder->query('//link[@rel="manifest"]');
        if (($spaner !== false && $spaner->count() > 0)) {
            $manifest_link = self::toAbsolutePath($spaner->item(0)->getAttribute('href'), $scheme, $domain);
            $manifest = json_decode(file_get_contents($manifest_link), true);
            if (!empty($manifest["icons"])) {
                foreach ($manifest["icons"] as $icon) {
                    if (empty($icon["purpose"]) || $icon["purpose"] == "any") {
                        $found_icons[] = self::toAbsolutePath($icon["src"], $scheme, $domain);
                    }
                }
            }
        }

        // Tenter de lire la favicon fournie
        $spaner = $finder->query('//link[contains(@rel,"icon")]');
        if (($spaner !== false && $spaner->count() > 0)) {
            for ($i = 0; $i < $spaner->count(); $i++) {
                $found_icons[] = self::toAbsolutePath($spaner->item($i)->getAttribute('href'), $scheme, $domain);
            }
        }

        // Icône par défaut
        $found_icons[] = "$scheme://$domain/favicon.ico";
        $found_icons = array_unique($found_icons);

        // Trouver et retourner l'icône la plus grande possible
        $max_width = 0;
        $icon_index = 0;
        foreach ($found_icons as $index => $icon) {
            $iconsize = getimagesize($icon);
            if ($iconsize[0] > $max_width) {
                $max_width = $iconsize[0];
                $icon_index = $index;
            }
        }

        // Convertir l'icône en URL data:
        $icon_data = file_get_contents($found_icons[$icon_index]);
        if ($icon_data === false) {
            return $found_icons[$icon_index];
        } else {
            $mime_type = mime_content_type($found_icons[$icon_index]);
            return "data:" . $mime_type . ";base64," . base64_encode($icon_data);
        }
    }

    public static function toAbsolutePath($href, $scheme, $hostname, $path = "/")
    {
        $absolutePath = "";
        if (strpos($href, '://') === false) {
            $absolutePath .= "$scheme://$hostname";

            if (substr($href, 0, 1) != "/") {
                $absolutePath .= $path;
            }

            $absolutePath .= $href;
        } else {
            $absolutePath = $href;
        }

        return $absolutePath;
    }
}
