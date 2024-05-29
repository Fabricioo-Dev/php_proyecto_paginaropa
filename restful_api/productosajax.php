<?php
//hacer con ajax el agregado del producto 
include './../conexionBD.php';
$consulta = "SELECT * FROM prenda";
$resultado_produtos = mysqli_query($conexion, $consulta);

$array_productos = [];

while($fila = $resultado_produtos->fetch_assoc()){
    $id_prenda = $fila['id_prenda'];
    $id_marca = $fila['id_marca'];
    $id_proveedor = $fila['id_proveedor'];
    $nombre = $fila['nombre'];
    $descripcion = $fila['descripcion'];
    $color = $fila['color'];
    $talle = $fila['talle'];
    $imagen = $fila['imagen'];

    echo $imagen;


    $precio = $fila['precio'];

    $prenda_assoc = [
        "id_prenda" => $id_prenda,
        "id_marca" => $id_marca,
        "id_proveedor" => $id_proveedor,
        "nombre" => $nombre,
        "descripcion" => $descripcion,
        "color" => $color,
        "talle" => $talle,
        "imagen" => $imagen,
        "precio" => $precio
    ];

    array_push($array_productos, $prenda_assoc);
}

echo json_encode($array_productos, JSON_UNESCAPED_SLASHES);

?>