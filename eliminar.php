
<?php
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/conexion.php";

$id_usuario = $_SESSION["usuario_id"];
$mensaje = "";

// Crear un token de seguridad
if (empty($_SESSION["token_eliminar"])) {
    $_SESSION["token_eliminar"] = bin2hex(random_bytes(32));
}

// Obtener el ID del contacto
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    $token = $_POST["token"] ?? "";

    // Comprobar el token de seguridad
    if (!hash_equals($_SESSION["token_eliminar"], $token)) {
        die("Solicitud no válida.");
    }

    if (!$id || $id <= 0) {
        header("Location: panel.php");
        exit;
    }

    // Eliminar solamente si pertenece al usuario autenticado
    $consulta = $conexion->prepare(
        "DELETE FROM contactos
         WHERE id = ? AND id_usuario = ?"
    );

    $consulta->bind_param("ii", $id, $id_usuario);
    $consulta->execute();

    if ($consulta->affected_rows > 0) {
        $consulta->close();

        unset($_SESSION["token_eliminar"]);

        header("Location: panel.php");
        exit;
    }

    $consulta->close();

    header("Location: panel.php");
    exit;
}

// Verificar que el contacto exista y pertenezca al usuario
if (!$id || $id <= 0) {
    header("Location: panel.php");
    exit;
}

$consulta = $conexion->prepare(
    "SELECT nombre FROM contactos
     WHERE id = ? AND id_usuario = ?"
);

$consulta->bind_param("ii", $id, $id_usuario);
$consulta->execute();

$resultado = $consulta->get_result();
$contacto = $resultado->fetch_assoc();

$consulta->close();

if (!$contacto) {
    header("Location: panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">

    <title>Eliminar contacto - Agenda Personal</title>
</head>

<body>

    <div class="contenedor-eliminar">

        <h1>Eliminar contacto</h1>

        <p>
            ¿Estás seguro de que deseas eliminar este contacto?
        </p>

        <h3 class="nombre-eliminar">
            <?php echo htmlspecialchars($contacto["nombre"]); ?>
        </h3>

        <p class="advertencia-eliminar">
            Esta acción no se puede deshacer.
        </p>

        <form method="POST" action="eliminar.php">

            <input
                type="hidden"
                name="id"
                value="<?php echo $id; ?>"
            >

            <input
                type="hidden"
                name="token"
                value="<?php echo htmlspecialchars($_SESSION["token_eliminar"]); ?>"
            >

            <div class="botones-eliminar">
                <button type="submit">
                    Sí, eliminar contacto
                </button>
            </div>

        </form>

        <div class="volver-eliminar">
            <a href="panel.php">Cancelar y volver al panel</a>
        </div>

    </div>

</body>
</html>
