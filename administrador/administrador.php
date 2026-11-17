<?php
session_start();

// Solo un administrador puede entrar al panel
if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ./../login.php');
    exit();
}

include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */
include_once '../Backend/contador_carrito.php';

// Listados para las tablas y para los <select> del formulario de prenda
$lista_proveedores = mysqli_query($conexion, "SELECT id_proveedor, nombre, telefono, correo FROM proveedor ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$lista_marcas = mysqli_query($conexion, "SELECT id_marca, nombre, descripcion FROM marca ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$lista_categorias = mysqli_query($conexion, "SELECT id_categoria, nombre FROM categoria ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

// Todas las prendas (activas y discontinuadas) con su stock total.
// El stock está repartido por talle en prenda_talle, por eso se suma con una subconsulta.
$lista_prendas = mysqli_query($conexion, "SELECT p.id_prenda, p.nombre, p.precio, p.imagen, p.estado, p.id_categoria, m.nombre AS marca,
                                                 (SELECT COALESCE(SUM(pt.cantidad), 0) FROM prenda_talle pt WHERE pt.id_prenda = p.id_prenda) AS stock
                                          FROM prenda p
                                          JOIN marca m ON m.id_marca = p.id_marca
                                          ORDER BY p.estado, p.nombre")->fetch_all(MYSQLI_ASSOC);

// Reporte de inventario usando la FUNCIÓN valor_inventario(precio, cantidad).
// Primero sumamos el stock de todos los talles de cada prenda y ese total se lo pasamos a la función.
$inventario = mysqli_query($conexion, "SELECT p.nombre, p.precio, SUM(pt.cantidad) AS cantidad,
                                              valor_inventario(p.precio, SUM(pt.cantidad)) AS capital_invertido
                                       FROM prenda p
                                       JOIN prenda_talle pt ON pt.id_prenda = p.id_prenda
                                       WHERE p.estado = 'Activa'
                                       GROUP BY p.id_prenda, p.nombre, p.precio")->fetch_all(MYSQLI_ASSOC);

// Reporte que consume la VISTA vista_marcas_detalle (la vista ya resuelve los INNER JOIN)
$vista_marcas = mysqli_query($conexion, "SELECT marca, proveedor, cantidad_prendas FROM vista_marcas_detalle")->fetch_all(MYSQLI_ASSOC);

$talles_posibles = ['XS', 'S', 'M', 'L', 'XL'];
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!------ Font Awesome ------->
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <!-----CSS----->
    <link rel="stylesheet" href="./../CSS/base.css">
    <link rel="stylesheet" href="./../CSS/administrador.css">
    <title>Panel de Administración</title>
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="./../index.php" class="logo__link" aria-label="ELITE - Inicio"><span class="logo__texto">ELITE</span></a>
        </div>
        <nav>
            <ul>
                <li><a href="./../nosotros.php">Nosotros</a></li>
                <li><a href="./../productos.php">Prendas</a></li>
                <li class="carrito"><a href="./../carrito.php" class="carrito">Carrito <i class="fa-solid fa-cart-shopping"></i>
                        <span class="carrito-contador">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    </a>
                </li>
                <li><a href="./administrador.php">Administradores</a></li>
            </ul>
        </nav>
        <div class="ingreso">
            <a href="./../pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
        </div>
    </header>

    <!-- sub-nav del panel -->
    <nav class="admin_subnav">
        <a href="#proveedores"><i class="fa-solid fa-truck"></i> Proveedores</a>
        <a href="#marcas"><i class="fa-solid fa-tag"></i> Marcas</a>
        <a href="#categorias"><i class="fa-solid fa-layer-group"></i> Categorías</a>
        <a href="#prendas"><i class="fa-solid fa-shirt"></i> Cargar prenda</a>
        <a href="#listado"><i class="fa-solid fa-list"></i> Prendas cargadas</a>
        <a href="#inventario"><i class="fa-solid fa-boxes-stacked"></i> Inventario</a>
        <a href="./ventas.php"><i class="fa-solid fa-receipt"></i> Registro de ventas</a>
    </nav>

    <div class="admin_encabezado">
        <h1>Hola Administrador: <?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></h1>
        <p>Desde acá cargás proveedores, marcas, categorías y prendas, y revisás el inventario.</p>
    </div>

    <main class="admin_main">

        <?php include '../Backend/mensaje.php'; ?>

        <!-- ===========================
        PROVEEDORES
        =========================== -->
        <section class="panel" id="proveedores">
            <h2 class="panel_titulo"><i class="fa-solid fa-truck"></i> Proveedores</h2>
            <div class="panel_cuerpo">

                <div class="panel_form">
                    <h3>Añadir proveedor</h3>
                    <form action="./nuevo_proveedorBD.php" method="post">
                        <label for="prov_nombre">Nombre del proveedor</label>
                        <input type="text" id="prov_nombre" name="nombre" maxlength="30" required>

                        <label for="prov_telefono">Teléfono</label>
                        <input type="tel" id="prov_telefono" name="telefono" maxlength="20" required>

                        <label for="prov_correo">Correo electrónico</label>
                        <input type="email" id="prov_correo" name="correo" maxlength="100" required>

                        <input type="submit" value="Guardar proveedor" class="btn_primario">
                    </form>
                </div>

                <div class="panel_tabla">
                    <h3>Cargados (<?php echo count($lista_proveedores); ?>)</h3>
                    <div class="buscador_wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscador_proveedores" class="buscador" placeholder="Buscar por nombre o correo..." aria-label="Buscar proveedor">
                    </div>
                    <div class="tabla_scroll">
                        <table class="tabla_admin" id="tabla_proveedores">
                            <thead>
                                <tr><th>#</th><th>Nombre</th><th>Teléfono</th><th>Correo</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista_proveedores as $p) { ?>
                                    <tr>
                                        <td><?php echo $p['id_proveedor']; ?></td>
                                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($p['correo']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="vacio buscador_sin_resultados" id="proveedores_sin_resultados" hidden>No se encontró ningún proveedor con ese criterio.</p>
                </div>

            </div>
        </section>

        <!-- ===========================
        MARCAS
        =========================== -->
        <section class="panel" id="marcas">
            <h2 class="panel_titulo"><i class="fa-solid fa-tag"></i> Marcas</h2>
            <div class="panel_cuerpo">

                <div class="panel_form">
                    <h3>Añadir marca</h3>
                    <form action="./nueva_marcaBD.php" method="post">
                        <label for="marca_nombre">Nombre de la marca</label>
                        <input type="text" id="marca_nombre" name="nombre" maxlength="30" required>

                        <label for="marca_desc">Descripción</label>
                        <textarea id="marca_desc" name="descripcion" rows="4" maxlength="100"></textarea>

                        <input type="submit" value="Guardar marca" class="btn_primario">
                    </form>
                </div>

                <div class="panel_tabla">
                    <h3>Cargadas (<?php echo count($lista_marcas); ?>)</h3>
                    <div class="buscador_wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscador_marcas" class="buscador" placeholder="Buscar marca por nombre..." aria-label="Buscar marca">
                    </div>
                    <div class="tabla_scroll">
                        <table class="tabla_admin" id="tabla_marcas">
                            <thead>
                                <tr><th>#</th><th>Marca</th><th>Descripción</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista_marcas as $m) { ?>
                                    <tr>
                                        <td><?php echo $m['id_marca']; ?></td>
                                        <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($m['descripcion']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="vacio buscador_sin_resultados" id="marcas_sin_resultados" hidden>No se encontró ninguna marca con ese nombre.</p>
                </div>

            </div>
        </section>

        <!-- ===========================
        CATEGORIAS
        =========================== -->
        <section class="panel" id="categorias">
            <h2 class="panel_titulo"><i class="fa-solid fa-layer-group"></i> Categorías</h2>
            <div class="panel_cuerpo">

                <div class="panel_form">
                    <h3>Añadir categoría</h3>
                    <form action="./nueva_categoriaBD.php" method="post">
                        <label for="cat_nombre">Nombre (ej: Remera, Pantalón)</label>
                        <input type="text" id="cat_nombre" name="nombre" maxlength="50" required>
                        <input type="submit" value="Guardar categoría" class="btn_primario">
                    </form>
                </div>

                <div class="panel_tabla">
                    <h3>Cargadas (<?php echo count($lista_categorias); ?>)</h3>
                    <div class="tabla_scroll">
                        <table class="tabla_admin">
                            <thead>
                                <tr><th>#</th><th>Categoría</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista_categorias as $c) { ?>
                                    <tr>
                                        <td><?php echo $c['id_categoria']; ?></td>
                                        <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

        <!-- ===========================
        ALTA DE PRENDAS
        =========================== -->
        <section class="panel" id="prendas">
            <h2 class="panel_titulo"><i class="fa-solid fa-shirt"></i> Añadir prenda</h2>
            <div class="panel_cuerpo panel_cuerpo--unico">

                <form action="./nuevo_productoBD.php" method="post" enctype="multipart/form-data" class="form_prenda">

                    <div class="form_col">
                        <label for="prenda_nombre">Prenda</label>
                        <input type="text" id="prenda_nombre" name="nombre" maxlength="100" required>

                        <label for="prenda_desc">Descripción</label>
                        <textarea id="prenda_desc" name="descripcion" rows="4" maxlength="100"></textarea>

                        <label for="prenda_color">Color</label>
                        <input type="text" id="prenda_color" name="color" maxlength="30" required>

                        <label for="nuevo_producto_precio">Precio</label>
                        <input type="number" id="nuevo_producto_precio" name="precio" min="1" step="1" required>
                    </div>

                    <div class="form_col">
                        <label for="productos_marca">Marca</label>
                        <select id="productos_marca" name="marca" required>
                            <?php foreach ($lista_marcas as $m) { ?>
                                <option value="<?php echo $m['id_marca']; ?>"><?php echo htmlspecialchars($m['nombre']); ?></option>
                            <?php } ?>
                        </select>

                        <label for="productos_categoria">Categoría</label>
                        <select id="productos_categoria" name="categoria" required>
                            <?php foreach ($lista_categorias as $c) { ?>
                                <option value="<?php echo $c['id_categoria']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                            <?php } ?>
                        </select>

                        <label for="productos_proveedor">Proveedor</label>
                        <select id="productos_proveedor" name="proveedor" required>
                            <?php foreach ($lista_proveedores as $p) { ?>
                                <option value="<?php echo $p['id_proveedor']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php } ?>
                        </select>

                        <label for="imagen_prenda">Imagen</label>
                        <input type="file" id="imagen_prenda" name="imagen" accept="image/*" class="nuevo_producto_imagen" required>
                    </div>

                    <!-- Stock por talle: cada input se envía como stock[XS], stock[S], ... -->
                    <fieldset class="talles_stock">
                        <legend>Stock por talle</legend>
                        <?php foreach ($talles_posibles as $talle) { ?>
                            <div class="talle_item">
                                <label for="stock_<?php echo $talle; ?>"><?php echo $talle; ?></label>
                                <input type="number" id="stock_<?php echo $talle; ?>" name="stock[<?php echo $talle; ?>]" min="0" value="0">
                            </div>
                        <?php } ?>
                    </fieldset>

                    <div class="preview">
                        <p>No se seleccionó ninguna imagen</p>
                    </div>

                    <input type="submit" value="Subir nuevo producto" class="btn_primario">
                </form>

            </div>
        </section>

        <!-- ===========================
        PRENDAS CARGADAS (modificación y baja)
        =========================== -->
        <section class="panel" id="listado">
            <h2 class="panel_titulo"><i class="fa-solid fa-list"></i> Prendas cargadas</h2>
            <div class="panel_cuerpo panel_cuerpo--unico">

                <div class="filtros_fila">
                    <select id="filtro_categoria_prendas" class="filtro_select" aria-label="Filtrar por categoría">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($lista_categorias as $c) { ?>
                            <option value="<?php echo $c['id_categoria']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                        <?php } ?>
                    </select>
                    <div class="buscador_wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscador_prendas" class="buscador" placeholder="Buscar prenda por nombre..." aria-label="Buscar prenda">
                    </div>
                </div>

                <div class="tabla_scroll">
                    <table class="tabla_admin" id="tabla_prendas">
                        <thead>
                            <tr><th>Foto</th><th>Prenda</th><th>Marca</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista_prendas as $prenda) { ?>
                                <tr data-categoria="<?php echo (int) $prenda['id_categoria']; ?>">
                                    <td><img src="../img/productos_img/<?php echo htmlspecialchars($prenda['imagen']); ?>" alt="" class="miniatura"></td>
                                    <td><?php echo htmlspecialchars($prenda['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($prenda['marca']); ?></td>
                                    <td class="monto">$<?php echo number_format($prenda['precio'], 0, ',', '.'); ?></td>
                                    <td><?php echo $prenda['stock']; ?></td>
                                    <td>
                                        <span class="estado <?php echo $prenda['estado'] === 'Activa' ? 'activa' : 'discontinuada'; ?>">
                                            <?php echo $prenda['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="acciones">
                                            <a class="link_detalle" href="./editar_producto.php?id_prenda=<?php echo $prenda['id_prenda']; ?>">Editar</a>
                                            <!-- Baja lógica / reactivación: se envía por POST para que no se ejecute con solo abrir un link -->
                                            <form action="./eliminar_producto.php" method="post" onsubmit="return confirm('¿Confirmás el cambio de estado de esta prenda?');">
                                                <input type="hidden" name="id_prenda" value="<?php echo $prenda['id_prenda']; ?>">
                                                <?php if ($prenda['estado'] === 'Activa') { ?>
                                                    <button type="submit" class="btn_baja">Dar de baja</button>
                                                <?php } else { ?>
                                                    <input type="hidden" name="reactivar" value="1">
                                                    <button type="submit" class="btn_reactivar">Reactivar</button>
                                                <?php } ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <p class="vacio buscador_sin_resultados" id="prendas_sin_resultados" hidden>No se encontró ninguna prenda con esos filtros.</p>
            </div>
        </section>

        <!-- ===========================
        INVENTARIO (FUNCION SQL)
        =========================== -->
        <section class="panel" id="inventario">
            <h2 class="panel_titulo"><i class="fa-solid fa-boxes-stacked"></i> Inventario <small>(función SQL <code>valor_inventario</code>)</small></h2>
            <div class="panel_cuerpo panel_cuerpo--unico">
                <?php if (count($inventario) > 0) { ?>

                    <div class="buscador_wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscador_inventario" class="buscador" placeholder="Buscar producto...">
                    </div>

                    <div class="tabla_scroll">
                        <table class="tabla_admin" id="tabla_inventario">
                            <thead>
                                <tr><th>Prenda</th><th>Precio unitario</th><th>Stock (todos los talles)</th><th>Capital invertido</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_absoluto = 0;
                                foreach ($inventario as $fila) {
                                    $total_absoluto += $fila['capital_invertido'];
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                        <td>$<?php echo number_format($fila['precio'], 0, ',', '.'); ?></td>
                                        <td><?php echo $fila['cantidad']; ?></td>
                                        <td class="monto">$<?php echo number_format($fila['capital_invertido'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Total capital en galpón</td>
                                    <td class="monto total">$<?php echo number_format($total_absoluto, 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                <?php } else { ?>
                    <p class="vacio">No hay prendas activas en el inventario.</p>
                <?php } ?>
            </div>
        </section>

        <!-- ===========================
        VISTA CON INNER JOIN
        =========================== -->
        <section class="panel" id="vista">
            <h2 class="panel_titulo"><i class="fa-solid fa-diagram-project"></i> Marcas por proveedor <small>(vista <code>vista_marcas_detalle</code>)</small></h2>
            <div class="panel_cuerpo panel_cuerpo--unico">
                <?php if (count($vista_marcas) > 0) { ?>
                    <div class="tabla_scroll">
                        <table class="tabla_admin">
                            <thead>
                                <tr><th>Marca</th><th>Proveedor</th><th>Cantidad de prendas</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vista_marcas as $fila) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fila['marca']); ?></td>
                                        <td><?php echo htmlspecialchars($fila['proveedor']); ?></td>
                                        <td><?php echo $fila['cantidad_prendas']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="vacio">Todavía no hay marcas con prendas y proveedor asignados.</p>
                <?php } ?>
            </div>
        </section>

    </main>

    <script>
        // Vista previa de la imagen elegida antes de subirla
        document.querySelector(".nuevo_producto_imagen").addEventListener("change", function() {
            const preview = document.querySelector(".preview");
            const archivo = this.files[0];
            if (archivo) {
                preview.innerHTML = '<ol><li><img><p></p></li></ol>';
                preview.querySelector("img").src = URL.createObjectURL(archivo);
                preview.querySelector("p").textContent = archivo.name;
            } else {
                preview.innerHTML = '<p>No se seleccionó ninguna imagen</p>';
            }
        });

        /* Filtros en vivo de tablas
           Oculta las filas <tr> que no coinciden con el texto escrito (en las columnas indicadas)
           ni con la categoría elegida (atributo data-categoria de la fila). Ignora mayúsculas y tildes.
           Opciones: tabla, buscador, columnas (índices de celda), select (opcional), vacio (opcional). */
        function activarFiltroTabla(opciones) {
            const tabla = document.getElementById(opciones.tabla);
            const buscador = document.getElementById(opciones.buscador);
            if (!tabla || !buscador) return;

            const select = opciones.select ? document.getElementById(opciones.select) : null;
            const mensajeVacio = opciones.vacio ? document.getElementById(opciones.vacio) : null;
            const filas = tabla.querySelectorAll("tbody tr");

            const normalizar = (texto) => texto.toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "");

            function aplicar() {
                const texto = normalizar(buscador.value.trim());
                const categoria = select ? select.value : "";
                let visibles = 0;

                filas.forEach(function(fila) {
                    const coincideTexto = opciones.columnas.some(function(i) {
                        return normalizar(fila.cells[i].textContent).includes(texto);
                    });
                    const coincideCategoria = !categoria || fila.dataset.categoria === categoria;
                    const visible = coincideTexto && coincideCategoria;

                    fila.hidden = !visible;
                    if (visible) visibles++;
                });

                if (mensajeVacio) mensajeVacio.hidden = visibles > 0 || filas.length === 0;
            }

            buscador.addEventListener("input", aplicar);
            if (select) select.addEventListener("change", aplicar);
        }

        activarFiltroTabla({ tabla: "tabla_proveedores", buscador: "buscador_proveedores", columnas: [1, 3], vacio: "proveedores_sin_resultados" });
        activarFiltroTabla({ tabla: "tabla_marcas", buscador: "buscador_marcas", columnas: [1], vacio: "marcas_sin_resultados" });
        activarFiltroTabla({ tabla: "tabla_prendas", buscador: "buscador_prendas", columnas: [1], select: "filtro_categoria_prendas", vacio: "prendas_sin_resultados" });
        activarFiltroTabla({ tabla: "tabla_inventario", buscador: "buscador_inventario", columnas: [0] });
    </script>

</body>

</html>
