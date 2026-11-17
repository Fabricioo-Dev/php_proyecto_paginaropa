<?php
// Conexión a la base de datos ropa_online.
// Todas las páginas incluyen este archivo y usan la variable $conexion.

// Configuramos mysqli para que lance una excepción ante cualquier error SQL.
// Esto es lo que nos permite usar try/catch y hacer ROLLBACK en las transacciones.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Detectamos si estamos en la nube (Render) o en local (XAMPP)
    if (getenv('RENDER') !== false) {
        // --- CONEXIÓN PRODUCCIÓN (NUBE - AIVEN) ---
        $servername = "mysql-d616a47-fabrifunes2003-c0b4.f.aivencloud.com";
        $username = "avnadmin";
        $password = getenv('DB_PASSWORD');
        $dbname = "defaultdb";
        $port = 12066;

        $conexion = mysqli_init();
        mysqli_ssl_set($conexion, NULL, NULL, NULL, NULL, NULL); // SSL Requerido por Aiven
        mysqli_real_connect($conexion, $servername, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);
    } else {
        // --- CONEXIÓN DESARROLLO (LOCAL - XAMPP) ---
        $servername = "localhost";
        $username = "root";
        $password = ""; 
        $dbname = "ropa_online";

        $conexion = mysqli_connect($servername, $username, $password, $dbname);
    }
} catch (mysqli_sql_exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Las tablas están en utf8mb4: usamos la misma codificación para no romper tildes ni la ñ
mysqli_set_charset($conexion, "utf8mb4");

// Zona horaria de Argentina para las fechas que arma PHP (por ejemplo, el código de orden)
date_default_timezone_set('America/Argentina/Mendoza');
?>