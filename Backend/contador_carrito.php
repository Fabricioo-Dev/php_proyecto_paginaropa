<?php
// Calcula cuántas prendas tiene el usuario en su carrito actual.
// El resultado queda en $cantidad_carrito y se muestra en el ícono del carrito del header.
include_once __DIR__ . '/conexionBD.php';
/** @var mysqli $conexion */

$cantidad_carrito = 0;

// El carrito actual se identifica por el usuario logueado y el código de orden abierto en la sesión
if (isset($_SESSION['id_usuario']) && isset($_SESSION['id_orden'])) {
    $stmtCount = $conexion->prepare("SELECT COALESCE(SUM(cantidad), 0) AS total FROM carrito WHERE id_usuario = ? AND id_orden = ?");
    $stmtCount->bind_param("is", $_SESSION['id_usuario'], $_SESSION['id_orden']);
    $stmtCount->execute();
    $cantidad_carrito = (int) $stmtCount->get_result()->fetch_assoc()['total'];
}
