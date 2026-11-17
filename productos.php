<?php session_start();
include_once './Backend/contador_carrito.php';

$esta_logueado = isset($_SESSION['id_usuario']);
$es_admin = isset($_SESSION['tipo_de_usuario']) && $_SESSION['tipo_de_usuario'] == 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/base.css">
    <link rel="stylesheet" href="./CSS/productos.css">
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
                <li class="carrito"><a href="carrito.php" class="carrito">Carrito <i class="fa-solid fa-cart-shopping"></i>
                        <span class="carrito-contador">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    </a>
                </li>
                <?php if ($es_admin) { ?>
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

    <main class="productos__main">
        <h1>Productos ELITE</h1>

        <div class="filtros_productos">
            <label for="filtro-categoria">Categoría:</label>
            <select id="filtro-categoria">
                <option value="">Todas</option>
            </select>

            <label for="filtro-talle">Talle:</label>
            <select id="filtro-talle">
                <option value="">Todos</option>
            </select>

            <label for="filtro-nombre">Buscar:</label>
            <input type="text" id="filtro-nombre" placeholder="Nombre de la prenda...">
        </div>

        <nav class="categorias_nav" id="categorias_nav"></nav>

        <div class="seccion_container_productos">
        </div>
    </main>

    <!-- Contenedor de las notificaciones ("Producto agregado al carrito", etc.) -->
    <div id="notificacion" class="notificacion"></div>
</body>

<script>
    const TALLES_ORDEN = ['XS', 'S', 'M', 'L', 'XL'];

    // Estos datos los decide el servidor según la sesión (no se pueden falsear desde el navegador)
    const ESTA_LOGUEADO = <?php echo $esta_logueado ? 'true' : 'false'; ?>;
    const ES_ADMIN = <?php echo $es_admin ? 'true' : 'false'; ?>;

    let productosData = [];

    // Al cargar la página pedimos las prendas a la API por AJAX.
    // La página no se recarga: los datos llegan en JSON y las tarjetas se arman con JavaScript.
    $(document).ready(function() {
        $.ajax({
            url: "./restful_api/productosajax.php",
            type: "get",
            dataType: "json",
            success: function(data) {
                productosData = data;
                cargarOpcionesDeFiltro(data);

                // Si venimos desde el inicio con ?categoria=ID, dejamos ese filtro elegido
                let categoriaUrl = new URLSearchParams(window.location.search).get('categoria');
                if (categoriaUrl) {
                    $('#filtro-categoria').val(categoriaUrl);
                }
                aplicarFiltros();
                resaltarPrendaElegida();
            },
            error: function() {
                mostrarNotificacion('No se pudieron cargar las prendas.', 'error');
            }
        });

        $('#filtro-categoria, #filtro-talle').on('change', aplicarFiltros);
        $('#filtro-nombre').on('input', aplicarFiltros);
    });

    // Llena los select de categoría y talle con los valores que existen en las prendas
    function cargarOpcionesDeFiltro(data) {
        let categorias = new Map();
        let talles = new Set();

        data.forEach(prenda => {
            if (prenda.id_categoria && prenda.categoria_nombre) {
                categorias.set(prenda.id_categoria, prenda.categoria_nombre);
            }
            prenda.talles.forEach(t => talles.add(t.talle));
        });

        categorias.forEach((nombre, id) => {
            $('#filtro-categoria').append(`<option value="${id}">${escaparHtml(nombre)}</option>`);
        });

        [...talles].sort((a, b) => TALLES_ORDEN.indexOf(a) - TALLES_ORDEN.indexOf(b)).forEach(talle => {
            $('#filtro-talle').append(`<option value="${talle}">${talle}</option>`);
        });
    }

    // Filtra las prendas ya descargadas según categoría, talle y texto buscado.
    // Una prenda se muestra solo si cumple los tres filtros a la vez.
    function aplicarFiltros() {
        let categoriaElegida = $('#filtro-categoria').val();
        let talleElegido = $('#filtro-talle').val();
        let textoBuscado = $('#filtro-nombre').val().trim().toLowerCase();

        let filtrado = productosData.filter(prenda => {
            let pasaCategoria = !categoriaElegida || String(prenda.id_categoria) === categoriaElegida;
            let pasaTalle = !talleElegido || prenda.talles.some(t => t.talle === talleElegido);
            let pasaTexto = !textoBuscado || prenda.nombre.toLowerCase().includes(textoBuscado);
            return pasaCategoria && pasaTalle && pasaTexto;
        });

        renderizarProductos(filtrado);
    }

    // Opciones de cantidad (1 hasta el stock del talle elegido)
    function opcionesCantidad(stock) {
        let opciones = '';
        for (let i = 1; i <= stock; i++) {
            opciones += `<option value="${i}">${i}</option>`;
        }
        return opciones;
    }

    // Arma el HTML de la tarjeta de una prenda
    function tarjetaHtml(prenda) {
        let id = prenda.id_prenda;
        let zonaCompra = '';

        if (prenda.talles.length === 0) {
            zonaCompra = "<p class='sin-stock'>No hay stock</p>";
        } else if (!ESTA_LOGUEADO) {
            zonaCompra = `<a href="login.php" class="btn btn-primary">Ingresá para comprar</a>`;
        } else {
            // Solo se listan los talles con stock; data-stock guarda las unidades de cada uno
            let opcionesTalle = prenda.talles.map(t =>
                `<option value="${t.talle}" data-stock="${t.cantidad}">${t.talle}</option>`).join('');

            zonaCompra = `
                <div class="form-group">
                    <label for="talle-${id}">Talle:</label>
                    <select id="talle-${id}" class="custom-select" onchange="actualizarCantidad(${id})">${opcionesTalle}</select>
                </div>
                <div class="form-group">
                    <label for="cantidad-${id}">Cantidad:</label>
                    <select id="cantidad-${id}" class="custom-select">${opcionesCantidad(prenda.talles[0].cantidad)}</select>
                </div>
                <button type="button" class="btn btn-primary" onclick="agregarAlCarrito(${id})">Agregar al carrito</button>`;
        }

        // Acciones del administrador: editar y dar de baja (la baja va por POST, nunca por un link)
        let zonaAdmin = '';
        if (ES_ADMIN) {
            zonaAdmin = `
                <a href="./administrador/editar_producto.php?id_prenda=${id}" class="btn btn-edit">Editar Producto</a>
                <form action="./administrador/eliminar_producto.php" method="post" onsubmit="return confirm('¿Dar de baja esta prenda?');">
                    <input type="hidden" name="id_prenda" value="${id}">
                    <button type="submit" class="btn btn-delete">Eliminar Producto</button>
                </form>`;
        }

        return `
            <div class="seccion_container_productos_tarjeta" id="prenda-${id}">
                <div class="img-wrapper">
                    <img src="./img/productos_img/${escaparHtml(prenda.imagen)}" alt="${escaparHtml(prenda.nombre)}" loading="lazy">
                </div>
                <h4>${escaparHtml(prenda.nombre)}</h4>
                <p>${escaparHtml(prenda.descripcion || '')}</p>
                <p>color: ${escaparHtml(prenda.color || '')}</p>
                <p class="precio">$${Number(prenda.precio).toLocaleString('es-AR')}</p>
                ${zonaCompra}
                ${zonaAdmin}
            </div>`;
    }

    // Agrupa las prendas por categoría y dibuja una sección por cada una
    function renderizarProductos(data) {
        let container = $('.seccion_container_productos');
        let nav = $('#categorias_nav');
        container.empty();
        nav.empty();

        if (data.length === 0) {
            nav.hide();
            container.html("<p class='sin_resultados'>No se encontraron prendas con esos filtros.</p>");
            return;
        }

        let grupos = {};
        data.forEach(prenda => {
            let categoria = prenda.categoria_nombre || 'Otros';
            if (!grupos[categoria]) grupos[categoria] = [];
            grupos[categoria].push(prenda);
        });

        let categorias = Object.keys(grupos).sort();
        nav.toggle(categorias.length > 1);

        categorias.forEach((categoria, i) => {
            let prendas = grupos[categoria];
            let idSeccion = 'cat-' + i;

            nav.append(`<a href="#${idSeccion}">${escaparHtml(categoria)} <span>${prendas.length}</span></a>`);
            container.append(`
                <section class="categoria_seccion" id="${idSeccion}">
                    <h2 class="categoria_titulo">${escaparHtml(categoria)}
                        <span class="categoria_contador">${prendas.length} ${prendas.length === 1 ? 'prenda' : 'prendas'}</span>
                    </h2>
                    <div class="wrapper_container_productos">${prendas.map(tarjetaHtml).join('')}</div>
                </section>`);
        });
    }

    // Al cambiar el talle, la lista de cantidades se ajusta al stock de ese talle
    function actualizarCantidad(idPrenda) {
        let stock = $(`#talle-${idPrenda} option:selected`).data('stock');
        $(`#cantidad-${idPrenda}`).html(opcionesCantidad(stock));
    }

    // Envía la prenda al carrito por AJAX (POST) y actualiza el contador del header con la respuesta
    function agregarAlCarrito(idPrenda) {
        $.ajax({
            url: './restful_api/agregarcarrito.php',
            type: 'POST',
            dataType: 'json',
            data: {
                id: idPrenda,
                talle: $(`#talle-${idPrenda}`).val(),
                cantidad: $(`#cantidad-${idPrenda}`).val()
            },
            success: function(response) {
                if (response.status === 'ok') {
                    $('.carrito-contador').text(response.cantidad_carrito);
                    mostrarNotificacion(response.message, 'success');
                } else {
                    mostrarNotificacion(response.message, 'error');
                }
            },
            error: function() {
                mostrarNotificacion('No se pudo agregar el producto al carrito.', 'error');
            }
        });
    }

    // Si venimos desde el inicio (productos.php#prenda-17), resaltamos esa tarjeta y vamos hasta ella
    function resaltarPrendaElegida() {
        let tarjeta = window.location.hash ? $(window.location.hash) : $();
        if (tarjeta.length) {
            tarjeta.addClass('resaltada');
            tarjeta[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Muestra un aviso abajo a la derecha que desaparece solo a los 3 segundos
    function mostrarNotificacion(texto, tipo) {
        $('#notificacion').text(texto).attr('class', 'notificacion visible ' + tipo);
        setTimeout(() => $('#notificacion').removeClass('visible'), 3000);
    }

    // Escapa los caracteres especiales antes de insertar datos de la base en el HTML (evita XSS)
    function escaparHtml(texto) {
        return $('<div>').text(texto).html().replace(/"/g, '&quot;');
    }
</script>

</html>
