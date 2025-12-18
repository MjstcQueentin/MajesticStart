<?php

/**
 * Agrégateur de nouvelles.
 * Fonctions utiles pour le chargement de flux RSS et l'agrégation de nouvelles depuis une ou plusieurs sources.
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 */
class NewsAggregator
{
    private static $writableDir = __DIR__ . "/../writable/";

    /**
     * Charge un flux RSS, le met en cache et retourne son contenu en SimpleXMLElement.
     * @param string $newsfeed_uuid UUID du flux
     * @param string $rss_link URI à partir de laquelle charger le flux
     * @throws RuntimeException When the source responds with an HTTP error and no cache is available
     * @return SimpleXMLElement
     */
    public static function load_rss($newsfeed_uuid, $rss_link)
    {
        $cache_link = self::$writableDir . "rsscache/" . $newsfeed_uuid . ".xml";

        $ch = curl_init($rss_link);
        curl_setopt_array($ch, [
            CURLOPT_FAILONERROR => true,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $xml = curl_exec($ch);

        // If the server responds with a 403 error, try again with the user agent
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 403) {
            curl_setopt($ch, CURLOPT_USERAGENT, sprintf("curl/%s (MajesticStart/%s; +%s) Bot", curl_version()['version'], MAJESTIC_START_VERSION, ENVIRONMENT_ROOT));
            $xml = curl_exec($ch);
        }

        if (curl_errno($ch) != 0) {
            // When the connection fails, use the cache when available
            if (is_file($cache_link)) {
                $xml = file_get_contents($cache_link);
            } else {
                throw new RuntimeException(curl_error($ch));
            }
        } elseif (!empty($xml)) {
            // Cache the RSS feed
            file_put_contents($cache_link, $xml);
        }

        curl_close($ch);

        $xmlObject = new SimpleXMLElement($xml, LIBXML_NOCDATA);
        $namespaces = $xmlObject->getNameSpaces(true);
        foreach ($namespaces as $namespace => $nsSource) {
            $xmlObject->registerXPathNamespace($namespace, $nsSource);
        }

        return $xmlObject;
    }

    /**
     * Transforme des items d'un flux RSS en arrays standardisés.
     * @param SimpleXMLElement|SimpleXMLElement[] $channel_items Tableau d'éléments <item>
     * @param array $newsfeed_uuid UUID du flux fournissant les items
     * @param int $max_items 
     * @return array
     */
    public static function transform($channel_items, $newsfeed_uuid, $max_items = 12)
    {
        libxml_use_internal_errors(true);
        $transformed = [];

        foreach ($channel_items as $item) {
            $image_src = "/assets/fallback-image.png";
            if (isset($item->enclosure)) {
                // Pièce jointe
                if (stripos($item->enclosure->attributes()->type, "image/") !== false) {
                    $image_src = $item->enclosure->attributes()->url;
                }
            } elseif (in_array("media", array_keys($item->getNamespaces(true))) && !empty($item->xpath("media:content"))) {
                // Média joint
                $image_src = $item->xpath("media:content")[0]->attributes()->url;
            } elseif (in_array("media", array_keys($item->getNamespaces(true))) && !empty($item->xpath("media:thumbnail"))) {
                // Média joint
                $image_src = $item->xpath("media:thumbnail")[0]->attributes()->url;
            } else {
                // Chercher sur la page de destination
                $ch = curl_init($item->link);
                curl_setopt_array($ch, [
                    CURLOPT_FAILONERROR => true,
                    CURLOPT_RETURNTRANSFER => true
                ]);
                $html = curl_exec($ch);

                if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 403) {
                    // On 403 errors, try again with the user agent
                    curl_setopt($ch, CURLOPT_USERAGENT, sprintf("curl/%s (MajesticStart/%s; +%s) Bot", curl_version()['version'], MAJESTIC_START_VERSION, ENVIRONMENT_ROOT));
                    $html = curl_exec($ch);
                }

                if (curl_errno($ch) == 0 && !empty($html) && $html !== false) {
                    $page = new DOMDocument();
                    $page->loadHTML($html);
                    $finder = new DOMXPath($page);

                    // Tenter avec OpenGraph
                    $image_src = $finder->evaluate('string(//meta[@property="og:image"]/@content)');

                    if ($image_src === false) {
                        // Tenter avec une image Wordpress
                        $image_src = $finder->evaluate('string(//img[contains(@class, "wp-post-image")]/@src)');
                    }

                    if ($image_src === false) {
                        // Image de secours
                        $image_src = "/assets/fallback-image.png";
                    } else {
                        // Convertir l'image en URL data:
                        $image_data = file_get_contents($image_src);
                        if ($image_data !== false) {
                            $image_mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $image_data);
                            $image_src = "data:" . $image_mime . ";base64," . base64_encode($image_data);
                        }
                    }
                }

                curl_close($ch);
            }

            $transformed[] = [
                "guid" => time(),
                "newsfeed_uuid" => $newsfeed_uuid,
                "title" => strval($item->title),
                "description" => strip_tags(strval($item->description)),
                "thumbnail_src" => strval($image_src),
                "publication_date" => date('Y-m-d H:i:s', strtotime(strval($item->pubDate))), // Sun, 26 Nov 2023 16:45:30 +0100
                "link" => strval($item->link)
            ];

            if (count($transformed) >= $max_items) break;
        }

        libxml_clear_errors();
        libxml_use_internal_errors(false);
        return $transformed;
    }
}
