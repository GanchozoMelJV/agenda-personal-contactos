
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

// Obtener el ID del contacto
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: panel.php");
    exit;
}

// Buscar el contacto y comprobar que pertenece al usuario
$consulta = $conexion->prepare(
    "SELECT id, nombre, telefono, correo, ciudad
     FROM contactos
     WHERE id = ? AND id_usuario = ?"
);

$consulta->bind_param("ii", $id, $id_usuario);
$consulta->execute();

$resultado = $consulta->get_result();
$contacto = $resultado->fetch_assoc();

$consulta->close();

// Si no existe o pertenece a otro usuario
if (!$contacto) {
    header("Location: panel.php");
    exit;
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");

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

        // Actualizar solamente el contacto del usuario autenticado
        $actualizar = $conexion->prepare(
            "UPDATE contactos
             SET nombre = ?, telefono = ?, correo = ?, ciudad = ?
             WHERE id = ? AND id_usuario = ?"
        );

        $actualizar->bind_param(
            "ssssii",
            $nombre,
            $telefono,
            $correo,
            $ciudad,
            $id,
            $id_usuario
        );

        if ($actualizar->execute()) {

            $actualizar->close();

            header("Location: panel.php");
            exit;

        } else {

            $mensaje = "No se pudieron actualizar los datos.";
            $actualizar->close();
        }
    }

    // Mantener los datos introducidos en el formulario
    $contacto["nombre"] = $nombre;
    $contacto["telefono"] = $telefono;
    $contacto["correo"] = $correo;
    $contacto["ciudad"] = $ciudad;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">

    <title>Editar contacto - Agenda Personal</title>
</head>

<body>

    <div class="contenedor-editar">

        <h1>Editar contacto</h1>

        <p class="descripcion-editar">
            Modifica la información del contacto seleccionado.
        </p>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje-error-editar">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>
        <?php endif; ?>

        <form method="POST"
              action="editar.php?id=<?php echo $id; ?>">

            <label for="nombre">Nombre completo:</label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                value="<?php echo htmlspecialchars($contacto["nombre"]); ?>"
                required
            >

            <label for="telefono">Teléfono:</label>

            <input
                type="tel"
                id="telefono"
                name="telefono"
                maxlength="20"
                value="<?php echo htmlspecialchars($contacto["telefono"]); ?>"
                required
            >

            <label for="correo">Correo electrónico:</label>

            <input
                type="email"
                id="correo"
                name="correo"
                maxlength="150"
                value="<?php echo htmlspecialchars($contacto["correo"] ?? ""); ?>"
            >

            <label for="ciudad">Ciudad:</label>

            <input
                type="text"
                id="ciudad"
                name="ciudad"
                maxlength="100"
                value="<?php echo htmlspecialchars($contacto["ciudad"] ?? ""); ?>"
            >

            <br><br>

            <div class="botones-editar">
                <button type="submit">Guardar cambios</button>
            </div>

        </form>

        <div class="volver-editar">
            <a href="panel.php">Volver al panel principal</a>
        </div>

    </div>

</body>
</html>
