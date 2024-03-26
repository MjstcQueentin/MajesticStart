<?php
include(__DIR__ . "/../init.php");

$db = new Database();
$settings = $db->select_settings();
$topics = $db->select_topics();
if (SessionUtils::is_logged_in()) {
    $user = $db->select_user($_SESSION["user"]["uuid"]);
    $user["set_newscategories"] =  !empty($user["set_newscategories"]) ? json_decode($user["set_newscategories"]) : [];

    $searchengine = $db->select_searchengines($user["set_searchengine"] ?? $settings["default_searchengine"]);
    $bookmarks = $db->select_bookmarks($_SESSION["user"]["uuid"]);
    $categories = $db->select_newscategories($user["set_newscategories"]);
} else {
    $searchengine = $db->select_searchengines($settings["default_searchengine"]);
    $bookmarks = $db->select_bookmarks();
    $categories = $db->select_newscategories();
}

foreach ($categories as $category_key => $category) {
    $categories[$category_key]["news"] = NewsAggregator::get_cache($category["uuid"]);

    if (count($categories[$category_key]["news"]) > 12) {
        array_splice($categories[$category_key]["news"], 12);
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<?= TemplateEngine::head() ?>
<style>
    #top {
        background-image: url('<?= to_image_url($settings['photo_url']) ?>');
        background-size: cover;
        background-position: center;
        min-height: calc(100vh - 42px);

        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;

        padding: 1rem;
    }

    .top-search-bar {
        width: 100%;
        max-width: 800px;
    }

    .bookmark-container {
        overflow-x: auto;
        display: flex;
        flex-direction: row;
        gap: 10px;
        padding: 6px 0;
    }

    a.bookmark {
        display: flex;
        align-items: center;
        justify-content: center;

        min-height: 100px;
        height: 100px;
        min-width: 100px;
        width: 100px;
        border-radius: 5px;
        outline: solid 0px #aaa;
        transition: outline 0.15s ease-out;
    }

    a.bookmark:first-child {
        margin-left: 3rem;
    }

    a.bookmark:last-child {
        margin-right: 3rem;
    }

    a.bookmark:hover {
        outline: solid 5px #aaa;
    }

    .weather-container {
        overflow-x: auto;
        display: flex;
        flex-direction: row;
        gap: 10px;
        padding: 6px 0;
    }

    .weather-block {
        min-width: 250px;
        min-height: 150px;
        border-radius: .25rem;
        background-image: linear-gradient(to top right, #e96d4a, #ff8250);
        background-position: center;
        background-size: cover;
        padding: 1rem;
        color: white;
    }

    .weather-block:first-child {
        margin-left: 3rem;
    }

    .weather-block:last-child {
        margin-right: 3rem;
    }

    .weather-block.skeleton {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .news-block-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
    }

    .news-block-title {
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .news-block-item {
        display: block;
        border-radius: 4px;
        color: inherit;
        text-decoration: none;
        outline: solid 0px #aaa;
        transition: outline 0.15s ease-out;
    }

    .news-block-item:hover {
        outline: solid 4px #aaa;
    }

    .news-block-item-image {
        height: 150px;
        border-radius: 4px;
        background-size: cover;
    }

    .news-block-item-caption {
        padding: .75rem;
    }

    .news-block-item-caption h6 {
        margin: 0;
    }

    .news-block-item-caption-source {
        display: flex;
        flex-direction: row;
        align-items: middle;
        justify-content: flex-start;
        gap: 4px;
    }

    .news-block-item-caption-source small {
        line-height: 1;
    }

    .news-block-item-caption-source img {
        height: 14px;
    }

    /* xxl */
    @media only screen and (max-width: 1400px) {
        .news-block-grid {
            grid-template-columns: repeat(5, 1fr);
        }
    }

    /* xl */
    @media only screen and (max-width: 1200px) {
        .news-block-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* lg */
    @media only screen and (max-width: 992px) {
        .news-block-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* lg */
    @media only screen and (max-width: 768px) {
        .news-block-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* */
    @media only screen and (max-width: 576px) {

        .me-5,
        .mx-5 {
            margin-right: .5rem !important;
        }

        .ms-5,
        .mx-5 {
            margin-left: .5rem !important;
        }

        a.bookmark:first-child {
            margin-left: .5rem;
        }

        a.bookmark:last-child {
            margin-right: .5rem;
        }

        .weather-block:first-child {
            margin-left: .5rem;
        }

        .weather-block:last-child {
            margin-right: .5rem;
        }

        .news-block-grid {
            grid-template-columns: repeat(1, 1fr);
        }
    }
</style>

<body>
    <?= TemplateEngine::header() ?>
    <div id="top" class="shadow">
        <div></div>
        <div class="top-search-bar">
            <form action="<?= $searchengine["result_url"] ?>" class="mb-1">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-body border-end-0" id="provider-logo">
                        <img src="<?= to_image_url($searchengine["icon"]) ?>" alt="<?= $searchengine["name"] ?>" height="24">
                    </span>
                    <input type="text" name="<?= $searchengine["query_param"] ?>" autofocus required class="form-control border-start-0 border-end-0" placeholder="Rechercher avec <?= $searchengine["name"] ?>" aria-label="Termes de recherches" aria-describedby="provider-logo">
                    <button class="btn btn-light border-top border-bottom border-end" type="submit" aria-label="Lancer la recherche"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <div class="d-flex flex-row flex-wrap gap-1">
                <a class="btn btn-sm btn-light" target="_blank" rel="noopener noreferrer" href="<?= $settings['photo_author_url'] ?>" title="Auteur de la photo">
                    <span class="visually-hidden">Auteur de la photo</span> <i class="bi bi-person"></i> <?= $settings['photo_author'] ?>
                </a>
                <a class="btn btn-sm btn-light" target="_blank" rel="noopener noreferrer" href="https://www.openstreetmap.org/search?query=<?= $settings['photo_place'] ?>" title="Emplacement de la photo">
                    <span class="visually-hidden">Emplacement de la photo</span> <i class="bi bi-geo-alt"></i> <?= $settings['photo_place'] ?>
                </a>
                <?php foreach ($topics as $topic) : ?>
                    <a class="btn btn-sm btn-light" target="_blank" rel="noopener noreferrer" href="<?= str_contains($topic["link_or_query"], "https://") ? $topic["link_or_query"] : ($searchengine["result_url"] . "?" . $searchengine["query_param"] . "=" . urlencode($topic["link_or_query"])) ?>">
                        <?php if ($topic["is_official"] == 1) : ?>
                            <i class="bi bi-patch-check-fill" title="Source officielle"></i><span class="visually-hidden">Source officielle</span>
                        <?php elseif ($topic["is_featured"] == 1) : ?>
                            <i class="bi bi-star-fill" title="Partenaire"></i><span class="visually-hidden">Partenaire</span>
                        <?php endif; ?>
                        <?= $topic["name"] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <a href="#bookmarks" class="d-block bg-body p-2 rounded-circle text-decoration-none text-center text-body" style="height:42px; width: 42px;" aria-label="Défiler vers le contenu" title="Défiler vers le contenu">
                <i class="d-block bi bi-chevron-down fs-5"></i>
            </a>
        </div>
    </div>
    <div id="bookmarks" class="my-4">
        <h2 class="ms-5">Marque-pages</h2>
        <div class="bookmark-container">
            <?php if (empty($bookmarks)) : ?>
                <div class="mx-5 alert alert-info">
                    <i class="bi bi-info-circle"></i> Vous n'avez aucun marque-page. Ajoutez-en depuis les paramètres.
                </div>
            <?php endif; ?>
            <?php foreach ($bookmarks as $bookmark) : ?>
                <a href="<?= $bookmark["url"] ?>" class="shadow-sm bookmark bg-body-secondary" title="<?= $bookmark["name"] ?>">
                    <img src="<?= to_image_url($bookmark["icon"]) ?>" alt="<?= $bookmark["name"] ?>" height="55">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="weather" class="my-4">
        <div class="ms-5 d-flex flex-row gap-4 align-items-baseline">
            <div class="d-flex flex-row gap-4 align-items-center">
                <h2 class="m-0">Météo</h2>
                <img id="weather-logo" src="/assets/logos/openweather_white_cropped.png" alt="OpenWeatherMap" height="28">
            </div>
            <p class="text-muted m-0 d-none"><i class="bi bi-geo-alt"></i> Ville</p>
        </div>
        <div class="weather-container">
            <div class="weather-block skeleton">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <div class="weather-block skeleton">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <div class="weather-block skeleton">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <div class="weather-block skeleton">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <div class="weather-block skeleton">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <div class="weather-block skeleton">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        </div>
    </div>
    <?php foreach ($categories as $category) : ?>
        <div class="mx-5 my-4 news-block">
            <div class="news-block-title">
                <h2><?= htmlspecialchars($category["title_fr"]) ?></h2>
                <a class="btn btn-lg btn-link" href="/feed.php?category=<?= htmlspecialchars($category["uuid"], ENT_COMPAT) ?>" title="Voir le fil complet">
                    <i class="bi bi-three-dots"></i>
                </a>
            </div>
            <?php if (empty($category["news"])) : ?>
                <div class="alert alert-info">
                    Aucun titre disponible pour le moment.
                </div>
            <?php else : ?>
                <div class="news-block-grid">
                    <?php foreach ($category["news"] as $newsPiece) : ?>
                        <a class="news-block-item bg-body-secondary" href="<?= $newsPiece["link"] ?>" target="_blank" rel="noopener noreferrer">
                            <div class="news-block-item-image" style="background-image: url(<?= to_image_url($newsPiece["image"]) ?>)"></div>
                            <div class="news-block-item-caption">
                                <div class="news-block-item-caption-source mb-2">
                                    <img alt="<?= $newsPiece["source"]["name"] ?>" src="<?= to_image_url($newsPiece["source"]["logo"]) ?>" <?php if ($newsPiece["source"]["logo_invertable"] == 1) echo 'class="invertable"' ?>>
                                    <small class="text-body-secondary ms-1" aria-label="<?= to_ago_str($newsPiece["pubDate"]) ?>" title="<?= to_ago_str($newsPiece["pubDate"]) ?>"><?= to_short_ago_str($newsPiece["pubDate"]) ?></small>
                                </div>
                                <h6> <?= htmlspecialchars($newsPiece["title"]) ?> </h6>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?= TemplateEngine::footer() ?>
    <script src="/assets/scripts/index-weather.js"></script>
    <script>
        function refreshColorMode() {
            var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            var topics = document.querySelectorAll("#top .top-search-bar .btn");
            var weatherLogo = document.querySelector("#weather-logo");

            if (mediaQuery.matches) {
                weatherLogo.setAttribute("src", "/assets/logos/openweather_white_cropped.png");
                topics.forEach((value, key, parent) => {
                    value.classList.remove("btn-light");
                    value.classList.add("btn-dark");
                });
            } else {
                weatherLogo.setAttribute("src", "/assets/logos/openweather_black_cropped.png");
                topics.forEach((value, key, parent) => {
                    value.classList.remove("btn-dark");
                    value.classList.add("btn-light");
                });
            }
        }

        document.addEventListener("DOMContentLoaded", refreshColorMode);
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener("change", refreshColorMode);
    </script>
    <script>
        console.debug(<?= json_encode($categories) ?>);
    </script>
</body>

</html>