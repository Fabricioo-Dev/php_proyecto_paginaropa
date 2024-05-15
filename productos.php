<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    
    <!------ Font Awesome ------->
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
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
                <li class="#Nosotros"><a href="productos.php">Nosotros</a></li>
                <li class="#Productos"><a href="productos.php">Prendas</a></li>
                <li class="carrito"><a href="" class="carrito">Carrito <i class="fa-solid fa-cart-shopping"></i></a>
                </li>
            </ul>
        </nav>
        <div class="ingreso">
            <?php session_start();
            if (isset($_SESSION['nombre'])) { ?>
                <a href="pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
            <?php } else { ?>
                <a href="login.php" class="registrar"><button>Ingresar <i class="fa-solid fa-user"></i></button></a>
            <?php }; ?>
        </div>
    </header>

    <main class="productos">
        <h1>Productos ELITE</h1>
        <div class="seccion1">
            
        </div>
    </main>
</body>

</html>