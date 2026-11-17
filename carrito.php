<?php
session_start();
include 'Backend/conexionBD.php';
/** @var mysqli $conexion */
include_once './Backend/contador_carrito.php';

// El carrito lo puede usar cualquier usuario logueado, sea admin o cliente normal.
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$id_orden = $_SESSION['id_orden'] ?? '';

// Renglones del carrito actual. Solo trae los del usuario de la sesión.
$stmt = $conexion->prepare("SELECT c.id_carrito, c.talle, c.cantidad, p.nombre, p.precio, p.imagen
                            FROM carrito c
                            JOIN prenda p ON c.id_prenda = p.id_prenda
                            WHERE c.id_usuario = ? AND c.id_orden = ?");
$stmt->bind_param("is", $_SESSION['id_usuario'], $id_orden);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito</title>
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/carrito.css">
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="./index.php" class="logo__link" aria-label="ELITE - Inicio"><span class="logo__texto">ELITE</span></a>
        </div>
        <nav>
            <ul>
                <li><a href="nosotros.php">Nosotros</a></li>
                <li><a href="productos.php">Prendas</a></li>
                <li class="carrito"><a href="carrito.php" class="carrito">Carrito <i class="fa-solid fa-cart-shopping"></i>
                        <span class="carrito-contador">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    </a>
                </li>
                <?php if (isset($_SESSION['tipo_de_usuario']) && $_SESSION['tipo_de_usuario'] == 1) { ?>
                    <li><a href="./administrador/administrador.php">Administradores</a></li>
                <?php }; ?>
            </ul>
        </nav>
        <div class="ingreso">
            <a href="pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
        </div>
    </header>

    <main>
        <div class="carrito-contenedor">
            <h1>Tu Carrito de Compras</h1>

            <?php include './Backend/mensaje.php'; ?>

            <?php if (count($items) > 0) { ?>

                <?php foreach ($items as $row) {
                    $subtotal = $row['precio'] * $row['cantidad'];
                    $total += $subtotal;
                ?>
                    <div class="producto-contenido">
                        <img src="img/productos_img/<?php echo htmlspecialchars($row['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($row['nombre']); ?>">
                        <h2><?php echo htmlspecialchars($row['nombre']); ?></h2>
                        <p>Talle Seleccionado: <strong><?php echo htmlspecialchars($row['talle']); ?></strong></p>
                        <p>Cantidad: <?php echo (int) $row['cantidad']; ?></p>
                        <p>Precio Unitario: $<?php echo number_format($row['precio'], 0, ',', '.'); ?></p>
                        <p>Subtotal: $<?php echo number_format($subtotal, 0, ',', '.'); ?></p>

                        <form action="Backend/eliminar_del_carrito.php" method="post">
                            <input type="hidden" name="id_carrito" value="<?php echo (int) $row['id_carrito']; ?>">
                            <button type="submit" class="btn-eliminar">
                                <span class="eliminar">Quitar del carrito <i class="fa-solid fa-trash"></i></span>
                            </button>
                        </form>
                    </div>
                <?php } ?>

                <div class="carrito-total">
                    <p>Total Final: $<?php echo number_format($total, 0, ',', '.'); ?></p>
                    <form action="Backend/compra_carrito.php" method="POST">
                        <button type="submit" class="btn-compra">Confirmar Compra</button>
                    </form>
                </div>

            <?php } else { ?>
                <p class="empty-cart">Tu carrito está vacío. <a href="productos.php">Ver prendas</a></p>
            <?php } ?>
        </div>
    </main>
</body>

</html>
