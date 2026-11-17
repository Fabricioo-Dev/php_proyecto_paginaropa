<?php
session_start();
include "./../Backend/conexionBD.php";
/** @var mysqli $conexion */
include_once '../Backend/contador_carrito.php';

if (!isset($_SESSION['tipo_de_usuario']) || $_SESSION['tipo_de_usuario'] != 1) {
    header('Location: ./../login.php');
    exit();
}

$id_prenda = (int) ($_GET['id_prenda'] ?? 0);

// Datos actuales de la prenda para precargar el formulario
$stmt = $conexion->prepare("SELECT * FROM prenda WHERE id_prenda = ?");
$stmt->bind_param("i", $id_prenda);
$stmt->execute();
$prenda = $stmt->get_result()->fetch_assoc();

if (!$prenda) {
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'La prenda solicitada no existe.'];
    header('Location: administrador.php#listado');
    exit();
}

// Stock actual de cada talle (los talles sin fila en prenda_talle quedan en 0)
$talles_posibles = ['XS', 'S', 'M', 'L', 'XL'];
$stock_actual = array_fill_keys($talles_posibles, 0);

$stmt = $conexion->prepare("SELECT talle, cantidad FROM prenda_talle WHERE id_prenda = ?");
$stmt->bind_param("i", $id_prenda);
$stmt->execute();
foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $fila) {
    $stock_actual[$fila['talle']] = $fila['cantidad'];
}

// Opciones de los select
$marcas = mysqli_query($conexion, "SELECT id_marca, nombre FROM marca ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$categorias = mysqli_query($conexion, "SELECT id_categoria, nombre FROM categoria ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$proveedores = mysqli_query($conexion, "SELECT id_proveedor, nombre FROM proveedor ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!------ Font Awesome ------->
    <script src="https://kit.fontawesome.com/5f6de38f20.js" crossorigin="anonymous"></script>
    <!-----CSS----->
    <link rel="stylesheet" href="./../CSS/base.css">
    <link rel="stylesheet" href="./../CSS/administrador.css">
    <title>Administradores</title>
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="./../index.php" class="logo__link" aria-label="ELITE - Inicio"><span class="logo__texto">ELITE</span></a>
        </div>
        <nav>
            <ul>
                <li class="#Nosotros"><a href="./../nosotros.php">Nosotros</a></li>
                <li class="#Productos"><a href="./../productos.php">Prendas</a></li>
                <li class="carrito"><a href="./../carrito.php" class="carrito">Carrito <i class="fa-solid fa-cart-shopping"></i>
                        <span class="carrito-contador">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    </a>
                </li>
                <li class="#administrador"><a href="./../administrador/administrador.php">Administradores</a></li>
            </ul>
        </nav>
        <div class="ingreso">
            <a href="./../pagina_perfil.php" class="registrar"><button>Mi Cuenta <i class="fa-solid fa-user"></i></button></a>
        </div>
    </header>

    <div class="admin_encabezado">
        <h1>Editar prenda: <?php echo htmlspecialchars($prenda['nombre']); ?></h1>
        <p><a href="./administrador.php#listado">&larr; Volver al panel</a></p>
    </div>

    <main class="admin_main">
        <?php include '../Backend/mensaje.php'; ?>

        <section class="panel editar__prenda">
            <form action="./editar_accion.php" method="post" enctype="multipart/form-data" class="form_prenda">
                <!-- El id de la prenda viaja oculto junto con el resto del formulario -->
                <input type="hidden" name="id_prenda" value="<?php echo $id_prenda; ?>">

                <div class="form_col">
                    <label for="nombre">Prenda</label>
                    <input type="text" name="nombre" id="nombre" maxlength="100" required value="<?php echo htmlspecialchars($prenda['nombre']); ?>">

                    <label for="descripcion">Descripcion</label>
                    <textarea name="descripcion" id="descripcion" maxlength="100"><?php echo htmlspecialchars($prenda['descripcion']); ?></textarea>

                    <label for="color">Color</label>
                    <input type="text" name="color" id="color" maxlength="30" required value="<?php echo htmlspecialchars($prenda['color']); ?>">

                    <label for="precio">Precio</label>
                    <input type="number" name="precio" id="precio" min="1" step="1" required value="<?php echo $prenda['precio']; ?>">
                </div>

                <div class="form_col">
                    <label for="productos_marca">Marca</label>
                    <select name="marca" id="productos_marca">
                        <?php foreach ($marcas as $m) { ?>
                            <option value="<?php echo $m['id_marca']; ?>" <?php if ($m['id_marca'] == $prenda['id_marca']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($m['nombre']); ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label for="productos_categoria">Categoria</label>
                    <select name="categoria" id="productos_categoria" required>
                        <?php foreach ($categorias as $c) { ?>
                            <option value="<?php echo $c['id_categoria']; ?>" <?php if ($c['id_categoria'] == $prenda['id_categoria']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($c['nombre']); ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label for="productos_proveedor">Proveedor</label>
                    <select name="proveedor" id="productos_proveedor">
                        <?php foreach ($proveedores as $p) { ?>
                            <option value="<?php echo $p['id_proveedor']; ?>" <?php if ($p['id_proveedor'] == $prenda['id_proveedor']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label for="imagen">Imagen (opcional: si no subís nada, queda la actual)</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*" class="nuevo_producto_imagen">
                </div>

                <!-- Stock por talle: si un talle queda en 0 deja de ofrecerse en el catálogo -->
                <fieldset class="talles_stock">
                    <legend>Stock por talle</legend>
                    <?php foreach ($talles_posibles as $talle) { ?>
                        <div class="talle_item">
                            <label for="stock_<?php echo $talle; ?>"><?php echo $talle; ?></label>
                            <input type="number" name="stock[<?php echo $talle; ?>]" id="stock_<?php echo $talle; ?>" min="0" value="<?php echo $stock_actual[$talle]; ?>">
                        </div>
                    <?php } ?>
                </fieldset>

                <div class="preview">
                    <p>Imagen actual:</p>
                    <img src="./../img/productos_img/<?php echo htmlspecialchars($prenda['imagen']); ?>" alt="Imagen actual de la prenda">
                </div>

                <input type="submit" class="btn_primario" value="Guardar cambios">
            </form>
        </section>
    </main>

    <script>
        // Vista previa de la imagen nueva antes de subirla
        document.querySelector(".nuevo_producto_imagen").addEventListener("change", function() {
            const archivo = this.files[0];
            if (archivo) {
                document.querySelector(".preview p").textContent = "Imagen nueva: " + archivo.name;
                document.querySelector(".preview img").src = URL.createObjectURL(archivo);
            }
        });
    </script>

</body>

</html>
