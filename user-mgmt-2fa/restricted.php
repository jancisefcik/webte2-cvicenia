<?php

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php");
    exit;
}

// TODO: Poskytnut pouzivatelovi docasne deaktivovat 2FA.
// TODO: Poskytnut pouzivatelovi moznost resetovania hesla.

?>
<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login/register s 2FA - Zabezpecena stranka</title>

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
        <h1>Zabezpecena stranka</h1>
        <h2>Obsah tu je dostupny iba po prihlaseni.</h2>
    </hgroup>
</header>
<main>

    <h3>Vitaj <?php echo $_SESSION['fullname']; ?></h3>
    <p><strong>Si prihlaseny pod emailom:</strong> <?php echo $_SESSION['email']; ?></p>
    <p><strong>Tvoj identifikator (login) je:</strong> <?php echo $_SESSION['login']; ?></p>
    <p><strong>Datum registracie/vytvonia konta:</strong> <?php echo $_SESSION['created_at'] ?></p>

    <a href="logout.php">Odhlasenie</a></p><br>
    <a href="index.php">Spat na hlavnu stranku</a></p>

</main>
</body>
</html>