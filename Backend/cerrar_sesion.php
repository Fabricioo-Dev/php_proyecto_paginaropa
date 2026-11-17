<?php
// Cierra la sesión del usuario y vuelve al inicio.
session_start();

session_unset();   // Borra todas las variables de sesión
session_destroy(); // Destruye la sesión en el servidor

header('Location: ../index.php');
exit();
