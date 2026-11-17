<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/base.css">
    <link rel="stylesheet" href="./CSS/auth.css">
    <title>Ingresá tu Cuenta</title>
</head>


<body>
    <div class="header-inicio">
        <div class="logo"><a href="./index.php" class="logo__link" aria-label="ELITE - Inicio"><span class="logo__texto">ELITE</span></a></div>
    </div>
    <div class="ingreso">
        <form action="./Backend/verificar_login.php" method="post">
            <h1>Ingresá tu Cuenta</h1>

            <?php include './Backend/mensaje.php'; ?>

            <label for="email">Correo Electrónico</label><br>
            <input type="email" name="email" id="email" placeholder="Email" maxlength="100" required><br>

            <label for="pass_login">Contraseña</label><br>
            <div class="contenedor-password">
                <input type="password" name="contrasenia" id="pass_login" placeholder="Contraseña" autocomplete="current-password" required>
                <i class="fa-solid fa-eye" id="ojo_login"></i>
            </div><br>

            <input type="submit" value="Iniciar Sesión" class="btn-iniciar-sesion">
        </form>

        <button class="btn-crear-cuenta"><a href="crear_cuenta.php">Crear Cuenta</a></button>
    </div>

    <script>
        // Muestra u oculta la contraseña cambiando el tipo del input
        document.getElementById('ojo_login').addEventListener('click', function() {
            let pass = document.getElementById('pass_login');
            pass.type = pass.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>
