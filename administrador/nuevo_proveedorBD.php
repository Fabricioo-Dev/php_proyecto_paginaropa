<?php
// Alta de un proveedor desde el panel de administración.
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ../login.php');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo = trim($_POST['correo'] ?? '');

// El teléfono se guarda como texto: solo aceptamos dígitos, espacios, guiones y un + inicial
if ($nombre === '' || !preg_match('/^\+?[0-9 -]{6,20}$/', $telefono) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Revisá los datos del proveedor (nombre, teléfono y correo válidos).'];
} else {
    $stmt = $conexion->prepare("INSERT INTO proveedor (nombre, telefono, correo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $telefono, $correo);
    $stmt->execute();
    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Proveedor "' . $nombre . '" guardado correctamente.'];
}

header('Location: administrador.php#proveedores');
exit();
