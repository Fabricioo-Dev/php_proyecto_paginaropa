<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!------ Font Awesome ------->
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <!-----CSS----->
    <link rel="stylesheet" href="./CSS/carrito.css">
    <title>Carrito de Productos</title>
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
            <?php if (isset($_SESSION['nombre'])) { ?>
                <a href="pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
            <?php } else { ?>
                <a href="login.php" class="registrar"><button>Ingresar <i class="fa-solid fa-user"></i></button></a>
            <?php }; ?>
        </div>
    </header>
    <main class="seccion_carrito">
        <h1>Bienvenido al carrito de productos</h1>
        <?php

        echo var_dump($_SESSION['carrito']);

        include './Backend/conexionBD.php';

        $stringIds = "";

        for($i=0;$i<count($_SESSION['carrito']);$i++){
            if($i == 0){
                $stringIds = $stringIds."(";
                $stringIds = $stringIds.$_SESSION['carrito'][$i]['id'].",";
            }
            if($i != 0 && ($i+1) != count($_SESSION['carrito']) ){
                $stringIds = $stringIds.$_SESSION['carrito'][$i]['id'].",";
            }

            if(($i+1) == count($_SESSION['carrito'])){
                $stringIds = $stringIds . $_SESSION['carrito'][$i]['id'];
                $stringIds = $stringIds.")";
            }
        }

        echo $stringIds;

        $consulta = "SELECT * FROM prenda WHERE id_prenda IN ".$stringIds.";";

        echo $consulta;


        $resultado_carrito = mysqli_query($conexion, $consulta);

        while ($i = $resultado_carrito->fetch_assoc()) {
            echo "<p value='{$i["id_prenda"]}'> {$i["nombre"]} </p>";
        }


        



        ?>
    </main>
</body>

</html>