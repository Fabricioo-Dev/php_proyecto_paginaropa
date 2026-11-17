<?php
// Valida el formulario de login.php e inicia la sesión del usuario.
session_start();
include 'conexionBD.php';
/** @var mysqli $conexion */

// El trigger de la tabla usuario guarda los correos en minúsculas, así que buscamos igual
$correo = strtolower(trim($_POST['email'] ?? ''));
$contrasenia = $_POST['contrasenia'] ?? '';

// Buscamos al usuario solo por el correo (consulta preparada para evitar inyección SQL)
$stmt = $conexion->prepare("SELECT id_usuario, nombre, apellido, correo, contrasena, tipo_de_usuario FROM usuario WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// password_verify compara la contraseña ingresada con el hash guardado en la base.
// Si el correo no existe o la clave no coincide damos el mismo mensaje, para no revelar qué correos están registrados.
if (!$usuario || !password_verify($contrasenia, $usuario['contrasena'])) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Usuario o contraseña incorrectos.'];
    header('Location: ../login.php');
    exit();
}

// Generamos un id de sesión nuevo al iniciar sesión (evita la fijación de sesión)
session_regenerate_id(true);

$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['apellido'] = $usuario['apellido'];
$_SESSION['correo'] = $usuario['correo'];
$_SESSION['tipo_de_usuario'] = (int) $usuario['tipo_de_usuario']; // 1 = administrador, 0 = cliente
$_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '¡Hola ' . $usuario['nombre'] . '! Iniciaste sesión correctamente.'];

header('Location: ../index.php');
exit();
