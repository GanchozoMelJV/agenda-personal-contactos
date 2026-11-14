
<?php

// Iniciar la sesión
session_start();

// Vaciar todos los datos de la sesión
$_SESSION = [];

// Obtener los parámetros de la cookie
if (ini_get("session.use_cookies")) {

    $parametros = session_get_cookie_params();

    // Eliminar la cookie de sesión
    setcookie(
        session_name(),
        "",
        [
            "expires" => time() - 42000,
            "path" => $parametros["path"],
            "domain" => $parametros["domain"],
            "secure" => $parametros["secure"],
            "httponly" => $parametros["httponly"],
            "samesite" => "Lax"
        ]
    );
}

// Destruir la sesión
session_destroy();

// Regresar al inicio de sesión
header("Location: login.php");
exit;

?>
