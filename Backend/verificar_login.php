<?php 
include 'conexionBD.php';

//en esta linea se confirma si se mando el formulario 
//en esta con strlen se obtiene la longitud de los campos del form y se verifica que se hayan completado
if ($_POST){ 
if (strlen($_POST['email']) > 0 && strlen($_POST['contraseña']) > 0){
    //trim() me sirve para sacar los espacios en blancos asi llega el codigo limpio a la BD
    $correo = trim($_POST['email']);
    $contrasenia = sha1(trim($_POST['contraseña']));

    $consulta = "SELECT * FROM usuario WHERE correo = '$correo' AND contrasena = '$contrasenia'";
        
    // Ejecutamos la consulta en la base de datos
    $resultado = mysqli_query($conexion, $consulta);

    session_start();
    if($resultado && mysqli_num_rows($resultado) > 0){
        $usuario = mysqli_fetch_assoc($resultado);
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['tipo_de_usuario']= $usuario['tipo_de_usuario'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        header('location:../index.php');
        exit();
    } else {
        $_SESSION['error_login'] = "Usuario o contraseña incorrectos...";
        header('Location: ../login.php');  // Redirige a login.php
        exit();
    }
}
}
?>