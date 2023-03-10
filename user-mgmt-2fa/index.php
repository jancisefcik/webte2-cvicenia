<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login/register s 2FA</title>

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
</head>
<body>
<header>
    <hgroup>
        <h1>Sprava pouzivatelov</h1>
        <h2>Registracia a prihlasovanie pouzivatela s 2FA</h2>
    </hgroup>
</header>
<main>

    <?php

    session_start();

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        // Neprihlaseny pouzivatel, zobraz odkaz na Login alebo Register stranku.
        echo '<p>Nie ste prihlaseny, prosim <a href="login.php">prihlaste sa</a> alebo sa <a href="register.php">zaregistrujte</a>.</p>';
    } else {
        // Prihlaseny pouzivatel, zobraz odkaz na zabezpecenu stranku.
        echo '<h3>Vitaj ' . $_SESSION['fullname'] . ' </h3>';
        echo '<a href="restricted.php">Zabezpecena stranka</a>';
    }

    ?>
</main>
</body>
</html>
