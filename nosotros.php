<?php
session_start();
include_once './Backend/contador_carrito.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/base.css">
    <link rel="stylesheet" href="./CSS/nosotros.css">
    <title>Nosotros</title>
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
            <?php
            if (isset($_SESSION['nombre'])) { ?>
                <a href="pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
            <?php } else { ?>
                <a href="login.php" class="registrar"><button>Ingresar <i class="fa-solid fa-user"></i></button></a>
            <?php }; ?>
        </div>
    </header>

    <main class="nosotros">

        <!-- Portada: logotipo grande y presentación del proyecto -->
        <section class="nosotros_portada">
            <div class="logo logo--grande">
                <span class="logo__link"><span class="logo__texto">ELITE</span></span>
            </div>
            <h1>Sobre Nosotros</h1>
            <p>Bienvenidos a Elite, una tienda online de ropa ficticia creada como proyecto para la materia de Bases de Datos 2 en la Licenciatura en Sistemas en la Universidad Champagnat de Mendoza. Elite ha sido diseñado para demostrar nuestras habilidades en el desarrollo web, combinando HTML, CSS, JavaScript, PHP y phpMyAdmin para ofrecer una experiencia de compra online completa y funcional.</p>
        </section>

        <!-- Datos del proyecto -->
        <section class="nosotros_datos" aria-label="Datos del proyecto">
            <div class="dato">
                <i class="fa-solid fa-calendar-days"></i>
                <span class="dato_titulo">Año</span>
                <span class="dato_valor">2026</span>
            </div>
            <div class="dato">
                <i class="fa-solid fa-book"></i>
                <span class="dato_titulo">Materia</span>
                <span class="dato_valor">Bases de Datos 2</span>
            </div>
            <div class="dato">
                <i class="fa-solid fa-graduation-cap"></i>
                <span class="dato_titulo">Carrera</span>
                <span class="dato_valor">Licenciatura en Sistemas</span>
            </div>
            <div class="dato">
                <i class="fa-solid fa-location-dot"></i>
                <span class="dato_titulo">Universidad</span>
                <span class="dato_valor">Champagnat, Mendoza</span>
            </div>
        </section>

        <!-- Creadores -->
        <section class="nosotros_bloque">
            <h2 class="bloque_titulo">Los creadores</h2>
            <div class="creadores">
                <article class="creador">
                    <span class="creador_inicial" aria-hidden="true">F</span>
                    <h3>Fabricio Funes</h3>
                    <p>Desarrollador</p>
                    <a href="https://www.linkedin.com/in/fabricio-funes-dev/" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin"></i> LinkedIn</a>
                </article>
                <article class="creador">
                    <span class="creador_inicial" aria-hidden="true">U</span>
                    <h3>Uriel Cabrera</h3>
                    <p>Desarrollador</p>
                    <a href="https://github.com/uricabrera" target="_blank" rel="noopener"><i class="fa-brands fa-github"></i> Github</a>
                </article>
            </div>
        </section>

        <!-- Tecnologías -->
        <section class="nosotros_bloque">
            <h2 class="bloque_titulo">Tecnologías</h2>
            <p class="bloque_texto">La tienda ha sido desarrollada utilizando las siguientes tecnologías:</p>
            <ul class="tecnologias">
                <li><img src="./img/logos_nosotros/html5_img.png" alt="HTML Logo"><span>HTML</span></li>
                <li><img src="./img/logos_nosotros/CSS3_logo_and_wordmark.svg.png" alt="CSS Logo"><span>CSS</span></li>
                <li><img src="./img/logos_nosotros/Unofficial_JavaScript_logo_2.svg.png" alt="JavaScript Logo"><span>JavaScript</span></li>
                <li><img src="./img/logos_nosotros/PHP-logo.svg.png" alt="PHP Logo"><span>PHP</span></li>
                <li><img src="./img/logos_nosotros/phpmyadmin-icon-6.png" alt="phpMyAdmin Logo"><span>phpMyAdmin</span></li>
            </ul>
        </section>

    </main>
</body>

</html>