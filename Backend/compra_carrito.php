<?php
// Confirma la compra del carrito actual.
//
// La compra escribe en varias tablas (venta, detalle_venta, prenda_talle y carrito),
// por eso se hace dentro de una TRANSACCIÓN: o se guardan todos los cambios juntos
// (COMMIT) o no se guarda ninguno (ROLLBACK). Así nunca queda una venta a medias,
// por ejemplo con el stock descontado pero sin el detalle cargado.
session_start();
include 'conexionBD.php';
/** @var mysqli $conexion */

// Solo usuarios logueados, por POST y con una orden abierta
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_orden'])) {
    header('Location: ../carrito.php');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_orden = $_SESSION['id_orden'];

$conexion->begin_transaction();

try {
    // 1) Renglones del carrito con el precio y el estado actual de cada prenda.
    // El ORDER BY hace que las filas de stock se bloqueen siempre en el mismo orden,
    // lo que evita bloqueos cruzados (deadlocks) entre dos compras simultáneas.
    $stmt = $conexion->prepare("SELECT c.id_prenda, c.talle, c.cantidad, p.nombre, p.precio, p.estado
                                FROM carrito c
                                JOIN prenda p ON p.id_prenda = c.id_prenda
                                WHERE c.id_usuario = ? AND c.id_orden = ?
                                ORDER BY c.id_prenda ASC, c.talle ASC");
    $stmt->bind_param("is", $id_usuario, $id_orden);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($items) === 0) {
        throw new Exception("El carrito está vacío.");
    }

    $total = 0;
    foreach ($items as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    // 2) Cabecera de la venta
    $stmt = $conexion->prepare("INSERT INTO venta (id_usuario, id_orden, total) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $id_usuario, $id_orden, $total);
    $stmt->execute();
    $id_venta = $conexion->insert_id;

    // Sentencias que se repiten para cada renglón
    // FOR UPDATE bloquea la fila del stock hasta el COMMIT: si dos clientes compran
    // la última unidad al mismo tiempo, el segundo espera y ve el stock ya actualizado.
    $stmtStock = $conexion->prepare("SELECT cantidad FROM prenda_talle WHERE id_prenda = ? AND talle = ? FOR UPDATE");
    $stmtDescontar = $conexion->prepare("UPDATE prenda_talle SET cantidad = cantidad - ? WHERE id_prenda = ? AND talle = ?");
    $stmtDetalle = $conexion->prepare("INSERT INTO detalle_venta (id_venta, id_prenda, talle, cantidad, precio_unitario) VALUES (?, ?, ?, ?, ?)");

    // 3) Por cada renglón: verificar stock, descontarlo y guardar el detalle
    foreach ($items as $item) {
        // Si la prenda se dio de baja mientras estaba en el carrito, no se puede vender
        if ($item['estado'] !== 'Activa') {
            throw new Exception($item['nombre'] . " ya no está disponible.");
        }

        $stmtStock->bind_param("is", $item['id_prenda'], $item['talle']);
        $stmtStock->execute();
        $stock = $stmtStock->get_result()->fetch_assoc();

        if (!$stock || $stock['cantidad'] < $item['cantidad']) {
            // Al lanzar la excepción saltamos al catch, que deshace todo lo anterior
            throw new Exception("No hay stock suficiente de " . $item['nombre'] . " (talle " . $item['talle'] . ").");
        }

        $stmtDescontar->bind_param("iis", $item['cantidad'], $item['id_prenda'], $item['talle']);
        $stmtDescontar->execute();

        // Guardamos el precio del momento: si mañana cambia, el historial de la venta no se altera
        $stmtDetalle->bind_param("iisii", $id_venta, $item['id_prenda'], $item['talle'], $item['cantidad'], $item['precio']);
        $stmtDetalle->execute();
    }

    // 4) Vaciamos el carrito ya comprado
    $stmt = $conexion->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_orden = ?");
    $stmt->bind_param("is", $id_usuario, $id_orden);
    $stmt->execute();

    // Todo salió bien: confirmamos los cambios
    $conexion->commit();

    unset($_SESSION['id_orden']); // la próxima prenda que se agregue abre una orden nueva
    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '¡Compra finalizada con éxito! Número de orden: ' . $id_orden];
    header('Location: detalles_pedido.php?id_venta=' . $id_venta);
    exit();
} catch (Exception $e) {
    // Algo falló: deshacemos todo lo que se hizo dentro de la transacción
    $conexion->rollback();

    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No se pudo completar la compra: ' . $e->getMessage()];
    header('Location: ../carrito.php');
    exit();
}
