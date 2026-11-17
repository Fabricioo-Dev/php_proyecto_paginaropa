<?php
// Quita un renglón del carrito del usuario logueado.
session_start();
include 'conexionBD.php';
/** @var mysqli $conexion */

// El carrito lo puede usar cualquier usuario logueado, sea admin o cliente
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit();
}

$id_carrito = (int) ($_POST['id_carrito'] ?? 0);

// El "AND id_usuario = ?" impide borrar renglones de otro usuario
// aunque alguien modifique el id_carrito desde el navegador.
$stmt = $conexion->prepare("DELETE FROM carrito WHERE id_carrito = ? AND id_usuario = ?");
$stmt->bind_param("ii", $id_carrito, $_SESSION['id_usuario']);
$stmt->execute();

$_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Prenda quitada del carrito.'];
header('Location: ../carrito.php');
exit();
