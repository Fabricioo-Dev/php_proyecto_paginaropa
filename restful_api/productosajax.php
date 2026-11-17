<?php
// API que devuelve en formato JSON las prendas activas con sus talles en stock.
// La consume productos.php mediante AJAX para armar el catálogo sin recargar la página.
include './../Backend/conexionBD.php';
/** @var mysqli $conexion */

header('Content-Type: application/json; charset=utf-8');

// 1) Prendas activas con el nombre de su categoría
$prendas = mysqli_query($conexion, "SELECT p.id_prenda, p.id_categoria, p.nombre, p.descripcion, p.color, p.imagen, p.precio,
                                           c.nombre AS categoria_nombre
                                    FROM prenda p
                                    LEFT JOIN categoria c ON c.id_categoria = p.id_categoria
                                    WHERE p.estado = 'Activa'")->fetch_all(MYSQLI_ASSOC);

// 2) Todos los talles con stock en una sola consulta (en lugar de una consulta por cada prenda)
$talles = mysqli_query($conexion, "SELECT id_prenda, talle, cantidad
                                   FROM prenda_talle
                                   WHERE cantidad > 0
                                   ORDER BY FIELD(talle, 'XS', 'S', 'M', 'L', 'XL')")->fetch_all(MYSQLI_ASSOC);

// 3) Le asignamos a cada prenda la lista de sus talles: [{talle, cantidad}, ...]
foreach ($prendas as $i => $prenda) {
    $prendas[$i]['talles'] = [];
    foreach ($talles as $t) {
        if ($t['id_prenda'] == $prenda['id_prenda']) {
            $prendas[$i]['talles'][] = ['talle' => $t['talle'], 'cantidad' => (int) $t['cantidad']];
        }
    }
}

echo json_encode($prendas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
