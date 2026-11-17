<?php
// detalles_pedido.php
// Muestra las prendas de una compra (detalle_venta) con su subtotal y el total pagado.
session_start();
include 'conexionBD.php';
/** @var mysqli $conexion */
include_once 'contador_carrito.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ./../login.php');
    exit();
}

$es_admin = isset($_SESSION['tipo_de_usuario']) && $_SESSION['tipo_de_usuario'] == 1;
$id_venta = (int) ($_GET['id_venta'] ?? 0);

// Un cliente solo puede ver SUS compras: el JOIN con venta y el filtro por id_usuario
// evitan que alguien vea pedidos ajenos cambiando el número en la URL.
// El administrador puede ver cualquier venta, por eso el (? = 1 OR ...).
$es_admin_numero = $es_admin ? 1 : 0;
$stmt = $conexion->prepare("SELECT p.nombre, dv.talle, dv.cantidad, dv.precio_unitario
                            FROM detalle_venta dv
                            JOIN prenda p ON p.id_prenda = dv.id_prenda
                            JOIN venta v ON v.id_venta = dv.id_venta
                            WHERE dv.id_venta = ? AND (? = 1 OR v.id_usuario = ?)");
$stmt->bind_param("iii", $id_venta, $es_admin_numero, $_SESSION['id_usuario']);
$stmt->execute();
$detalles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido</title>
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./../CSS/base.css">
    <link rel="stylesheet" href="./../CSS/detalles_pedido.css">
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="./../index.php" class="logo__link" aria-label="ELITE - Inicio"><span class="logo__texto">ELITE</span></a>
        </div>
        <nav>
            <ul>
                <li class="#Nosotros"><a href="./../nosotros.php">Nosotros</a></li>
                <li class="#Productos"><a href="./../productos.php">Prendas</a></li>
                <li class="carrito">
                    <a href="./../carrito.php" class="carrito">
                        Carrito <i class="fa-solid fa-cart-shopping"></i>
                        <span class="carrito-contador">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    </a>
                </li>
                <?php if ($es_admin) { ?>
                    <li class="#administrador"><a href="./../administrador/administrador.php">Administradores</a></li>
                <?php }; ?>
            </ul>
        </nav>
        <div class="ingreso">
            <a href="./../pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
        </div>
    </header>

    <main class="contenido_detalles">
        <?php include 'mensaje.php'; ?>

        <?php if (count($detalles) > 0) { ?>
            <h2>Detalles del Pedido #<?php echo $id_venta; ?></h2>
            <div class="tabla_scroll">
                <table class="tabla_detalles">
                    <thead>
                        <tr>
                            <th>Nombre de Prenda</th>
                            <th>Talle</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($detalles as $row) {
                            $subtotal = $row['cantidad'] * $row['precio_unitario'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['talle']); ?></td>
                                <td><?php echo (int) $row['cantidad']; ?></td>
                                <td>$<?php echo number_format($row['precio_unitario'], 0, ',', '.'); ?></td>
                                <td>$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <h3>Total Pagado: $<?php echo number_format($total, 0, ',', '.'); ?></h3>
        <?php } else { ?>
            <p>No se encontraron detalles para este pedido o no tenés permisos para verlo.</p>
        <?php } ?>

        <a href="./../pagina_perfil.php#mis-pedidos" class="boton_volver">Volver a mis pedidos</a>
    </main>
</body>

</html>
