<?php
// Tabla con el historial de compras del usuario logueado.
// Se incluye desde pagina_perfil.php, que ya abrió la sesión y la conexión ($conexion).
/** @var mysqli $conexion */

// Solo las ventas del usuario de la sesión
$stmt = $conexion->prepare("SELECT id_venta, id_orden, fecha, total FROM venta WHERE id_usuario = ? ORDER BY fecha DESC");
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$pedidos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (count($pedidos) === 0) {
    echo '<p>Todavía no tenés compras registradas.</p>';
} else {
    echo '<h2>Mis pedidos</h2>';
    echo '<div class="tabla_scroll">';
    echo '<table class="tabla_pedidos">';
    echo '<thead><tr><th>Nº Orden</th><th>Fecha de Compra</th><th>Total</th><th>Detalles</th></tr></thead>';
    echo '<tbody>';
    foreach ($pedidos as $pedido) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($pedido['id_orden']) . '</td>';
        echo '<td>' . date('d/m/Y H:i', strtotime($pedido['fecha'])) . '</td>';
        echo '<td>$' . number_format($pedido['total'], 0, ',', '.') . '</td>';
        echo '<td><a href="./Backend/detalles_pedido.php?id_venta=' . (int) $pedido['id_venta'] . '">Ver detalles</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}
