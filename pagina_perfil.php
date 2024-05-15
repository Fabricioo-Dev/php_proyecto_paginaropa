<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<style>
    /* Estilos generales */
    body {
        margin: 0;
        padding: 0;
        background-color: #dbeaff;
        /* Fondo celeste claro */
        font-family: Arial, sans-serif;
        color: #76777c;
        /* Color blanco para el texto */
    }

    a {
        text-decoration: none;
    }

    /* Estilos para el header */
    .header-inicio {
        background-color: #0d1722;
        display: flex;
        justify-content: center;
        align-items: center;
        /* Centrar verticalmente */
        height: 60px;
    }

    .header-inicio img {
        height: 60px;
        /* Ajusta el tamaño del logo */
        width: auto;
    }

    .mi_cuenta {
        margin-left: 10px;
        width: 400px;
    }
    
    .cerrar_sesion {
        text-decoration: none;
        color: #0f1b36;
    }
</style>

</html>