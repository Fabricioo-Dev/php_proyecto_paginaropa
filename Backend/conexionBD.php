
<?php

$servername = "localhost"; // Nombre del servidor
$username = "root"; // Nombre de usuario de la base de datos
$password = ""; // Contraseña de la base de datos
$dbname = "ropa_online"; // Nombre de la base de datos

// Crear la conexión
$conexion = mysqli_connect($servername, $username, $password, $dbname);


if (!$conexion) { //esta linea es por si se produce un error al conectar
    die("Error de conexión: " . mysqli_connect_error());
}/*else{
    echo "Conexión exitosa";
}*/

?>