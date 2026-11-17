<?php session_start();
include_once './Backend/contador_carrito.php';
/** @var mysqli $conexion */

// Prendas destacadas: las 6 prendas activas cargadas más recientemente
// (el id AUTO_INCREMENT más alto es la última que se dio de alta).
$destacadas = mysqli_query($conexion, "SELECT p.id_prenda, p.nombre, p.precio, p.imagen, c.nombre AS categoria
                                       FROM prenda p
                                       LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
                                       WHERE p.estado = 'Activa'
                                       ORDER BY p.id_prenda DESC
                                       LIMIT 6")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Marca de remeras de hombre y mujer ELITE">
    <meta property="og:title" content="Elite">
    <meta property="og:description" content="ELITE: Elegancia y Distinción en Cada Fibra">
    <meta property="og:image" content="./img/muestra4.jpg">
    <meta name="author" content="Fabricio Funes Uriel Cabrera">
    <meta name="keywords" content="ropa, moda, tienda online, camisetas, remeras">
    <meta name="robots" content="index, follow">

    <title>Elite Remeras</title>
    <!------ Font Awesome ------->
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <!-----CSS----->
    <link rel="stylesheet" href="./CSS/base.css">
    <link rel="stylesheet" href="./CSS/index.css">
</head>

<body>
    <!----- HEADER ----->
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
            <?php if (isset($_SESSION['nombre'])) { ?>
                <a href="pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
            <?php } else { ?>
                <a href="login.php" class="registrar"><button>Ingresar <i class="fa-solid fa-user"></i></button></a>
            <?php }; ?>
        </div>
    </header>
    <!----- MAIN ----->
    <main>
        <div class="contenedor_mensaje">
            <?php include './Backend/mensaje.php'; ?>
        </div>

        <!-- Portada: presentación de la marca -->
        <section class="hero">
            <p class="hero_etiqueta">Proyecto de tienda de remeras por Fabricio, Uriel</p>
            <h1 class="hero_titulo">Elegancia y Distinción en Cada Fibra</h1>
            <p class="hero_texto">Descubre ELITE, la marca que redefinirá tu estilo con sus remeras de algodón de primera calidad. Con un
                enfoque en la elegancia y la distinción, nuestras prendas están diseñadas para aquellos que buscan lo mejor en
                moda y confort.</p>
            <p class="hero_texto">Fundada en la vibrante ciudad de Mendoza, ELITE es el resultado de la pasión y dedicación de los
                creadores Fabricio Funes y Uriel Cabrera. Cada remera refleja nuestra dedicación a la excelencia, combinando materiales
                premium, diseños innovadores y una atención meticulosa a los detalles.</p>
            <p class="hero_texto">Únete a la élite de la moda con ELITE y experimenta el lujo de vestir con estilo y confianza en cada
                ocasión.</p>
            <a class="hero_boton" href="productos.php">Ver prendas</a>
        </section>

        <!-- Nueva colección: se carga desde la base de datos. Cada tarjeta lleva al catálogo
             y el #prenda-ID hace que productos.php resalte esa prenda. -->
        <section class="remeras">
            <h2>Nueva Colección ELITE</h2>

            <?php if (count($destacadas) > 0) { ?>
                <div class="destacadas">
                    <?php foreach ($destacadas as $prenda) { ?>
                        <a class="destacada" href="productos.php#prenda-<?php echo $prenda['id_prenda']; ?>">
                            <img src="./img/productos_img/<?php echo htmlspecialchars($prenda['imagen']); ?>" alt="<?php echo htmlspecialchars($prenda['nombre']); ?>">
                            <span class="destacada_categoria"><?php echo htmlspecialchars($prenda['categoria'] ?? ''); ?></span>
                            <h3><?php echo htmlspecialchars($prenda['nombre']); ?></h3>
                            <p>$<?php echo number_format($prenda['precio'], 0, ',', '.'); ?></p>
                        </a>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="sin_prendas">Pronto vas a encontrar nuestras prendas acá.</p>
            <?php } ?>

            <div class="ver_todas">
                <a href="productos.php">Ver todas las prendas <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </section>
    </main>
    <!----- FOOTER ----->
    <footer>
        <div class="container-footer">
            <p>&copy; 2026 Proyecto de Tienda de Ropa - HTML, PHP y SQL |
                <a href="https://www.uch.edu.ar/">Universidad Champagnat</a>
            </p>
        </div>
    </footer>

</body>

</html>
