<?php
// Registra un nuevo cliente desde el formulario de crear_cuenta.php.
session_start();
include 'conexionBD.php';
/** @var mysqli $conexion */

// trim() quita los espacios sobrantes para que los datos lleguen limpios a la base
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo = trim($_POST['email'] ?? '');
$contrasenia = $_POST['contrasenia'] ?? '';

// Validaciones básicas (los largos máximos coinciden con las columnas de la tabla usuario)
$error = '';
if (strlen($nombre) < 2 || strlen($nombre) > 30 || strlen($apellido) < 2 || strlen($apellido) > 30) {
    $error = 'Nombre y apellido deben tener entre 2 y 30 caracteres.';
} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $error = 'El correo electrónico no es válido.';
} elseif (strlen($contrasenia) < 6) {
    $error = 'La contraseña debe tener al menos 6 caracteres.';
}

if ($error !== '') {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => $error];
    header('Location: ../crear_cuenta.php');
    exit();
}

// Nunca guardamos la contraseña en texto plano: password_hash genera un hash seguro (bcrypt)
$contrasenia_segura = password_hash($contrasenia, PASSWORD_DEFAULT);

// Desde la web siempre se registran clientes (tipo 0).
// Los administradores se asignan directamente en la base de datos.
try {
    $stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido, correo, contrasena, tipo_de_usuario) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $nombre, $apellido, $correo, $contrasenia_segura);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    // El código 1062 significa "valor duplicado": el índice UNIQUE de correo rechazó el INSERT
    $texto = ($e->getCode() == 1062) ? 'Ese correo ya está registrado.' : 'No se pudo crear la cuenta. Intentá nuevamente.';
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => $texto];
    header('Location: ../crear_cuenta.php');
    exit();
}

$_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '¡Cuenta creada con éxito! Ya podés iniciar sesión.'];
header('Location: ../login.php');
exit();
