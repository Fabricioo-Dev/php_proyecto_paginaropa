<?php session_start();
include './Backend/conexionBD.php';
/** @var mysqli $conexion */
include_once './Backend/contador_carrito.php';

// La página de perfil solo tiene sentido con un usuario logueado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/base.css">
    <link rel="stylesheet" href="./CSS/pagina_perfil.css">
    <title>Perfil_usuario</title>
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="./index.php" class="logo__link" aria-label="ELITE - Inicio"><span class="logo__texto">ELITE</span></a>
        </div>
        <nav>
            <ul>
                <li class="#Nosotros"><a href="nosotros.php">Nosotros</a></li>
                <li class="#Productos"><a href="productos.php">Prendas</a></li>
                <li class="carrito">
                    <a href="carrito.php" class="carrito">
                        Carrito <i class="fa-solid fa-cart-shopping"></i>
                        <span class="carrito-contador">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    </a>
                </li>
                <?php
                if (isset($_SESSION['tipo_de_usuario']) && $_SESSION['tipo_de_usuario'] == 1) { ?>
                    <li class="#administrador"><a href="./administrador/administrador.php">Administradores</a></li>
                <?php }; ?>
            </ul>
        </nav>
        <div class="ingreso">
            <a href="pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
        </div>
    </header>
    <main class="main">
        <div class="contenedor_cuenta">
            <div class="mi_cuenta">
                <h1>Mi cuenta</h1>
                <h3>DATOS PERSONALES</h3>
                <hr>
                <p><?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></p>
                <p><?php echo htmlspecialchars($_SESSION['correo']); ?></p>
                <button><a href="./Backend/cerrar_sesion.php" class="cerrar_sesion">CERRAR SESIÓN</a></button>
            </div>
        </div>
        <div class="contenedor_pedidos" id="mis-pedidos">
            <?php include './Backend/mostrar_pedidos.php'; ?>
        </div>
    </main>
</body>

</html>
