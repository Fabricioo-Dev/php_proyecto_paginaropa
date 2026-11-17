<?php
// Alta de una marca desde el panel de administración.
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ../login.php');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($nombre === '' || mb_strlen($nombre) > 30 || mb_strlen($descripcion) > 100) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'El nombre es obligatorio (máx. 30 caracteres) y la descripción admite hasta 100.'];
} else {
    try {
        $stmt = $conexion->prepare("INSERT INTO marca (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $descripcion);
        $stmt->execute();
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Marca "' . $nombre . '" guardada correctamente.'];
    } catch (mysqli_sql_exception $e) {
        // 1062 = valor duplicado (marca.nombre tiene índice UNIQUE)
        $texto = ($e->getCode() == 1062) ? 'Esa marca ya existe.' : 'No se pudo guardar la marca.';
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => $texto];
    }
}

header('Location: administrador.php#marcas');
exit();
