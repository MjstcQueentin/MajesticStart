<?php
include(__DIR__ . "/../init.php");

// If no category, redirect to the homepage
if (empty($_GET["category"])) {
    header("Location: /");
    http_response_code(307);
}

$db = new Database();
$settings = $db->select_settings();
$category = $db->select_newscategories($_GET["category"]);
$news = NewsAggregator::get_cache($_GET["category"]);

if (empty($category) || empty($news)) {
    header("Location: /");
    http_response_code(307);
}

$category = $category[0];

?>
<!DOCTYPE html>
<html lang="fr">
<?= TemplateEngine::head($category["title_fr"] . " | Majestic Start") ?>
<style>
    #top {
        background-image: url('<?= $settings['photo_url'] ?>');
        background-size: cover;
        background-position: center;

        width: 100%;
        height: 150px;
        margin-bottom: 1rem;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }

    #feed {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .news-article {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: stretch;
        color: inherit;
        text-decoration: none;
        border-radius: 5px;
    }

    .news-article-image {
        min-width: 250px;
        background-size: cover;
        background-position: center;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .news-article-text {
        padding: 1rem;
    }

    .news-article-metadata {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
    }

    .news-article-metadata img {
        height: 16px;
    }

    @media only screen and (max-width: 991px) {
        .news-article {
            flex-direction: column;
        }

        .news-article-image {
            min-height: 250px;
            border-top-left-radius: 5px;
            border-bottom-left-radius: 0;
            border-top-right-radius: 0;
        }
    }
</style>

<body data-bs-theme="<?= $_COOKIE['bs-theme'] ?? 'light' ?>">
    <?= TemplateEngine::header() ?>

    <div class="px-5 py-3 bg-body-secondary">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Start</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category["title_fr"]) ?></li>
            </ol>
        </nav>
    </div>


    <div id="top">
        <h2 class="bg-body py-2 px-3 rounded"><?= htmlspecialchars($category["title_fr"]) ?></h2>
    </div>

    <div id="feed" class="container mb-5">
        <?php foreach ($news as $article) : ?>
            <a class="news-article bg-body-tertiary" href="<?= htmlspecialchars($article["link"], ENT_COMPAT) ?>">
                <div class="news-article-image" style="background-image: url(<?= htmlspecialchars($article["image"], ENT_COMPAT) ?>)"></div>
                <div class="news-article-text">
                    <h3><?= htmlspecialchars($article["title"]) ?></h3>
                    <p><?= htmlspecialchars($article["title"]) ?></p>
                    <div class="news-article-metadata">
                        <img <?php if ($article["source"]["logo_invertable"] == 1) echo 'class="invertable"'; ?> src="<?= htmlspecialchars($article["source"]["logo"], ENT_COMPAT) ?>" alt="<?= htmlspecialchars($article["source"]["name"], ENT_COMPAT) ?>">
                        <small class="text-body-secondary" aria-label="<?= to_ago_str($article["pubDate"]) ?>" title="<?= to_ago_str($article["pubDate"]) ?>"><?= to_ago_str($article["pubDate"]) ?></small>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?= TemplateEngine::footer() ?>
</body>

</html>