<?php
include(__DIR__ . "/../init.php");

$sources = model("NewsSourceModel")->select_all();
?>
<!DOCTYPE html>
<html lang="fr">
<?= TemplateEngine::head("Transparence de Majestic Start") ?>

<body data-bs-theme="<?= $_COOKIE['bs-theme'] ?? 'light' ?>">
    <?= TemplateEngine::header() ?>

    <div class="px-5 py-3 bg-body-secondary">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Start</a></li>
                <li class="breadcrumb-item active" aria-current="page">Transparence</li>
            </ol>
        </nav>
    </div>

    <section class="container my-5">
        <h2>La transparence sur Majestic Start</h2>
    </section>

    <section class="container my-5">
        <h3>Introduction</h3>
        <p>
            Majestic Start, comme l'ensemble des outils et services du projet Les Majesticiels, est produit par un développeur français indépendant, dans un but non lucratif.
            En tant que projet libre et à source ouverte, ce service est fourni sans contrepartie.
        </p>
        <p>
            Les logos et les noms des titres de presse apparaissant sur ce portail appartiennent à leurs propriétaires respectifs et peuvent être déposés.
        </p>
        <p>
            Les titres, descriptions et illustrations des articles présentés sur ce portail sont tirés depuis les flux RSS mis à disposition par leur émetteurs,
            et soumis aux règles relatives au droit d'auteur dans leurs pays d'origine.
        </p>
    </section>

    <section class="container my-5">
        <h3>Liste des sources</h3>
        <div class="accordion" id="sourcesAccordion">
            <?php foreach ($sources as $index => $source): ?>
                <?php
                $feeds = model('NewsFeedModel')->select(['newssource_id' => $source['id'], 'access_ok' => 1]);
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapseThree">
                            <img
                                src="<?= htmlspecialchars($source['logo_' . $_COOKIE['bs-theme'] ?? 'light']) ?>"
                                lightsrc="<?= htmlspecialchars($source['logo_light']) ?>"
                                darksrc="<?= htmlspecialchars($source['logo_dark']) ?>"
                                height="16">
                            <span class="ms-2">
                                <?= htmlspecialchars($source['name']) ?>
                            </span>
                            <span class="text-muted ms-2">
                                <?= htmlspecialchars($source['address']) ?>
                            </span>
                            <a class="ms-2" href="<?= htmlspecialchars($source['website']) ?>">
                                <?= htmlspecialchars($source['website']) ?>
                            </a>
                        </button>
                    </h2>
                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse" data-bs-parent="#sourcesAccordion">
                        <div class="accordion-body">
                            <ul>
                                <?php foreach ($feeds as $feed): ?>
                                    <li>
                                        <span><?= htmlspecialchars($feed['name']) ?></span>
                                        <a class="ms-2" href="<?= htmlspecialchars($feed['rss_feed_url']) ?>"><?= htmlspecialchars($feed['rss_feed_url']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </section>

    <?= TemplateEngine::footer() ?>
</body>

</html>