<?php
// Alta de una categoría desde el panel de administración.
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ../login.php');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === '') {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'El nombre de la categoría es obligatorio.'];
} else {
    try {
        $stmt = $conexion->prepare("INSERT INTO categoria (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Categoría "' . $nombre . '" guardada correctamente.'];
    } catch (mysqli_sql_exception $e) {
        // 1062 = valor duplicado (categoria.nombre tiene índice UNIQUE)
        $texto = ($e->getCode() == 1062) ? 'Esa categoría ya existe.' : 'No se pudo guardar la categoría.';
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => $texto];
    }
}

header('Location: administrador.php#categorias');
exit();
