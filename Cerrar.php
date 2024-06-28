<?php
session_start();
session_unset();
session_destroy();

// Eliminar las cookies si existen
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    setcookie('username', '', time() - 3600, "/");
    setcookie('password', '', time() - 3600, "/");
}

header("Location: index.php");
exit();
?>
