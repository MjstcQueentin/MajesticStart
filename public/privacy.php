<?php
include(__DIR__ . "/../init.php");
?>
<!DOCTYPE html>
<html lang="fr">
<?= TemplateEngine::head("La confidentialité sur Majestic Start") ?>

<body data-bs-theme="<?= $_COOKIE['bs-theme'] ?? 'light' ?>">
    <?= TemplateEngine::header() ?>

    <div class="px-5 py-3 bg-body-secondary">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Start</a></li>
                <li class="breadcrumb-item active" aria-current="page">Confidentialité</li>
            </ol>
        </nav>
    </div>

    <section class="container my-5">
        <h2>La confidentialité sur Majestic Start</h2>
        <small>Dernière mise à jour le 8 mars 2024</small>
    </section>
    <section class="container my-5">
        <h3>Introduction</h3>
        <p>Majestic Start, comme l'ensemble des outils et services du projet Les Majesticiels, est produit par un développeur français indépendant, dans un but non lucratif.</p>
        <p>Le respect et la protection des données personnelles des usagers des Majesticiels est une priorité absolue, et à ce titre, tout sera mis en oeuvre pour garantir l'information, le respect du consentement et des droits des utilisateurs, selon les règles en vigueur dans l'Union Européenne.</p>
    </section>
    <section class="container my-5">
        <h3>Éditeur du service</h3>
        <p>
            Cette instance de Majestic Start est éditée par <b><?= htmlspecialchars(WEBMASTER_NAME) ?></b>.<br>
            <?= htmlspecialchars(WEBMASTER_LOCATION) ?><br>
            Courriel : <?= htmlspecialchars(WEBMASTER_EMAIL) ?><br>
            Téléphone : <?= htmlspecialchars(WEBMASTER_PHONE) ?>
        </p>
    </section>
    <section class="container my-5">
        <h3>Hébergeur du service</h3>
        <p>Cette instance de Majestic Start est hébergée par <b><?= htmlspecialchars(HOSTER_NAME) ?></b>.<br>
            <?= htmlspecialchars(HOSTER_LOCATION) ?><br>
            Courriel : <?= htmlspecialchars(HOSTER_EMAIL) ?><br>
            Téléphone : <?= htmlspecialchars(HOSTER_PHONE) ?>
        </p>
    </section>
    <section class="container my-5">
        <h3>Description des données collectées et des finalités justifiant la collecte</h3>
        <table>
            <thead>
                <tr>
                    <th scope="col">Donnée collectée</th>
                    <th scope="col">Descriptif</th>
                    <th scope="col">Finalité</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Identité et photo de profil</td>
                    <td>Il s'agit de votre nom ou de votre surnom, selon ce que vous avez configuré dans votre compte MajestiCloud.</td>
                    <td>Facultatif. Cette donnée est collectée si vous choisissez de créer un compte MajestiCloud et de l'utiliser dans MajestiCloud. Elle permet d'indiquer que votre expérience est personnalisée.</td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td>L'adresse de courriel principale que vous avez configuré dans votre compte MajestiCloud.</td>
                    <td>Facultatif. Cette donnée est collectée si vous choisissez de créer un compte MajestiCloud et de l'utiliser dans MajestiCloud. Elle permet d'envoyer des alertes de sécurité liées à votre compte.</td>
                </tr>
                <tr>
                    <td>Géolocalisation</td>
                    <td>Vos coordonnées géographiques (latitude et longitude), fournies par votre appareil/navigateur.</td>
                    <td>Facultatif. Cette donnée est collectée si vous activez le paramètre correspondant dans votre navigateur. La géolocalisation est collectée pour afficher les prévisions météorologiques.</td>
                </tr>
            </tbody>
        </table>
    </section>
    <section class="container my-5">
        <h3>Durée de vie des données collectées</h3>
        <table>
            <thead>
                <tr>
                    <th scope="col">Donnée collectée</th>
                    <th scope="col">Début de vie</th>
                    <th scope="col">Fin de vie</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Identité et photo de profil</td>
                    <td>À la création de votre compte MajestiCloud</td>
                    <td>À la suppression de votre compte MajestiCloud</td>
                </tr>
                <tr>
                    <td>Adresse mail</td>
                    <td>À la création de votre compte MajestiCloud</td>
                    <td>À la suppression de votre compte MajestiCloud</td>
                </tr>
                <tr>
                    <td>Géolocalisation</td>
                    <td>À l'affichage de la page d'accueil, à condition que vous activiez le paramètre correspondant dans votre navigateur</td>
                    <td>Aucun historique de cette donnée n'est conservé.</td>
                </tr>
            </tbody>
        </table>
    </section>
    <section class="container my-5">
        <h3>Fournisseurs externes</h3>
        <p>Lorsque vous consentez à sa collecte, votre géolocalisation est envoyée au fournisseur de prévisions météorologiques <b>OpenWeatherMap</b>, édité par la société OpenWeather Ltd, basée au Royaume-Uni.</p>
        <p>L'usage de cette donnée par OpenWeatherMap est soumise à sa propre politique de confidentialité, laquelle est consultable à l'adresse suivante : <a href="https://openweather.co.uk/privacy-policy">https://openweather.co.uk/privacy-policy</a></p>
    </section>
    <section class="container my-5">
        <h3>Demandes spécifiques et recours</h3>
        <p>En France, conformément à la loi Informatique et Libertés du 6 janvier 1978 modifiée en 2004, vous bénéficiez d’un droit d’accès et de rectification aux informations qui vous concernent. Vous pouvez l'exercer en adressant une demande à contact@quentinpugeat.fr.</p>
        <p>Si vous constatez une violation de vos droits concernant vos données à caractère personnel, vous avez le droit d'introduire une réclamation auprès de la Commission Nationale de l'Informatique et des Libertés (www.cnil.fr). Cependant, tout sera mis en oeuvre pour répondre à vos demandes et réclamations, ainsi merci de bien vouloir me contacter en premier lieu avant de saisir la CNIL.</p>
    </section>
    <?= TemplateEngine::footer() ?>
</body>

</html>