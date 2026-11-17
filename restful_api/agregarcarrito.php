<?php
// Agrega una prenda al carrito. Lo llama productos.php por AJAX (POST) y responde en JSON:
//   {"status": "ok",    "message": "...", "cantidad_carrito": 3}
//   {"status": "error", "message": "motivo"}
session_start();
include '../Backend/conexionBD.php';
/** @var mysqli $conexion */

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["status" => "error", "message" => "Tenés que iniciar sesión para comprar."]);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_prenda = (int) ($_POST['id'] ?? 0);
$cantidad = (int) ($_POST['cantidad'] ?? 0);
$talle = $_POST['talle'] ?? '';

if ($id_prenda <= 0 || $cantidad <= 0 || !in_array($talle, ['XS', 'S', 'M', 'L', 'XL'])) {
    echo json_encode(["status" => "error", "message" => "Datos inválidos o talle no seleccionado."]);
    exit();
}

// El código de orden se genera con la primera prenda y vive en la sesión hasta que se compra.
// Formato: fecha (AAAAMMDD) + 4 caracteres al azar, por ejemplo 20261117A3F9.
if (!isset($_SESSION['id_orden'])) {
    $_SESSION['id_orden'] = date("Ymd") . strtoupper(bin2hex(random_bytes(2)));
}
$id_orden = $_SESSION['id_orden'];

// Stock disponible del talle elegido (solo de prendas activas)
$stmt = $conexion->prepare("SELECT pt.cantidad FROM prenda_talle pt
                            JOIN prenda p ON p.id_prenda = pt.id_prenda
                            WHERE pt.id_prenda = ? AND pt.talle = ? AND p.estado = 'Activa'");
$stmt->bind_param("is", $id_prenda, $talle);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stock = $fila ? $fila['cantidad'] : 0;

// ¿La prenda con ese talle ya estaba en el carrito?
$stmt = $conexion->prepare("SELECT id_carrito, cantidad FROM carrito WHERE id_usuario = ? AND id_orden = ? AND id_prenda = ? AND talle = ?");
$stmt->bind_param("isis", $id_usuario, $id_orden, $id_prenda, $talle);
$stmt->execute();
$renglon = $stmt->get_result()->fetch_assoc();
$en_carrito = $renglon ? $renglon['cantidad'] : 0;

// No dejamos agregar más unidades de las que hay en stock
if ($en_carrito + $cantidad > $stock) {
    echo json_encode(["status" => "error", "message" => "No hay stock suficiente. Disponible: $stock (ya tenés $en_carrito en el carrito)."]);
    exit();
}

if ($renglon) {
    // Ya estaba: sumamos la cantidad al renglón existente
    $nueva_cantidad = $en_carrito + $cantidad;
    $stmt = $conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id_carrito = ?");
    $stmt->bind_param("ii", $nueva_cantidad, $renglon['id_carrito']);
} else {
    // No estaba: agregamos un renglón nuevo
    $stmt = $conexion->prepare("INSERT INTO carrito (id_usuario, id_orden, id_prenda, talle, cantidad) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isisi", $id_usuario, $id_orden, $id_prenda, $talle, $cantidad);
}
$stmt->execute();

// Devolvemos el total actualizado para refrescar el contador del header
include '../Backend/contador_carrito.php';
echo json_encode(["status" => "ok", "message" => "Prenda agregada al carrito.", "cantidad_carrito" => $cantidad_carrito]);
