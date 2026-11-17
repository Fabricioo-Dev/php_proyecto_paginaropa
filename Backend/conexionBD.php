<?php
// Conexión a la base de datos ropa_online.
// Todas las páginas incluyen este archivo y usan la variable $conexion.

$servername = "localhost"; // Nombre del servidor
$username = "root";        // Usuario de la base de datos
$password = "";            // Contraseña de la base de datos
$dbname = "ropa_online";   // Nombre de la base de datos

// Configuramos mysqli para que lance una excepción ante cualquier error SQL.
// Esto es lo que nos permite usar try/catch y hacer ROLLBACK en las transacciones.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = mysqli_connect($servername, $username, $password, $dbname);
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Las tablas están en utf8mb4: usamos la misma codificación para no romper tildes ni la ñ
mysqli_set_charset($conexion, "utf8mb4");

// Zona horaria de Argentina para las fechas que arma PHP (por ejemplo, el código de orden)
date_default_timezone_set('America/Argentina/Mendoza');
