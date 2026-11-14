
<?php

// Configurar la seguridad de las sesiones
ini_set("session.use_only_cookies", "1");
ini_set("session.use_strict_mode", "1");

session_start();

require_once "config/conexion.php";

// Si ya inició sesión, enviarlo al panel
if (isset($_SESSION["usuario_id"])) {
    header("Location: panel.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = strtolower(trim($_POST["correo"] ?? ""));
    $password = $_POST["password"] ?? "";

    // Comprobar campos obligatorios
    if ($correo === "" || $password === "") {

        $mensaje = "Debes completar todos los campos.";

    } elseif (
        strlen($correo) > 150 ||
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
    ) {

        $mensaje = "Ingresa un correo electrónico válido.";

    } else {

        // Buscar al usuario por su correo
        $consulta = $conexion->prepare(
            "SELECT id, nombre, correo, password
             FROM usuarios
             WHERE correo = ?"
        );

        $consulta->bind_param("s", $correo);
        $consulta->execute();

        $resultado = $consulta->get_result();

        if ($resultado->num_rows === 1) {

            $usuario = $resultado->fetch_assoc();

            // Verificar la contraseña
            if (password_verify($password, $usuario["password"])) {

                // Regenerar el identificador de sesión
                session_regenerate_id(true);

                // Guardar los datos del usuario
                $_SESSION["usuario_id"] = $usuario["id"];
                $_SESSION["usuario_nombre"] = $usuario["nombre"];
                $_SESSION["usuario_correo"] = $usuario["correo"];

                // Redirigir al panel privado
                $consulta->close();

                header("Location: panel.php");
                exit;

            } else {

                $mensaje = "Correo o contraseña incorrectos.";
            }

        } else {

            $mensaje = "Correo o contraseña incorrectos.";
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

    <title>Iniciar sesión - Agenda Personal</title>

</head>

<body>

    <h1>Iniciar sesión</h1>

    <p style="text-align: center;">
        Ingresa tus datos para acceder a tu agenda personal.
    </p>

    <?php if (!empty($mensaje)): ?>

        <p style="color: red; text-align: center;">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>

    <?php endif; ?>

    <form method="POST" action="login.php">

        <label for="correo">Correo electrónico:</label>
        <br>

        <input
            type="email"
            id="correo"
            name="correo"
            maxlength="150"
            required
        >

        <br><br>

        <label for="password">Contraseña:</label>
        <br>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <br><br>

        <div style="text-align: center;">
            <button type="submit">Ingresar</button>
        </div>

    </form>

    <p style="text-align: center;">
        ¿No tienes una cuenta?
        <a href="registro.php">Regístrate aquí</a>
    </p>

</body>
</html>
