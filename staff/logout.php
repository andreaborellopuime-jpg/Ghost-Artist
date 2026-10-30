<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se usan cookies de sesión, borrarlas también
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión por completo
session_destroy();

// Redirigir de vuelta a la pantalla de login
header("Location: login.php");
exit();
?>