 
<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "agenda_personal";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Comprobar si existe algún error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar los caracteres
$conexion->set_charset("utf8mb4");
?>
