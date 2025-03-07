<?php
include(__DIR__ . "/../init.php");

$settings = model('SettingModel')->select_all();
$topics = model('TopicModel')->select_all(["is_official" => "DESC", "is_featured" => "DESC"]);
$planned_event = model('PlannedEventModel')->select_today();
$search_engines = model('SearchEngineModel')->select_all();

if (SessionUtils::is_logged_in()) {
    $user = model('UserModel')->select_one($_SESSION["user"]["uuid"]);
    $user["set_newscategories"] =  !empty($user["set_newscategories"]) ? json_decode($user["set_newscategories"]) : [];

    $searchengine = model('SearchEngineModel')->select_one($user["set_searchengine"] ?? $_COOKIE['start-search-engine-id'] ?? $settings["default_searchengine"]);
    $bookmarks = model('BookmarkModel')->select(["user_id" => $_SESSION["user"]["uuid"]]);
    $categories = !empty($user["set_newscategories"])
        ? model('NewsCategoryModel')->select(["uuid" => $user["set_newscategories"]], ["display_order" => "ASC"])
        : model('NewsCategoryModel')->select_all(["display_order" => "ASC"]);
} else {
    $searchengine = model('SearchEngineModel')->select_one($_COOKIE['start-search-engine-id'] ?? $settings["default_searchengine"]);
    $bookmarks = model('BookmarkModel')->select(["user_id" => null]);
    $categories = model('NewsCategoryModel')->select_all(["display_order" => "ASC"]);
}
?>
<!DOCTYPE html>
<html lang="fr">
<?= TemplateEngine::head("Majestic Start", ['/assets/stylesheets/index.css']) ?>
<style>
    #top {
        background-image: url('<?= !empty($planned_event) ? $planned_event['picture_url'] : $settings['photo_url'] ?>');
    }
</style>

<body data-bs-theme="<?= $_COOKIE['bs-theme'] ?? 'light' ?>">
    <?= TemplateEngine::header() ?>
    <div id="top" class="shadow">
        <div></div>
        <div class="top-search-bar">
            <form id="search-bar" action="<?= $searchengine["result_url"] ?>" class="mb-1">
                <div class="input-group input-group-lg">
                    <button class="btn btn-lg btn-<?= $_COOKIE['bs-theme'] ?? 'light' ?> dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= $searchengine["icon"] ?>" alt="<?= $searchengine["name"] ?>" class="align-text-top" height="24" width="24">
                    </button>
                    <ul class="dropdown-menu shadow">
                        <li>
                            <h6 class="dropdown-header">Changer de moteur de recherche</h6>
                        </li>
                        <?php foreach ($search_engines as $item): ?>
                            <li>
                                <button type="button" class="dropdown-item" data-trigger="search-engine-change" data-search-id="<?= htmlspecialchars($item['uuid']) ?>">
                                    <img src="<?= $item["icon"] ?>" alt="" class="align-baseline" height="12">
                                    <?= htmlspecialchars($item['name']) ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <input type="text" name="<?= $searchengine["query_param"] ?>" autofocus required class="form-control border-start-0 border-end-0" placeholder="Rechercher avec <?= $searchengine["name"] ?>" aria-label="Termes de recherches">
                    <button class="btn btn-<?= $_COOKIE['bs-theme'] ?? 'light' ?> border-top border-bottom border-end" type="submit" aria-label="Lancer la recherche"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <script src="/assets/scripts/index-search.js"></script>

            <div class="d-flex flex-row flex-wrap gap-1">
                <?php if (!empty($planned_event)): ?>
                    <a class="btn btn-sm btn-info" target="_blank" rel="noopener noreferrer" href="<?= $planned_event['picture_author_url'] ?>" title="Auteur de la photo">
                        <span class="visually-hidden">Auteur de la photo</span> <i class="bi bi-person"></i> <?= $planned_event['picture_author'] ?>
                    </a>
                    <?php if (!empty($planned_event['picture_place'])): ?>
                        <a class="btn btn-sm btn-info" target="_blank" rel="noopener noreferrer" href="https://www.openstreetmap.org/search?query=<?= $planned_event['picture_place'] ?>" title="Emplacement de la photo">
                            <span class="visually-hidden">Emplacement de la photo</span> <i class="bi bi-geo-alt"></i> <?= $planned_event['picture_place'] ?>
                        </a>
                    <?php endif; ?>
                    <a class="btn btn-sm btn-info" target="_blank" rel="noopener noreferrer" target="_blank" rel="noopener noreferrer" href="<?= str_contains($planned_event['topic_link_or_query'], "https://") ? $planned_event['topic_link_or_query'] : ($searchengine["result_url"] . "?" . $searchengine["query_param"] . "=" . urlencode($planned_event['topic_link_or_query'])) ?>" title="Évènement spécial en cours">
                        <span class="visually-hidden">Évènement spécial</span> <i class="bi bi-calendar-heart"></i> <?= $planned_event['topic_name'] ?>
                    </a>
                <?php else: ?>
                    <a class="btn btn-sm btn-<?= $_COOKIE['bs-theme'] ?? 'light' ?>" target="_blank" rel="noopener noreferrer" href="<?= $settings['photo_author_url'] ?>" title="Auteur de la photo">
                        <span class="visually-hidden">Auteur de la photo</span> <i class="bi bi-person"></i> <?= $settings['photo_author'] ?>
                    </a>
                    <?php if (!empty($settings['photo_place'])): ?>
                        <a class="btn btn-sm btn-<?= $_COOKIE['bs-theme'] ?? 'light' ?>" target="_blank" rel="noopener noreferrer" href="https://www.openstreetmap.org/search?query=<?= $settings['photo_place'] ?>" title="Emplacement de la photo">
                            <span class="visually-hidden">Emplacement de la photo</span> <i class="bi bi-geo-alt"></i> <?= $settings['photo_place'] ?>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php foreach ($topics as $topic) : ?>
                    <a class="btn btn-sm btn-<?= $_COOKIE['bs-theme'] ?? 'light' ?>" target="_blank" rel="noopener noreferrer" href="<?= str_contains($topic["link_or_query"], "https://") ? $topic["link_or_query"] : ($searchengine["result_url"] . "?" . $searchengine["query_param"] . "=" . urlencode($topic["link_or_query"])) ?>">
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

    <?php if (OpenWeatherMap::isConfigured()) : ?>
        <div id="weather" class="my-4">
            <div class="ms-5 d-flex flex-row gap-4 align-items-baseline">
                <div class="d-flex flex-row gap-4 align-items-center">
                    <h2 class="m-0">Météo</h2>
                    <img id="weather-logo" src="/assets/logos/openweather_white_cropped.png" lightsrc="/assets/logos/openweather_black_cropped.png" darksrc="/assets/logos/openweather_white_cropped.png" alt="OpenWeatherMap" height="28">
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
        <script src="/assets/scripts/index-weather.js"></script>
    <?php endif; ?>

    <?php foreach ($categories as $category) : ?>
        <?php
        $category["news"] = model('NewsPostModel')->select_of_category($category["uuid"], 12);
        ?>
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
                    <?php foreach ($category["news"] as $newsPost) : ?>
                        <a class="news-block-item bg-body-secondary" href="<?= $newsPost["link"] ?>" target="_blank" rel="noopener noreferrer">
                            <div class="news-block-item-image" style="background-image: url(<?= $newsPost["thumbnail_src"] ?>)"></div>
                            <div class="news-block-item-caption">
                                <div class="news-block-item-caption-source mb-2">
                                    <img
                                        src="<?= htmlspecialchars($newsPost["newssource_logo_light"]) ?>"
                                        lightsrc="<?= htmlspecialchars($newsPost["newssource_logo_light"]) ?>"
                                        darksrc="<?= htmlspecialchars($newsPost["newssource_logo_dark"]) ?>"
                                        alt="<?= $newsPiece["newssource_name"] ?>">
                                    <small
                                        class="text-body-secondary ms-1"
                                        aria-label="<?= to_ago_str(strtotime($newsPost["publication_date"])) ?>"
                                        title="<?= to_ago_str(strtotime($newsPost["publication_date"])) ?>">
                                        <?= to_ago_str(strtotime($newsPost["publication_date"]), true) ?>
                                    </small>
                                </div>
                                <h6><?= htmlspecialchars($newsPost["title"]) ?></h6>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?= TemplateEngine::footer() ?>
    <script>
        function refreshColorMode() {
            var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            var topics = document.querySelectorAll("#top .top-search-bar .btn");

            if (mediaQuery.matches) {
                topics.forEach((value, key, parent) => {
                    if (value.classList.contains("btn-light")) {
                        value.classList.remove("btn-light");
                        value.classList.add("btn-dark");
                    }
                });
            } else {
                topics.forEach((value, key, parent) => {
                    if (value.classList.contains("btn-dark")) {
                        value.classList.remove("btn-dark");
                        value.classList.add("btn-light");
                    }
                });
            }
        }

        document.addEventListener("DOMContentLoaded", refreshColorMode);
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener("change", refreshColorMode);
    </script>
</body>

</html>