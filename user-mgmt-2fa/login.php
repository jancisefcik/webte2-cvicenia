<?php

session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: restricted.php");
    exit;
}

require_once "../../config.global.php";
require_once 'PHPGangsta/GoogleAuthenticator.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // TODO: Skontrolovat ci login a password su zadane (podobne ako v register.php).

    $sql = "SELECT fullname, email, login, password, created_at, 2fa_code FROM users WHERE login = :login";

    $stmt = $pdo->prepare($sql);

    // TODO: Upravit SQL tak, aby mohol pouzivatel pri logine zadat login aj email.
    $stmt->bindParam(":login", $_POST["login"], PDO::PARAM_STR);

    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            // Uzivatel existuje, skontroluj heslo.
            $row = $stmt->fetch();
            $hashed_password = $row["password"];

            if (password_verify($_POST['password'], $hashed_password)) {
                // Heslo je spravne.
                $g2fa = new PHPGangsta_GoogleAuthenticator();
                if ($g2fa->verifyCode($row["2fa_code"], $_POST['2fa'], 2)) {
                    // Heslo aj kod su spravne, pouzivatel autentifikovany.

                    // Uloz data pouzivatela do session.
                    $_SESSION["loggedin"] = true;
                    $_SESSION["login"] = $row['login'];
                    $_SESSION["fullname"] = $row['fullname'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["created_at"] = $row['created_at'];

                    // Presmeruj pouzivatela na zabezpecenu stranku.
                    header("location: restricted.php");
                }
                else {
                    echo "Neplatny kod 2FA.";
                }
            } else {
                echo "Nespravne meno alebo heslo.";
            }
        } else {
            echo "Nespravne meno alebo heslo.";
        }
    } else {
        echo "Ups. Nieco sa pokazilo!";
    }

    unset($stmt);
    unset($pdo);
}

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login/register s 2FA - Login</title>

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
        <h1>Prihlasenie</h1>
        <h2>Prihlasenie pouzivatela po registracii</h2>
    </hgroup>
</header>
<main>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        <label for="login">
            Prihlasovacie meno:
            <input type="text" name="login" value="" id="login" required>
        </label>
        <br>
        <label for="password">
            Heslo:
            <input type="password" name="password" value="" id="password" required>
        </label>
        <br>
        <label for="2fa">
            2FA kod:
            <input type="number" name="2fa" value="" id="2fa" required>
        </label>

        <button type="submit">Prihlasit sa</button>
    </form>
    <p>Este nemate vytvorene konto? <a href="register.php">Registrujte sa tu.</a></p>
</main>
</body>
</html>