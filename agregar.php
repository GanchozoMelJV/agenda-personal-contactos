
<?php
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/conexion.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recibir los datos del formulario
    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");

    // Validar los campos obligatorios
    if ($nombre === "" || $telefono === "") {

        $mensaje = "El nombre y el teléfono son obligatorios.";

    } elseif (
        strlen($nombre) > 100 ||
        strlen($telefono) > 20 ||
        strlen($correo) > 150 ||
        strlen($ciudad) > 100
    ) {

        $mensaje = "Uno de los campos supera la longitud permitida.";

    } elseif (
        $correo !== "" &&
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
    ) {

        $mensaje = "Ingresa un correo electrónico válido.";

    } else {

        // Obtener el identificador del usuario autenticado
        $id_usuario = $_SESSION["usuario_id"];

        // Insertar el contacto asociado al usuario
        $consulta = $conexion->prepare(
            "INSERT INTO contactos
            (id_usuario, nombre, telefono, correo, ciudad)
            VALUES (?, ?, ?, ?, ?)"
        );

        $consulta->bind_param(
            "issss",
            $id_usuario,
            $nombre,
            $telefono,
            $correo,
            $ciudad
        );

        if ($consulta->execute()) {

            $consulta->close();

            header("Location: panel.php");
            exit;

        } else {

            $mensaje = "No se pudo registrar el contacto.";
            $consulta->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/estilos.css">

    <title>Agregar contacto - Agenda Personal</title>
</head>

<body>

    <h1>Agregar nuevo contacto</h1>

    <p style="text-align: center;">
        Completa los datos de la persona que deseas guardar.
    </p>

    <?php if (!empty($mensaje)): ?>
        <p style="color: red; text-align: center;">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="agregar.php">

        <label for="nombre">Nombre completo:</label>

        <input
            type="text"
            id="nombre"
            name="nombre"
            maxlength="100"
            required
        >

        <label for="telefono">Teléfono:</label>

        <input
            type="tel"
            id="telefono"
            name="telefono"
            maxlength="20"
            required
        >

        <label for="correo">Correo electrónico:</label>

        <input
            type="email"
            id="correo"
            name="correo"
            maxlength="150"
        >

        <label for="ciudad">Ciudad:</label>

        <input
            type="text"
            id="ciudad"
            name="ciudad"
            maxlength="100"
        >

        <br><br>

        <div style="text-align: center;">
            <button type="submit">Guardar contacto</button>
        </div>

    </form>

    <br>

    <p style="text-align: center;">
        <a href="panel.php">Volver al panel principal</a>
    </p>

</body>
</html>
