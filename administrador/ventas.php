<?php
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */
include_once '../Backend/contador_carrito.php';

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('location:../login.php');
    exit();
}

// Una fila por venta, con el cliente que la hizo y cuántos renglones tiene.
// LEFT JOIN con detalle_venta para que una venta aparezca aunque no tenga renglones cargados.
$ventas = mysqli_query($conexion, "SELECT v.id_venta, v.id_orden, v.fecha, v.total,
                                          u.nombre, u.apellido, u.correo,
                                          COUNT(dv.id_detalle) AS cantidad_items
                                   FROM venta v
                                   JOIN usuario u ON u.id_usuario = v.id_usuario
                                   LEFT JOIN detalle_venta dv ON dv.id_venta = v.id_venta
                                   GROUP BY v.id_venta, v.id_orden, v.fecha, v.total, u.nombre, u.apellido, u.correo
                                   ORDER BY v.fecha DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./../CSS/base.css">
    <link rel="stylesheet" href="./../CSS/administrador.css">
    <title>Registro de Ventas</title>
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

    <!-- Mismo subnav que administrador.php, así la navegación del panel no desaparece al entrar acá -->
    <nav class="admin_subnav">
        <a href="./administrador.php#proveedores"><i class="fa-solid fa-truck"></i> Proveedores</a>
        <a href="./administrador.php#marcas"><i class="fa-solid fa-tag"></i> Marcas</a>
        <a href="./administrador.php#categorias"><i class="fa-solid fa-layer-group"></i> Categorías</a>
        <a href="./administrador.php#prendas"><i class="fa-solid fa-shirt"></i> Cargar prenda</a>
        <a href="./administrador.php#listado"><i class="fa-solid fa-list"></i> Prendas cargadas</a>
        <a href="./administrador.php#inventario"><i class="fa-solid fa-boxes-stacked"></i> Inventario</a>
        <a href="./ventas.php"><i class="fa-solid fa-receipt"></i> Registro de ventas</a>
    </nav>

    <div class="admin_encabezado">
        <h1>Registro de Ventas</h1>
        <p>Listado completo de todas las órdenes, ordenadas de la más reciente a la más antigua.</p>
    </div>

    <main class="admin_main">
        <section class="panel">
            <h2 class="panel_titulo"><i class="fa-solid fa-receipt"></i> Ventas</h2>
            <div class="panel_cuerpo panel_cuerpo--unico">
                <?php if (count($ventas) > 0) { ?>
                    <div class="buscador_wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="buscador_registro_ventas" class="buscador" placeholder="Buscar por N° de orden, cliente o correo...">
                    </div>

                    <div class="tabla_scroll">
                        <table class="tabla_admin" id="tabla_registro_ventas">
                            <thead>
                                <tr><th>N° Orden</th><th>Usuario</th><th>Correo</th><th>Fecha</th><th>Ítems</th><th>Total</th><th>Detalle</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $row) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_orden']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre'] . " " . $row['apellido']); ?></td>
                                        <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                                        <td><?php echo $row['cantidad_items']; ?></td>
                                        <td class="monto">$<?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                        <td><a class="link_detalle" href="./detalle_venta.php?id_venta=<?php echo $row['id_venta']; ?>">Ver detalle</a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="vacio" id="ventas_sin_resultados" style="display:none;">No se encontró ninguna venta con ese criterio.</p>
                <?php } else { ?>
                    <p class="vacio">Todavía no hay ventas registradas.</p>
                <?php } ?>
            </div>
        </section>
    </main>

    <script>
        // Buscador: oculta las ventas cuyo número de orden, cliente o correo no contienen el texto escrito
        document.getElementById("buscador_registro_ventas")?.addEventListener("input", function() {
            const texto = this.value.trim().toLowerCase();
            let visibles = 0;
            document.querySelectorAll("#tabla_registro_ventas tbody tr").forEach(function(fila) {
                const coincide = fila.textContent.toLowerCase().includes(texto);
                fila.style.display = coincide ? "" : "none";
                if (coincide) visibles++;
            });
            document.getElementById("ventas_sin_resultados").style.display = visibles === 0 ? "block" : "none";
        });
    </script>

</body>

</html>
