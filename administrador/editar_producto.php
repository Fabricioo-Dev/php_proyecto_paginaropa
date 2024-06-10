<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!------ Font Awesome ------->
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <!-----CSS----->
    <link rel="stylesheet" href="./../CSS/administrador.css">
    <title>Administradores</title>
</head>

<body>
    <?php //session_start(); 
    ?>
    <header class="header">
        <div class="logo">
            <a href="/index.php"><img src="./../img/logo-marca-nuevo.jpg" alt="Logo de la marca"></a>
        </div>
        <nav>
            <ul>
                <li class="#Nosotros"><a href="./../nosotros.php">Nosotros</a></li>
                <li class="#Productos"><a href="./../productos.php">Prendas</a></li>
                <li class="carrito"><a href="./../carrito.php" class="carrito">Carrito <i class="fa-solid fa-cart-shopping"></i></a></li>
                <?php session_start();
                if (isset($_SESSION['tipo_de_usuario']) && $_SESSION['tipo_de_usuario'] == 1) { ?>
                    <li class="#administrador"><a href="./../administrador/administrador.php">Administradores</a></li>
                <?php }; ?>
            </ul>
        </nav>
        <main>
            
        </main>
</body>