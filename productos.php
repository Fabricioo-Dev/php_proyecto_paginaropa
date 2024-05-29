<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>

    <!------ Font Awesome ------->
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-----CSS----->
    <link rel="stylesheet" href="./CSS/productos.css">
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="/index.php"><img src="./img/logo-marca-nuevo.jpg" alt="Logo de la marca"></a>
        </div>
        <nav>
            <ul>
                <li class="#Nosotros"><a href="nosotros.php">Nosotros</a></li>
                <li class="#Productos"><a href="productos.php">Prendas</a></li>
                <li class="carrito"><a href="carrito.php" class="carrito">Carrito <i class="fa-solid fa-cart-shopping"></i></a></li>
                <?php session_start();
                if (isset($_SESSION['tipo_de_usuario']) && $_SESSION['tipo_de_usuario'] == 1) { ?>
                    <li class="#administrador"><a href="./administrador/administrador.php">Administradores</a></li>
                <?php }; ?>
            </ul>
        </nav>
        <div class="ingreso">
            <?php
            if (isset($_SESSION['nombre'])) { ?>
                <a href="pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
            <?php } else { ?>
                <a href="login.php" class="registrar"><button>Ingresar <i class="fa-solid fa-user"></i></button></a>
            <?php }; ?>
        </div>
    </header>

    <main class="productos">
        <h1>Productos ELITE</h1>
        <div class="seccion_container_productos">
            <div class="seccion_container_productos_tarjeta">
                <img src="./img/muestra1.jpg" alt="Imagen del producto">
                <h4>Nombre del producto</h4>
                <p>Descripcion</p>
                <p>Talla</p>
                <p>Color</p>
                <p>Precio</p>
                <button type="submit">Agregar al carrito</button>
            </div>
        </div>
    </main>
</body>



</html>