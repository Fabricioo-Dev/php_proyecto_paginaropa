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
        </div>
    </main>
</body>

<script>
    $(document).ready(function() {
        $.ajax({
            url: "./restful_api/productosajax.php",
            type: "get",
            dataType: "json",
            success: function(data) {

                let container_producto = document.getElementsByClassName("seccion_container_productos");
                let wrapper = document.createElement("div");
                wrapper.innerHTML = "";
                wrapper.classList.add("wrapper_container_productos");

                for (const prenda of data) {
                    let prendaHtml = `
                    <div class="seccion_container_productos_tarjeta">
                        <img src="./img/productos_img/${prenda['imagen']}" alt="Imagen del producto">
                        <h4>${prenda['nombre']}</h4>
                        <p>${prenda['descripcion']}</p>
                        <p>talle: ${prenda['talle']}</p>
                        <p>color: ${prenda['color']}</p>
                        <p>precio: ${prenda['precio']}</p>
                        <button type="submit" name="btnAgregar"><a href="">Agregar al carrito</a></button>
                        <?php
                            if($_SESSION['tipo_de_usuario'] == 1){
                                echo "<button type='submit' name='btnEditar'>";
                            }
                        ?>
                        ${prenda['isAdmin'] === 1 ? `<a href='./administrador/editar_producto.php?id_prenda=${prenda["id_prenda"]}'>Editar Producto</a>` : "" }
                        <?php
                            if($_SESSION['tipo_de_usuario'] == 1){
                                echo "</button>";
                            }
                        ?>

                        <?php
                            if($_SESSION['tipo_de_usuario'] == 1){
                            echo "<button type='submit' name='btnEliminar'>";
                            }
                        ?>

                        ${prenda['isAdmin'] === 1 ? `<a href='./administrador/eliminar_producto.php?prenda_id=${prenda["id_prenda"]}'>Eliminar Producto</a>` : "" }

                        <?php
                            if($_SESSION['tipo_de_usuario'] == 1){
                                echo "</button>";
                            }
                        ?>




                    </div>
                    `
                    //Tenemos que cerrar el php y poner la variable id_prenda con javaScript
                    wrapper.innerHTML += prendaHtml;

                }


                container_producto[0].appendChild(wrapper);



            }
        });
    });
</script>



</html>