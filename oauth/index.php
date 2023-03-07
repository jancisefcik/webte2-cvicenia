<?php

session_start();

require_once 'vendor/autoload.php';
require_once '../../config.global.php';

// Inicializacia Google API klienta
$client = new Google\Client();

// Definica konfiguracneho JSON suboru pre autentifikaciu klienta.
// Subor sa stiahne z Google Cloud Console v zalozke Credentials.
$client->setAuthConfig('../../client_secret.json');

// Nastavenie URI, na ktoru Google server presmeruje poziadavku po uspesnej autentifikacii.
$redirect_uri = "https://site18.webte.fei.stuba.sk/oauth/redirect.php";
$client->setRedirectUri($redirect_uri);

// Definovanie Scopes - rozsah dat, ktore pozadujeme od pouzivatela z jeho Google uctu.
$client->addScope("email");
$client->addScope("profile");

// Vytvorenie URL pre autentifikaciu na Google server - odkaz na Google prihlasenie.
$auth_url = $client->createAuthUrl();

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OAuth2 cez Google</title>

    <style>
        html {
            max-width: 70ch;
            padding: 3em 1em;
            margin: auto;
            line-height: 1.75;
            font-size: 1.25em;
        }

        h1,h2,h3,h4,h5,h6 {
            margin: 3em 0 1em;
        }

        p,ul,ol {
            margin-bottom: 2em;
            color: #1d1d1d;
            font-family: sans-serif;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
</head>
<body>
    <header>
        <hgroup>
            <h1>OAuth2 cez Google</h1>
            <h2>Implementacia pomocou kniznice Google API for PHP</h2>
        </hgroup>
    </header>
    <main>

        <?php
        // Ak som prihlaseny, existuje session premenna.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            // Vypis relevantne info a uvitaciu spravu.
            echo '<h3>Vitaj ' . $_SESSION['name'] . '</h3>';
            echo '<p>Si prihlaseny ako: ' . $_SESSION['email'] . '</p>';
            echo '<p><a role="button" href="restricted.php">Zabezpecena stranka</a>';
            echo '<a role="button" class="secondary" href="logout.php">Odhlas ma</a></p>';

        } else {
            // Ak nie som prihlaseny, zobraz mi tlacidlo na prihlasenie.
            echo '<h3>Nie si prihlaseny</h3>';
            echo '<a role="button" href="' . filter_var($auth_url, FILTER_SANITIZE_URL) . '">Google prihlasenie</a>';
        }
        ?>


    </main>
</body>
</html>
