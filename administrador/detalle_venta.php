<?php
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */
include_once '../Backend/contador_carrito.php';

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('location:../login.php');
    exit();
}

$id_venta = (int) ($_GET['id_venta'] ?? 0);

// Cabecera de la venta: datos de la orden y del cliente que la hizo
$stmtVenta = $conexion->prepare(
    "SELECT venta.id_orden, venta.fecha, venta.total,
            usuario.nombre AS nombre_usuario, usuario.apellido AS apellido_usuario
     FROM venta
     INNER JOIN usuario ON usuario.id_usuario = venta.id_usuario
     WHERE venta.id_venta = ?"
);
$stmtVenta->bind_param("i", $id_venta);
$stmtVenta->execute();
$venta = $stmtVenta->get_result()->fetch_assoc();

if (!$venta) {
    header('location:./ventas.php');
    exit();
}

// Renglones de la venta. Usamos detalle_venta.precio_unitario (el precio que se cobró)
// y no prenda.precio, que puede haber cambiado después de la compra.
$stmtDetalle = $conexion->prepare(
    "SELECT prenda.nombre, prenda.color, detalle_venta.talle, detalle_venta.cantidad, detalle_venta.precio_unitario
     FROM detalle_venta
     INNER JOIN prenda ON prenda.id_prenda = detalle_venta.id_prenda
     WHERE detalle_venta.id_venta = ?"
);
$stmtDetalle->bind_param("i", $id_venta);
$stmtDetalle->execute();
$detalle = $stmtDetalle->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./../CSS/base.css">
    <link rel="stylesheet" href="./../CSS/administrador.css">
    <title>Detalle de Venta</title>
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

    <!-- Mismo subnav que administrador.php y ventas.php, así no se
         pierde la navegación del panel al entrar a un detalle puntual. -->
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
        <h1>Detalle de la orden <?php echo htmlspecialchars($venta['id_orden']); ?></h1>
        <p>
            Cliente: <strong><?php echo htmlspecialchars($venta['nombre_usuario'] . " " . $venta['apellido_usuario']); ?></strong>
            &nbsp;·&nbsp;
            Fecha: <strong><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></strong>
        </p>
    </div>

    <main class="admin_main">
        <section class="panel">
            <h2 class="panel_titulo"><i class="fa-solid fa-receipt"></i> Prendas de esta compra</h2>
            <div class="panel_cuerpo panel_cuerpo--unico">
                <div class="tabla_scroll">
                    <table class="tabla_admin">
                        <thead>
                            <tr>
                                <th>Prenda</th>
                                <th>Color</th>
                                <th>Talle</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($linea = $detalle->fetch_assoc()):
                                $subtotal = $linea['cantidad'] * $linea['precio_unitario'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($linea['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($linea['color']); ?></td>
                                    <td><?php echo htmlspecialchars($linea['talle']); ?></td>
                                    <td><?php echo $linea['cantidad']; ?></td>
                                    <td class="monto">$<?php echo number_format($linea['precio_unitario'], 0, ',', '.'); ?></td>
                                    <td class="monto">$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">Total de la venta</td>
                                <td class="monto total">$<?php echo number_format($venta['total'], 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <br>
                <a class="link_detalle" href="./ventas.php">&larr; Volver al listado de ventas</a>
            </div>
        </section>
    </main>

</body>

</html>