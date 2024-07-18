<?php
session_start();

if (isset($_POST['hacer_compra'])) {
    // Conexión a la base de datos
    include 'conexionBD.php';

    // Variables para almacenar los datos de la compra
    $usuarioId = $_SESSION['id_usuario']; // Suponiendo que el ID del usuario está almacenado en la sesión
    $total = 0;
    $fechaCompra = date('Y-m-d H:i:s');


    $dia = date("Ymd");
    $valuerandom = strtoupper(substr(uniqid(sha1(time())),0,4));
    $valororden = $dia.$valuerandom;

    $insercionesExitosas = true;

    // Recorrer el carrito y preparar los datos para la inserción
    foreach ($_SESSION['carrito'] as $item) {
        $prendaId = $item['id'];
        $cantidad = $item['cantidad'];
        

        $consulta = "INSERT INTO carrito (id_usuario, id_prenda, cantidad,id_orden) VALUES ($usuarioId, $prendaId, $cantidad,'$valororden')";
        $resultado = mysqli_query($conexion, $consulta);

        if (!$resultado) {
            $insercionesExitosas = false;
            break; // Salir del bucle si hay un error
        }
    }

    // Aquí puedes agregar más lógica para procesar la compra, como actualizar el inventario, enviar un correo de confirmación, etc

    if ($insercionesExitosas) {
        // Vaciar el carrito
        $_SESSION['carrito'] = [];
        // Redirigir de vuelta al carrito con un mensaje de éxito
        header("Location: ./../carrito.php?compra=exitosa");
    } else {
        // Manejar el error, redirigir con un mensaje de error o mostrar un mensaje
        header("Location: ./../carrito.php?compra=fallida");
    }

    exit();
}
