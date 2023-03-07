<?php

session_start();

// Ak je pouzivatel prihlaseny, ziskam data zo session, pracujem s DB etc...
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    $email = $_SESSION['email'];
    $id = $_SESSION['id'];
    $fullname = $_SESSION['fullname'];
    $name = $_SESSION['name'];
    $surname = $_SESSION['surname'];

} else {
    // Ak pouzivatel prihlaseny nie je, presmerujem ho na hl. stranku.
    header('Location: index.php');
}
?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OAuth2 cez Google - Zabezpecena stranka</title>

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
        <h1>Zabezpecena stranka</h1>
        <h2>Obsah tu je dostupny iba po prihlaseni.</h2>
    </hgroup>
</header>
<main>

    <h3>Vitaj <?php echo $fullname ?></h3>
    <p>Si prihlaseny pod emailom: <?php echo $email?></p>
    <p>Tvoj identifikator je: <?php echo $id?></p>
    <p>Meno: <?php echo $name?>, Priezvisko: <?php echo $surname?></p>

    <a role="button" class="secondary" href="logout.php">Odhlasenie</a></p>
    <a role="button" href="index.php">Spat na hlavnu stranku</a></p>


</main>
</body>
</html>
