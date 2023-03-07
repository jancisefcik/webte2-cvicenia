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

// Ak bolo prihlasenie uspesne, Google server nam posle autorizacny kod v URI,
// ktory ziskame pomocou premennej $_GET['code']. Pri neuspesnom prihlaseni tento kod nie je odoslany.
if (isset($_GET['code'])) {
    // Na zaklade autentifikacneho kodu ziskame "access token".
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // Inicializacia triedy OAuth2, pomocou ktorej ziskame informacie pouzivatela na zaklade Scopes.
    $oauth = new Google\Service\Oauth2($client);
    $account_info = $oauth->userinfo->get();

    // Ziskanie dat pouzivatela z Google uctu. Tieto data sa nachadzaju aj v tokene po jeho desifrovani.
    $g_fullname = $account_info->name;
    $g_id = $account_info->id;
    $g_email = $account_info->email;
    $g_name = $account_info->givenName;
    $g_surname = $account_info->familyName;

    // Na tomto mieste je vhodne vytvorit poziadavku na vlastnu DB, ktora urobi:
    // 1. Ak existuje prihlasenie Google uctom -> ziskaj mi minule prihlasenia tohoto pouzivatela.
    // 2. Ak neexistuje prihlasenie pod tymto Google uctom -> vytvor novy zaznam v tabulke prihlaseni.

    // Ulozime potrebne data do session.
    $_SESSION['access_token'] = $token['access_token'];
    $_SESSION['email'] = $g_email;
    $_SESSION['id'] = $g_id;
    $_SESSION['fullname'] = $g_fullname;
    $_SESSION['name'] = $g_name;
    $_SESSION['surname'] = $g_surname;

}
// Presmerujem pouzivatela na hlavnu stranku alebo kam potrebujem
// aj v pripade, ze zabludil na redirect.php mimo prihlasenia.
header('Location: index.php');