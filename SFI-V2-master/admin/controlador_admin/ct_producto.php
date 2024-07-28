<?php
include_once '../modelo_admin/mod_producto.php';

// Verifica si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoge los datos del formulario con valores predeterminados si no están presentes
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $descuento = $_POST['descuento'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $talla = $_POST['talla'] ?? '';
    $categoria_idcategoria = $_POST['categoria_idcategoria'] ?? null;
    $estado = $_POST['estado'] ?? '';

    // Calcula el precio con descuento
    $precioConDescuento = $precio - ($precio * ($descuento / 100));

    // Manejo de los archivos de imagen
    $imagenes = ['img1', 'img2', 'img3'];
    $rutasImagenes = [];

    foreach ($imagenes as $imagen) {
        if (isset($_FILES[$imagen]) && $_FILES[$imagen]['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$imagen]['tmp_name'];
            $fileName = $_FILES[$imagen]['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '../vista_Admin/img/vestidos/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $rutasImagenes[$imagen] = $newFileName;
            } else {
                $rutasImagenes[$imagen] = null; // En caso de error al subir la foto
            }
        } else {
            $rutasImagenes[$imagen] = null; // No se ha subido ninguna foto
        }
    }

    // Almacenar las rutas de las imágenes en variables separadas
    $img1 = $rutasImagenes['img1'] ?? null;
    $img2 = $rutasImagenes['img2'] ?? null;
    $img3 = $rutasImagenes['img3'] ?? null;

    // Crear instancia del modelo
    $modelo = new ModeloProducto();
    $resultado = $modelo->agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $estado, $img1, $img2, $img3);

    if ($resultado) {
        $_SESSION['registro'] = 'Producto agregado correctamente.';
        header("Location: ../vista_Admin/productos.php?mensaje=Producto agregado exitosamente");
    } else {
        $_SESSION['registro'] = 'Hubo un problema al agregar el producto.';
        header("Location: ../vista_Admin/productos.php?mensaje=Error al agregar producto");
    }
}
?>
