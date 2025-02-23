<!DOCTYPE html>
<html lang="es">
<?php session_start(); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./CSS/crear_cuenta.css">
    <title>Crear cuenta</title>
</head>

<body>
    <div class="header-inicio">
        <a href="index.php"><img src="./img/logo-marca.jpg" alt="Logo de la marca"></a>
    </div>
    <div class="ingreso">
        <form action="Backend/crear_usuarioBD.php" method="post">
            <h1>Crear Cuenta</h1>
            <label for="nombre">Nombre</label><br>
            <input type="text" name="nombre" required><br>
            <label for="apellido">Apellido</label><br>
            <input type="text" name="apellido" required><br>
            <label for="email">Correo Electrónico</label><br>
            <input type="text" name="email" required><br>
            <label for="contraseña">Contraseña</label><br>
            <input type="password" name="contrasenia" required><br>
            <input type="submit" value="Crear Cuenta" class="btn-iniciar-sesion">
        </form>
        <?php
        if (isset($_SESSION['error_crear_usuario'])) {
            echo "<p class='mensaje_error'>" . $_SESSION['error_crear_usuario'] . "</p>";
            unset($_SESSION['error_crear_usuario']);
        }
        ?>
    </div>
</body>

</html>