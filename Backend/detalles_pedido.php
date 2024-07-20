<?php
session_start();

if ($_SESSION['tipo_de_usuario'] != 1) {
    header('location: ./login.php');
}
include 'conexionBD.php';

$order_id = isset($_GET['id_orden']) ? intval($_GET['id_orden']) : 0;

// Consulta para obtener los detalles del pedido
$consulta = "
    SELECT p.nombre, c.cantidad, p.precio
    FROM carrito c
    JOIN prendas p ON c.id_prenda = p.id_prenda
    WHERE c.id_orden = $order_id
";
$resultado = mysqli_query($conexion, $consulta);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    echo "<h2>Detalles del Pedido #{$order_id}</h2>";
    echo '<table class="tabla_detalles">';
    echo '<thead><tr><th>Nombre de Prenda</th><th>Cantidad</th><th>Precio Unitario</th></tr></thead>';
    echo '<tbody>';

    $total = 0;
    while ($row = mysqli_fetch_assoc($resultado)) {
        $subtotal = $row['cantidad'] * $row['precio'];
        $total += $subtotal;
        echo '<tr>';
        echo "<td>{$row['nombre']}</td>";
        echo "<td>{$row['cantidad']}</td>";
        echo "<td>$" . number_format($row['precio'], 2) . "</td>";
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo "<h3>Total: $" . number_format($total, 2) . "</h3>";
} else {
    echo "<p>No se encontraron detalles para el pedido #{$order_id}.</p>";
}

// Cerrar la conexión
mysqli_close($conexion);
?>
