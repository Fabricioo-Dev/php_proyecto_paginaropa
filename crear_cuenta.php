<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./CSS/base.css">
    <link rel="stylesheet" href="./CSS/auth.css">
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <title>Crear cuenta</title>
</head>

<body>
    <div class="header-inicio">
        <div class="logo"><a href="index.php" class="logo__link" aria-label="ELITE - Inicio"><span class="logo__texto">ELITE</span></a></div>
    </div>
    <div class="ingreso">
        <form action="Backend/crear_usuarioBD.php" method="post">
            <h1>Crear Cuenta</h1>

            <?php include './Backend/mensaje.php'; ?>

            <!-- Los maxlength coinciden con el largo de las columnas de la tabla usuario -->
            <label for="nombre">Nombre</label><br>
            <input type="text" name="nombre" id="nombre" minlength="2" maxlength="30" required><br>
            <label for="apellido">Apellido</label><br>
            <input type="text" name="apellido" id="apellido" minlength="2" maxlength="30" required><br>
            <label for="email">Correo Electrónico</label><br>
            <input type="email" name="email" id="email" maxlength="100" required><br>
            <label for="pass_input">Contraseña (mínimo 6 caracteres)</label><br>
            <div class="contenedor-password">
                <input type="password" name="contrasenia" id="pass_input" minlength="6" required>
                <i class="fa-solid fa-eye" id="ojo_icono"></i>
            </div><br>

            <input type="submit" value="Crear Cuenta" class="btn-iniciar-sesion">
        </form>
        <p class="link_login">¿Ya tenés cuenta? <a href="login.php">Iniciá sesión</a></p>
    </div>

    <script>
        // Muestra u oculta la contraseña cambiando el tipo del input
        document.getElementById('ojo_icono').addEventListener('click', function() {
            let pass = document.getElementById('pass_input');
            pass.type = pass.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>

</html>
