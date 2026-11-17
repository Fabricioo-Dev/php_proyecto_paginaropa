<?php
// Guarda los cambios de una prenda (formulario de editar_producto.php).
// Igual que el alta, modifica prenda y prenda_talle dentro de una transacción.
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ../login.php');
    exit();
}

$id_prenda = (int) ($_POST['id_prenda'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$color = trim($_POST['color'] ?? '');
$precio = (int) ($_POST['precio'] ?? 0);
$id_marca = (int) ($_POST['marca'] ?? 0);
$id_proveedor = (int) ($_POST['proveedor'] ?? 0);
$id_categoria = (int) ($_POST['categoria'] ?? 0);
$stock = $_POST['stock'] ?? [];

// La imagen es opcional al editar: si no se sube una nueva, se conserva la actual
$hay_imagen_nueva = isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK;
$extension = strtolower(pathinfo($_FILES['imagen']['name'] ?? '', PATHINFO_EXTENSION));
$imagen_valida = !$hay_imagen_nueva || (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']) && @getimagesize($_FILES['imagen']['tmp_name']) !== false);

if ($nombre === '' || $color === '' || $precio <= 0 || !$imagen_valida) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Revisá los datos: nombre, color, precio mayor a 0 y una imagen válida.'];
    header('Location: editar_producto.php?id_prenda=' . $id_prenda);
    exit();
}

$conexion->begin_transaction();

try {
    // 1) Datos generales
    $stmt = $conexion->prepare("UPDATE prenda SET id_marca = ?, id_proveedor = ?, id_categoria = ?, nombre = ?, descripcion = ?, color = ?, precio = ? WHERE id_prenda = ?");
    $stmt->bind_param("iiisssii", $id_marca, $id_proveedor, $id_categoria, $nombre, $descripcion, $color, $precio, $id_prenda);
    $stmt->execute();

    // 2) Imagen nueva (solo si se eligió un archivo)
    if ($hay_imagen_nueva) {
        $imagen = rand(1, 1000) . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['imagen']['name']));
        $stmt = $conexion->prepare("UPDATE prenda SET imagen = ? WHERE id_prenda = ?");
        $stmt->bind_param("si", $imagen, $id_prenda);
        $stmt->execute();
    }

    // 3) Stock por talle.
    // ON DUPLICATE KEY UPDATE aprovecha el índice UNIQUE (id_prenda, talle):
    // si el talle no existe para la prenda lo inserta, y si ya existe le actualiza la cantidad.
    $stmtTalle = $conexion->prepare("INSERT INTO prenda_talle (id_prenda, talle, cantidad) VALUES (?, ?, ?)
                                     ON DUPLICATE KEY UPDATE cantidad = VALUES(cantidad)");
    foreach (['XS', 'S', 'M', 'L', 'XL'] as $talle) {
        $cantidad = max(0, (int) ($stock[$talle] ?? 0));
        $stmtTalle->bind_param("isi", $id_prenda, $talle, $cantidad);
        $stmtTalle->execute();
    }

    // La imagen nueva se copia recién al final, cuando todas las consultas ya funcionaron
    if ($hay_imagen_nueva) {
        move_uploaded_file($_FILES['imagen']['tmp_name'], './../img/productos_img/' . $imagen);
    }

    $conexion->commit();
    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Prenda "' . $nombre . '" actualizada correctamente.'];
    header('Location: administrador.php#listado');
    exit();
} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No se pudo editar la prenda: ' . $e->getMessage()];
    header('Location: editar_producto.php?id_prenda=' . $id_prenda);
    exit();
}
