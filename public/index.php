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

    if (count($categories[$category_key]["news"]) > 15) {
        array_splice($categories[$category_key]["news"], 15);
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<?= TemplateEngine::head() ?>
<style>
    #top {
        background-image: url('<?= $settings['photo_url'] ?>');
        background-size: cover;
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

    .news-block-root {
        display: flex;
        flex-direction: row;
        gap: 4px;
    }

    .news-block-root .carousel {
        flex: 1;
        max-height: 600px;
    }

    .news-block-root .carousel-item .carousel-image {
        min-height: 400px;
        max-height: 400px;
        background-size: cover;
        background-position: center;
    }

    .news-block-root .carousel-caption {
        background-color: rgba(0, 0, 0, .5);
        color: white !important;
    }

    .news-block-root .carousel-caption img {
        filter: brightness(0) invert(100);
    }

    .news-block-root .news-block-grid {
        flex: 1;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 4px;
    }

    .news-block-grid .card {
        width: calc(50% - 2px);
    }

    .news-block-grid .card .col-md-4 {
        border-bottom-left-radius: .25rem;
        border-top-left-radius: .25rem;
        background-position: center;
        background-size: cover;
        height: 100%;
    }

    .news-block-root .card-title {
        font-size: 16px;
    }

    .news-block-root .card-body .card-text img {
        height: 16px;
        vertical-align: middle;
    }

    .news-block-root .card-body .card-text small {
        vertical-align: middle;
    }

    body[data-bs-theme="dark"] .news-block-root .card-body .card-text img {
        filter: brightness(0) invert(100);
    }

    /* xxl */
    @media only screen and (max-width: 1400px) {

        .news-block-root .carousel-caption h5,
        .news-block-root .card-title,
        .news-block-root .card-text {
            font-size: 14px;
        }

        .news-block-root .card-body .card-text img {
            height: 14px;
        }
    }

    @media only screen and (max-width: 576px) {
        .news-block-root .carousel-item .carousel-image {
            min-height: 300px;
            max-height: 300px;
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
                        <img src="<?= $searchengine["icon"] ?>" alt="<?= $searchengine["name"] ?>" height="24">
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
                    <a class="btn btn-sm btn-light" target="_blank" rel="noopener noreferrer" href="<?= str_contains($topic["link_or_query"], "https://") ? $topic["link_or_query"] : ($searchengine["result_url"] . urlencode($topic["link_or_query"])) ?>">
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
                    <img src="<?= $bookmark["icon"] ?>" alt="<?= $bookmark["name"] ?>" height="55">
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
            <h2><?= $category["title_fr"] ?></h2>
            <?php if (empty($category["news"])) : ?>
                <div class="alert alert-info">
                    Aucun titre disponible pour le moment.
                </div>
            <?php else : ?>
                <div class="news-block-root">
                    <div id="newsCarousel<?= $category["uuid"] ?>" class="carousel slide" data-bs-ride="true">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#newsCarousel<?= $category["uuid"] ?>" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Actualité <?= $category["title_fr"] ?> 1"></button>
                            <?php for ($i = 1; $i < count($category["news"]) - 4; $i++) : ?>
                                <button type="button" data-bs-target="#newsCarousel<?= $category["uuid"] ?>" data-bs-slide-to="<?= $i ?>" aria-label="Actualité <?= $category["title_fr"] ?> <?= $i + 1 ?>"></button>
                            <?php endfor; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php for ($i = 0; $i < count($category["news"]) - 4; $i++) : ?>
                                <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                                    <!-- <img src="<?= $category["news"][$i]["image"] ?>" class="carousel-image d-block w-100 rounded" alt="Illustration"> -->
                                    <div class="carousel-image w-100 rounded" style="background-image: url(<?= $category["news"][$i]["image"] ?>)"></div>
                                    <a href="<?= $category["news"][$i]["link"] ?>" target="_blank" rel="noopener noreferrer">
                                        <div class="carousel-caption">
                                            <h5 class="px-2"><?= $category["news"][$i]["title"] ?></h5>
                                            <div class="d-flex flex-row align-items-center justify-content-center gap-3">
                                                <img alt="<?= $category["news"][$i]["source"]["name"] ?>" src="<?= $category["news"][$i]["source"]["logo"] ?>" height="16">
                                                <span><?= to_ago_str($category["news"][$i]["pubDate"]) ?></span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel<?= $category["uuid"] ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Précédent</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel<?= $category["uuid"] ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Suivant</span>
                        </button>
                    </div>
                    <div class="news-block-grid d-none d-xl-flex">
                        <?php for ($i = count($category["news"]) - 4; $i < count($category["news"]); $i++) : ?>
                            <div class="card" style="max-width: 540px;">
                                <div class="row g-0 align-items-center" style="height: 100%;">
                                    <div class="col-md-4" style="background-image: url('<?= $category["news"][$i]["image"] ?>');">
                                    </div>
                                    <div class="col-md-8">
                                        <a class="text-decoration-none text-body" href="<?= $category["news"][$i]["link"] ?>" target="_blank" rel="noopener noreferrer">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= $category["news"][$i]["title"] ?></h5>
                                                <p class="card-text">
                                                    <img alt="<?= $category["news"][$i]["source"]["name"] ?>" src="<?= $category["news"][$i]["source"]["logo"] ?>">
                                                    <small class="text-body-secondary ms-1">- <?= to_ago_str($category["news"][$i]["pubDate"]) ?></small>
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
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
</body>

</html>