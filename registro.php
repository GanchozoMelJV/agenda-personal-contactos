
<?php
require_once "config/conexion.php";

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $correo = strtolower(trim($_POST["correo"] ?? ""));
    $password = $_POST["password"] ?? "";

    // Validar campos obligatorios
    if ($nombre === "" || $correo === "" || $password === "") {

        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = "error";

    // Validar longitud del nombre
    } elseif (strlen($nombre) > 100) {

        $mensaje = "El nombre no puede superar los 100 caracteres.";
        $tipo_mensaje = "error";

    // Validar formato y longitud del correo
    } elseif (
        strlen($correo) > 150 ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
    ) {

        $mensaje = "Ingresa un correo electrónico válido.";
        $tipo_mensaje = "error";

    // Validar longitud de la contraseña
    } elseif (strlen($password) < 8 || strlen($password) > 72) {

        $mensaje = "La contraseña debe tener entre 8 y 72 caracteres.";
        $tipo_mensaje = "error";

    } else {

        // Comprobar si el correo ya está registrado
        $consulta = $conexion->prepare(
            "SELECT id FROM usuarios WHERE correo = ?"
        );

        $consulta->bind_param("s", $correo);
        $consulta->execute();

        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "Este correo ya se encuentra registrado.";
            $tipo_mensaje = "error";

        } else {

            // Proteger la contraseña
            $password_segura = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            if ($password_segura === false) {

                $mensaje = "No se pudo procesar la contraseña.";
                $tipo_mensaje = "error";

            } else {

                // Registrar el nuevo usuario
                $insertar = $conexion->prepare(
                    "INSERT INTO usuarios (nombre, correo, password)
                     VALUES (?, ?, ?)"
                );

                $insertar->bind_param(
                    "sss",
                    $nombre,
                    $correo,
                    $password_segura
                );

                if ($insertar->execute()) {

                    $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión.";
                    $tipo_mensaje = "exito";

                } else {

                    $mensaje = "Ocurrió un error al registrar el usuario.";
                    $tipo_mensaje = "error";
                }

                $insertar->close();
            }
        }

        $consulta->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/estilos.css">

    <title>Registro - Agenda Personal</title>
</head>

<body>

    <div class="contenedor-registro">

        <h1>Crear una cuenta</h1>

        <p class="descripcion-registro">
            Regístrate para utilizar tu agenda personal de contactos.
        </p>

        <?php if (!empty($mensaje)): ?>

            <p class="<?php echo $tipo_mensaje === 'exito' ? 'mensaje-exito-registro' : 'mensaje-error-registro'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </p>

        <?php endif; ?>

        <form method="POST" action="registro.php">

            <label for="nombre">Nombre completo:</label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                required
            >

            <label for="correo">Correo electrónico:</label>

            <input
                type="email"
                id="correo"
                name="correo"
                maxlength="150"
                required
            >

            <label for="password">Contraseña:</label>

            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                maxlength="72"
                required
            >

            <div class="boton-registro">
                <button type="submit">Registrarme</button>
            </div>

        </form>

        <div class="enlace-login">
            ¿Ya tienes una cuenta?
            <a href="login.php">Inicia sesión aquí</a>
        </div>

    </div>

</body>
</html>
