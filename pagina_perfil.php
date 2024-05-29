<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./CSS/pagina_perfil.css">
    <title>Perfil_usuario</title>
</head>

<body>
    <div class="header-inicio">
        <a href="index.php"><img src="./img/logo-marca.jpg" alt="Logo de la marca"></a>
    </div>
    <div class="mi_cuenta">
        <h1>Mi cuenta</h1>
        <h3>DATOS PERSONALES</h3>
        <hr>
        <?php session_start();
        echo "<p>{$_SESSION['nombre']} {$_SESSION['apellido']} </p>";
        echo "<p>{$_SESSION['correo']}</p>";
        ?>
        <button><a href="cerrar_sesion.php" class="cerrar_sesion">CERRAR SESIÓN</a></button>
    </div>
</body>


</html>