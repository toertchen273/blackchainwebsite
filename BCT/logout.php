<?php
session_start();

// Alle Session-Variablen löschen
$_SESSION = array();

// Wenn die Sitzung mit einem Cookie begonnen wurde, löschen wir das Cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session zerstören
session_destroy();

// Umleitung zur Login-Seite
header("Location: login.php");
exit();
?>
