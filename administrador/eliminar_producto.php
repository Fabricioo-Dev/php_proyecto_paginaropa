<?php
// Baja lógica (o reactivación) de una prenda.
// No usamos DELETE porque detalle_venta referencia a la prenda: si la borráramos
// perderíamos el historial de ventas. Solo cambiamos la columna estado, y el
// catálogo muestra únicamente las prendas 'Activa'.
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Solo se acepta por POST (desde el botón del panel), nunca abriendo un link
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: administrador.php#listado');
    exit();
}

$id_prenda = (int) ($_POST['id_prenda'] ?? 0);
$estado = isset($_POST['reactivar']) ? 'Activa' : 'Discontinuada';

$stmt = $conexion->prepare("UPDATE prenda SET estado = ? WHERE id_prenda = ?");
$stmt->bind_param("si", $estado, $id_prenda);
$stmt->execute();

$_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => $estado === 'Activa' ? 'Prenda reactivada.' : 'Prenda dada de baja: ya no aparece en el catálogo.'];
header('Location: administrador.php#listado');
exit();
