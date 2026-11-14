
<?php
session_start();

// Comprobar que el usuario haya iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/conexion.php";

// Obtener los contactos del usuario autenticado
$id_usuario = $_SESSION["usuario_id"];

$consulta = $conexion->prepare(
    "SELECT id, nombre, telefono, correo, ciudad
     FROM contactos
     WHERE id_usuario = ?
     ORDER BY nombre ASC"
);

$consulta->bind_param("i", $id_usuario);
$consulta->execute();

$resultado = $consulta->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">

    <title>Mi Agenda Personal</title>
</head>

<body class="pagina-panel">

    <div class="contenedor-panel">

        <!-- ENCABEZADO -->
        <div class="encabezado-panel">

            <h1>Mi Agenda Personal</h1>

            <p>
                Organiza y administra tus contactos fácilmente.
            </p>

        </div>

        <!-- INFORMACION DEL USUARIO -->
        <div class="tarjeta-usuario">

            <h2>¡Bienvenido/a,
                <?php echo htmlspecialchars($_SESSION["usuario_nombre"]); ?>!
            </h2>

            <p>
                <strong>Correo electrónico:</strong>
                <?php echo htmlspecialchars($_SESSION["usuario_correo"]); ?>
            </p>

        </div>

        <!-- SECCION DE CONTACTOS -->
        <div class="seccion-contactos">

            <div class="encabezado-contactos">

                <h2>Mis contactos</h2>

                <a href="agregar.php" class="boton-agregar">
                    + Agregar nuevo contacto
                </a>

            </div>

            <?php if ($resultado->num_rows > 0): ?>

                <div class="tabla-contenedor">

                    <table class="tabla-contactos">

                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Ciudad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($contacto = $resultado->fetch_assoc()): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($contacto["nombre"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($contacto["telefono"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($contacto["correo"] ?? ""); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($contacto["ciudad"] ?? ""); ?>
                                    </td>

                                    <td class="acciones">

                                        <a class="enlace-editar"
                                           href="editar.php?id=<?php echo $contacto["id"]; ?>">
                                            Editar
                                        </a>

                                        <a class="enlace-eliminar"
                                           href="eliminar.php?id=<?php echo $contacto["id"]; ?>">
                                            Eliminar
                                        </a>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="mensaje-vacio">

                    <h3>¡Tu agenda está lista!</h3>

                    <p>
                        Todavía no tienes contactos registrados.
                    </p>

                    <p>
                        Haz clic en «Agregar nuevo contacto» para comenzar.
                    </p>

                </div>

            <?php endif; ?>

        </div>

        <!-- CERRAR SESION -->
        <div class="pie-panel">

            <a href="logout.php" class="boton-cerrar">
                Cerrar sesión
            </a>

        </div>

    </div>

</body>
</html>

<?php
$consulta->close();
?>
