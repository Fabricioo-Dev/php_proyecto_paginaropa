
<?php
include "./../conexionBD.php";

if(isset($_POST["nombre"]) && isset($_POST["descripcion"]) && isset($_POST["talla"]) && isset($_POST["color"]) && isset($_FILES["imagen"]) && isset($_POST["precio"])){
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $tallas = $_POST["talla"];
    $color = $_POST["color"];
    $imagen = $_FILES["imagen"]["nombre"]; //se usa FILES en vez de POST ya que es un archivo lo que se guarda
    $imagen_tmp = $_FILES["imagen"]["tmp_name"]; // Obtener la ubicación temporal del archivo
    $precio = $_POST["precio"];

    // Mover el archivo de imagen al directorio deseado
    $directorio_destino = "xampp/htdocs/VSC PHP/paginaderopa/img/productos_img";
    /*move_uploaded_file(): Esta función de PHP toma dos argumentos: la ubicación temporal del archivo ($_FILES["imagen"]["tmp_name"]) y la ubicación final donde deseas mover el archivo (la ruta completa formada por $directorio_destino . $imagen). La función se encarga de mover físicamente el archivo desde su ubicación temporal a la ubicación definitiva que especificaste.*/
    move_uploaded_file($_FILES["imagen"]["tmp_name"], $directorio_destino . $imagen);

    $consulta = "INSERT INTO prenda(nombre, descripcion, color, talle, imagen, precio) 
                VALUES ('$nombre', '$descripcion', '$color', '$tallas', '$imagen', '$precio')";

    $resultado = mysqli_query($conexion,$consulta);

    if ($resultado){
        echo "<h2>prenda guardada con exito</h2>";
        header('Location: administrador.php');
        exit();
    } else {
        echo "<h2>error al agregar prenda</h2>";
    }
} else {
    echo "<h2>No se inserto bien los requisitos para la prenda</h2>";
}

?>