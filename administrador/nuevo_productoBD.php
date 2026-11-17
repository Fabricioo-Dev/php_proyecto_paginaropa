<?php
// Alta de una prenda con su stock por talle.
// Se escribe en dos tablas (prenda y prenda_talle), por eso va dentro de una transacción:
// si falla el stock, tampoco queda guardada la prenda.
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ./../login.php');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$color = trim($_POST['color'] ?? '');
$precio = (int) ($_POST['precio'] ?? 0);
$id_marca = (int) ($_POST['marca'] ?? 0);
$id_proveedor = (int) ($_POST['proveedor'] ?? 0);
$id_categoria = (int) ($_POST['categoria'] ?? 0);
$stock = $_POST['stock'] ?? []; // llega como stock[XS], stock[S], ...

// Validamos los datos y que el archivo sea realmente una imagen
$subio_archivo = isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK;
$extension = strtolower(pathinfo($_FILES['imagen']['name'] ?? '', PATHINFO_EXTENSION));
$es_imagen = $subio_archivo && in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']) && @getimagesize($_FILES['imagen']['tmp_name']) !== false;

if ($nombre === '' || $color === '' || $precio <= 0 || !$es_imagen) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Revisá los datos: nombre, color, precio mayor a 0 y una imagen válida (jpg, png, webp o gif).'];
    header('Location: administrador.php#prendas');
    exit();
}

// Nombre único para la imagen: número al azar + nombre original sin caracteres raros
$imagen = rand(1, 1000) . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['imagen']['name']));

$conexion->begin_transaction();

try {
    // 1) Datos generales de la prenda
    $stmt = $conexion->prepare("INSERT INTO prenda (nombre, descripcion, color, imagen, precio, id_proveedor, id_marca, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiii", $nombre, $descripcion, $color, $imagen, $precio, $id_proveedor, $id_marca, $id_categoria);
    $stmt->execute();
    $id_prenda = $conexion->insert_id; // id generado por el AUTO_INCREMENT

    // 2) Una fila de stock por cada talle
    $stmtTalle = $conexion->prepare("INSERT INTO prenda_talle (id_prenda, talle, cantidad) VALUES (?, ?, ?)");
    foreach (['XS', 'S', 'M', 'L', 'XL'] as $talle) {
        $cantidad = max(0, (int) ($stock[$talle] ?? 0));
        $stmtTalle->bind_param("isi", $id_prenda, $talle, $cantidad);
        $stmtTalle->execute();
    }

    // La imagen se mueve a la carpeta del sitio recién cuando sabemos que los INSERT funcionaron
    move_uploaded_file($_FILES['imagen']['tmp_name'], './../img/productos_img/' . $imagen);

    $conexion->commit();
    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Prenda "' . $nombre . '" agregada correctamente.'];
} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No se pudo agregar la prenda: ' . $e->getMessage()];
}

header('Location: administrador.php#listado');
exit();
