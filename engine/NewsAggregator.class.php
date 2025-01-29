<?php

/**
 * Agrégateur de nouvelles.
 * Fonctions utiles pour le chargement de flux RSS et l'agrégation de nouvelles depuis une ou plusieurs sources.
 * @author Quentin Pugeat <contact@quentinpugeat.fr>
 */
class NewsAggregator
{
    /**
     * Charge un flux RSS, le met en cache et retourne son contenu en SimpleXMLElement.
     * @param string $newsfeed_uuid UUID du flux
     * @param string $rss_link URI à partir de laquelle charger le flux
     * @throws RuntimeException When the source responds with an HTTP error and no cache is available
     * @return SimpleXMLElement
     */
    public static function load_rss($newsfeed_uuid, $rss_link)
    {
        $cache_link = __DIR__ . "/raw-xml-cache/" . $newsfeed_uuid . ".xml";
        // Write a new cache file if it was modified more than 15 minutes ago (or if it doesn't exist)
        $ch = curl_init($rss_link);
        curl_setopt_array($ch, [
            CURLOPT_USERAGENT => "MajesticStart/" . MAJESTIC_START_VERSION,
            CURLOPT_FAILONERROR => true,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $xml = curl_exec($ch);

        if (curl_errno($ch) != 0) {
            // When the connection fails, use the cache when available
            if (is_file($cache_link)) {
                $xml = file_get_contents($cache_link);
            } else {
                throw new RuntimeException(curl_error($ch));
            }
        }
        curl_close($ch);

        file_put_contents($cache_link, $xml);

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
                    CURLOPT_USERAGENT => "MajesticStart/" . MAJESTIC_START_VERSION,
                    CURLOPT_FAILONERROR => true,
                    CURLOPT_RETURNTRANSFER => true
                ]);
                $html = curl_exec($ch);

                if (!empty($html) && $html !== false) {
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
                    }
                }
            }

            $transformed[] = [
                "guid" => time(),
                "newsfeed_uuid" => $newsfeed_uuid,
                "title" => strval($item->title),
                "description" => strip_tags(strval($item->description)),
                "thumbnail_src" => strval($image_src),
                "publication_date" => DateTime::createFromFormat('D, d M Y H:i:s O', strval($item->pubDate))->format('Y-m-d H:i:s'), // Sun, 26 Nov 2023 16:45:30 +0100
                "link" => strval($item->link)
            ];

            if (count($transformed) >= $max_items) break;
        }

        return $transformed;
    }

    /**
     * Fonction d'aggrégation finale, trie les éléments dans l'ordre antichronologique.
     * Met l'aggrégation en cache.
     * @param string $category_uuid UUID de la catégorie
     * @param array $transformed_items Tableau d'éléments transformés
     * @return array
     * @deprecated On privilégiera l'usage de la table 'newspost' pour la mise en cache des articles
     */
    public static function aggregate($category_uuid, &$transformed_items)
    {
        $cache_link = __DIR__ . "/aggregator-cache/$category_uuid.sd";
        usort($transformed_items, function ($a, $b) {
            if (intval($a["pubDate"]) > intval($b["pubDate"])) return -1;
            elseif (intval($a["pubDate"]) < intval($b["pubDate"])) return 1;
            else return 0;
        });

        file_put_contents($cache_link, serialize($transformed_items));

        return $transformed_items;
    }

    /**
     * Vérifie si la catégorie est mise en cache depuis moins de 15 minutes.
     * @param string $category_uuid UUID de la catégorie
     * @return bool TRUE si la catégorie a été mise en cache depuis moins de 15 minutes, FALSE sinon.
     * @deprecated On privilégiera l'usage de la table 'newspost' pour la mise en cache des articles
     */
    public static function is_cached($category_uuid)
    {
        $cache_link = __DIR__ . "/aggregator-cache/$category_uuid.sd";
        if (!is_file($cache_link) || filemtime($cache_link) < (time() - (15 * 60))) {
            return false;
        }
        return true;
    }

    /**
     * Retourne les éléments transformés dernièrement mis en cache d'une catégorie.
     * Si la catégorie n'a pas de cache, la fonction retourne un tableau vide.
     * @param string $category_uuid UUID de la catégorie
     * @return array
     * @deprecated On privilégiera l'usage de la table 'newspost' pour la mise en cache des articles
     */
    public static function get_cache($category_uuid)
    {
        $cache_link = __DIR__ . "/aggregator-cache/$category_uuid.sd";
        if (is_file($cache_link)) {
            return unserialize(file_get_contents($cache_link)) ?? [];
        }

        return [];
    }
}
