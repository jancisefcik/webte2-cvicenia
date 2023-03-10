<?php

session_start();

// Uvolnenie session premennych. Tieto dva prikazy su ekvivalentne.
$_SESSION = array();
session_unset();

// Vymazanie session.
session_destroy();

// Presmerovanie na hlavnu stranku.
header("location: index.php");
exit;