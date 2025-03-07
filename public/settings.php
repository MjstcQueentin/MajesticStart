<?php
include(__DIR__ . "/../init.php");
if (!SessionUtils::is_logged_in()) {
    http_response_code(307);
    header('Location: /login.php');
    return;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    model("UserModel")->update_one($_SESSION["user_uuid"], [
        "set_searchengine" => $_POST["set_searchengine"],
        "set_newscategories" => json_encode(!empty($_POST["set_newscategories"]) ? $_POST["set_newscategories"] : [])
    ]);

    http_response_code(307);
    header('Location: /');
    return;
}

$user = model("UserModel")->select_one($_SESSION["user_uuid"]);
$user["set_newscategories"] =  !empty($user["set_newscategories"]) ? json_decode($user["set_newscategories"]) : [];
$user["bookmarks"] = model('BookmarkModel')->select(['user_id' => $_SESSION["user_uuid"]]);

$searchengines = model('SearchEngineModel')->select_all();
$newscategories = model('NewsCategoryModel')->select_all();
?>
<!DOCTYPE html>
<html lang="fr">
<?= TemplateEngine::head("Paramètres | Majestic Start") ?>

<body data-bs-theme="<?= $_COOKIE['bs-theme'] ?? 'light' ?>">
    <?= TemplateEngine::header("Paramètres") ?>
    <form class="d-flex flex-row" style="height: calc(100vh - 42px); max-height: calc(100vh - 42px);" action="settings.php" method="POST">
        <div class="col-4 h-100 overflow-auto border-end d-none d-md-block">
            <h3 class="my-4 mx-4">Paramètres</h3>
            <nav id="navbar-settings" class="m-4 flex-column align-items-stretch">
                <nav class="nav nav-pills flex-column">
                    <a class="nav-link" href="#account">Compte</a>
                    <a class="nav-link" href="#search">Recherche</a>
                    <a class="nav-link" href="#bookmarks">Marque-pages</a>
                    <a class="nav-link" href="#news">Catégories d'actualités</a>
                </nav>
            </nav>
            <div class="d-flex flex-row gap-1 mx-4">
                <a href="/" class="btn btn-secondary"><i class="bi bi-x-lg"></i> Annuler</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> Enregistrer</button>
            </div>
        </div>
        <div class="col h-100 overflow-auto" data-bs-spy="scroll" data-bs-target="#navbar-settings" data-bs-smooth-scroll="true" tabindex="0">
            <div class="p-4">
                <div id="account" class="mb-5">
                    <h4>Compte</h4>
                    <div class="d-flex flex-row gap-3 align-items-center">
                        <div>
                            <img src="<?= SessionUtils::profile_picture() ?>" alt="Photo de profil" height="75" width="75" class="rounded-circle">
                        </div>
                        <div>
                            <p class="m-0 fs-5"><?= $_SESSION["user"]["name"] ?></p>
                            <p class="mb-1"><?= $_SESSION["user"]["primary_email"] ?></p>
                            <a class="btn btn-sm btn-primary" href="<?= MAJESTICLOUD_USER_URI ?>">Gérer sur MajestiCloud <i class="bi bi-box-arrow-up-right"></i></a>
                        </div>
                    </div>
                </div>
                <div id="search" class="mb-5">
                    <h4>Recherche</h4>
                    <p>
                        Sélectionnez le moteur de recherche à afficher sur votre portail.<br>
                        Ce paramètre sera aussi utilisé lorsque vous cliquerez sur un sujet mis en lumière.
                    </p>
                    <?php foreach ($searchengines as $engine) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="set_searchengine" id="<?= $engine['uuid'] ?>-search-radio" value="<?= $engine['uuid'] ?>" <?php if ($user['set_searchengine'] == $engine['uuid']) echo 'checked' ?>>
                            <label class="form-check-label" for="<?= $engine['uuid'] ?>-search-radio" style="vertical-align: super;">
                                <img style="vertical-align: sub;" src="<?= $engine['icon'] ?>" alt="" height="16" width="16">
                                <span class="ms-1"><?= $engine['name'] ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div id="bookmarks" class="mb-5">
                    <h4>Marque-pages</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Adresse</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($user["bookmarks"])) : ?>
                                    <tr>
                                        <td><i class="bi bi-info-circle"></i></td>
                                        <td colspan="3">Aucun marque-page.</td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($user["bookmarks"] as $bookmark) : ?>
                                    <tr id="bookmark-<?= $bookmark['uuid'] ?>">
                                        <td><img src="<?= $bookmark['icon'] ?>" alt="" height="16" width="16"></td>
                                        <td><?= $bookmark['name'] ?></td>
                                        <td><?= $bookmark['url'] ?></td>
                                        <td><button type="button" class="btn btn-sm btn-danger" onclick="deleteBookmark('<?= $bookmark['uuid'] ?>')"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><i class="bi bi-plus-circle-dotted"></i></td>
                                    <td><input type="text" class="form-control form-control-sm w-100"></td>
                                    <td><input type="url" class="form-control form-control-sm w-100"></td>
                                    <td><button type="button" class="btn btn-sm btn-success" onclick="addBookmark()"><i class="bi bi-floppy"></i></button></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div id="news" class="mb-5">
                    <h4>Catégories d'actualités</h4>
                    <p>Choisissez quelles catégories d'actualités afficher sur votre portail.</p>
                    <?php foreach ($newscategories as $category) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="set_newscategories[]" id="<?= $category['uuid'] ?>-newscategory-check" value="<?= $category['uuid'] ?>" <?php if (in_array($category['uuid'], $user['set_newscategories'])) echo 'checked' ?>>
                            <label class="form-check-label" for="<?= $category['uuid'] ?>-newscategory-check">
                                <?= $category['title_fr'] ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex flex-row gap-1 d-md-none">
                    <a href="/" class="btn btn-secondary"><i class="bi bi-x-lg"></i> Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> Enregistrer</button>
                </div>
            </div>
            <?= TemplateEngine::footer() ?>
            <script src="/assets/scripts/settings-bookmarks.js"></script>
        </div>
    </form>
</body>

</html>