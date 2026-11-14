# Agenda Personal de Contactos

## Descripción del proyecto

Agenda personal de Contactos es una aplicación web desarrollada en PHP y MySQL que permite a los usuarios registrarse, iniciar sesión y administrar sus contactos de manera privada y segura, cada usuario puede gestionar exclusivamente sus propios contactos, evitando el acceso a información perteneciente a otras cuentas.

## Tecnologías utilizadas

- PHP.
- MySQL / MariaDB.
- HTML5.
- CSS3.
- XAMPP.
- phpMyAdmin.
- Visual Studio Code.

## Funcionalidades principales

- Registro de nuevos usuarios.
- Validación de correos electrónicos duplicados.
- Inicio y cierre de sesión.
- Autenticación mediante contraseña.
- Visualización de información del usuario.
- Registro de contactos.
- Edición de contactos.
- Eliminación de contactos con confirmación.
- Restricción de acceso a los contactos de otros usuarios.

## Seguridad implementada

La aplicación utiliza diferentes mecanismos de seguridad:

- `password_hash()` para proteger las contraseñas.
- `password_verify()` para comprobar las contraseñas durante el inicio de sesión.
- Sesiones PHP para controlar el acceso de los usuarios.
- Consultas preparadas para interactuar con la base de datos.
- Validaciones de formularios en el servidor.
- Verificación de propiedad de los contactos para impedir que un usuario modifique o elimine información de otra cuenta.
- Protección mediante token de seguridad en la eliminación de contactos.
- `htmlspecialchars()` para evitar la interpretación de contenido HTML no confiable.

## Requisitos para ejecutar el proyecto

- Tener instalado XAMPP.
- Tener Apache y MySQL activos.
- Contar con un navegador web.
- Tener PHP y MySQL disponibles mediante XAMPP.

## Instalación y ejecución

1. Instalar y abrir XAMPP.

2. Iniciar los servicios Apache y MySQL.

3. Copiar la carpeta `agenda_personal` dentro de `C:\xampp\htdocs`.

4. Abrir phpMyAdmin desde `http://localhost/phpmyadmin`.

5. Crear una base de datos llamada `agenda_personal`.

6. Seleccionar la base de datos e importar el archivo `agenda_personal.sql`.

7. Verificar la configuración de conexión a la base de datos en `config/conexion.php`.

8. Abrir el navegador e ingresar a:

   `http://localhost/agenda_personal/registro.php`

9. Registrar un usuario e iniciar sesión para comenzar a utilizar la aplicación.

## Base de datos

El proyecto utiliza una base de datos llamada `agenda_personal`, que contiene las siguientes tablas:

- **usuarios:** almacena la información de los usuarios registrados.
- **contactos:** almacena los contactos asociados a cada usuario mediante el campo `id_usuario`.

## Autora

Proyecto académico desarrollado como parte de la actividad de aprendizaje sobre metodologías de desarrollo web por la estudiante Melanie Johanna Ganchozo Velasquez.
