<?php
require_once(__DIR__ . "/icon-scraper/Icon.php");
require_once(__DIR__ . "/icon-scraper/DataAccess.php");
require_once(__DIR__ . "/icon-scraper/Scraper.php");

class BookmarkUtils
{
    public static function iconFrom($url)
    {
        $scraper = new \Mpclarkson\IconScraper\Scraper();

        $icons = $scraper->get($url);

        if (count($icons) > 0) {
            $iconPath = $icons[0]->getHref();
            $width = 0;
            foreach ($icons as $icon) {
                if ($icon->getWidth() > $width) {
                    $iconPath = $icon->getHref();
                    $width = $icon->getWidth();
                }
            }
        } else {
            $iconPath = 'http://' . parse_url($url)['host'] . '/favicon.ico';
        }

        return $iconPath;
    }
}
