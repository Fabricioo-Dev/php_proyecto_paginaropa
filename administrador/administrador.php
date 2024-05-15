<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./../CSS/administrador.css">
    <title>Administradores</title>
</head>

<body>
    <?php session_start(); ?>
    <h1>Hola Administrador: <?php echo "{$_SESSION['nombre']} {$_SESSION['apellido']}"; ?></h1>
    <hr>
    <h3 class="administrador_titulo">Administrar productos</h3>
    <div class="productos">
        <h4>Añadir Producto</h4>
        <form action="./nuevo_producto.php" method="post" enctype="multipart/form-data">
            <label for="nombre">Prenda</label>
            <input type="text" name="nombre" placeholder="Ingresar Nombre"></br>
            <label for="descripcion">Descripcion</label></br>
            <textarea name="descripcion"></textarea></br>
            <label for="talla">Talla</label>
            <select name="talla">
                <option value="XS">XS</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            </select></br>
            <label for="color">Color</label>
            <input type="text" name="color"></br>
            <label for="precio">Precio</label>
            <input type="text" name="precio" id="nuevo_producto_precio"></br>
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" accept="image/*" class="nuevo_producto_imagen" placeholder="Ingresar el archivo de imagen"></br>

            <div class="preview">
                <p>No se detectan archivos en el preview</p>
            </div>
            <input type="submit" value="Subir nuevo producto">
        </form>
    </div>


</body>


<script>
    function setInputFilter(textbox, inputFilter, errMsg) {
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(function(event) {
            textbox.addEventListener(event, function(e) {
                if (inputFilter(this.value)) {
                    // Accepted value.
                    if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                        this.classList.remove("input-error");
                        this.setCustomValidity("");
                    }

                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    // Rejected value: restore the previous one.
                    this.classList.add("input-error");
                    this.setCustomValidity(errMsg);
                    this.reportValidity();
                    this.value = this.oldValue;
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                } else {
                    // Rejected value: nothing to restore.
                    this.value = "";
                }
            });
        });
    }


    function updateImageDisplay() {
        while (preview.firstChild) {
            preview.removeChild(preview.firstChild);
        }

        const curFiles = input.files;
        if (curFiles.length === 0) {
            const para = document.createElement("p");
            para.textContent = "No hay imagenes que hayan sido agregadas";
            preview.appendChild(para);
        } else {
            const list = document.createElement("ol");
            preview.appendChild(list);

            for (const file of curFiles) {
                const listItem = document.createElement("li");
                const para = document.createElement("p");
                if (file) {
                    para.textContent = `Nombre archivo ${file.name}, Tamaño archivo ${file.size}.`;
                    const image = document.createElement("img");
                    image.src = URL.createObjectURL(file);
                    image.alt = image.title = file.name;

                    listItem.appendChild(image);
                    listItem.appendChild(para);
                } else {
                    para.textContent = `Nombre archivo ${file.name}: No es un tipo válido de archivo. Actualiza tu seleccion de archivo.`;
                    listItem.appendChild(para);
                }

                list.appendChild(listItem);
            }
        }
    }




    setInputFilter(document.getElementById("nuevo_producto_precio"), function(value) {
        return /^\d*\.?\d*$/.test(value); // Permite solo valores digitos (numericos)
    }, "Solo valores numericos se pueden añadir");



    const input = document.querySelector(".nuevo_producto_imagen");
    const preview = document.querySelector(".preview");

    input.addEventListener("change", updateImageDisplay);
</script>

</html>