<?php
include_once '../modelo_admin/mod_producto.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $idproducto = $_POST['idproducto'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $descuento = $_POST['descuento'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $talla = $_POST['talla'] ?? '';
    $categoria_idcategoria = $_POST['categoria_idcategoria'] ?? null;

    // Validación de datos
    if (empty($nombre) || empty($precio) || empty($descripcion) || empty($talla) || empty($categoria_idcategoria)) {
        $_SESSION['registro'] = 'Todos los campos son obligatorios.';
        header("Location: ../vista_Admin/productos.php?mensaje=Error: Campos incompletos");
        exit;
    }

    // Calcular el precio con descuento
    $precioConDescuento = $precio - ($precio * ($descuento / 100));

    // Manejar la subida de imágenes
    $imagenes = ['img1', 'img2', 'img3'];
    $rutasImagenes = [];
    $directorioImagenes = '../vista_Admin/img/vestidos/';

    foreach ($imagenes as $imagen) {
        $imgPath = isset($_POST['original_' . $imagen]) ? $directorioImagenes . $_POST['original_' . $imagen] : null;
        if ($_POST['remove_' . $imagen] == '1') {
            if ($imgPath && is_file($imgPath)) {
                unlink($imgPath); // Eliminar la imagen existente del sistema de archivos
            }
            $rutasImagenes[$imagen] = null; // Establecer la ruta de la imagen como null
        } else {
            if (isset($_FILES[$imagen]) && $_FILES[$imagen]['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES[$imagen]['tmp_name'];
                $fileName = $_FILES[$imagen]['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $directorioImagenes . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $rutasImagenes[$imagen] = $newFileName;
                } else {
                    $_SESSION['registro'] = 'Error al subir la imagen.';
                    header("Location: ../vista_Admin/productos.php?mensaje=Error al subir la imagen");
                    exit;
                }
            } else {
                $rutasImagenes[$imagen] = $_POST['original_' . $imagen] ?? null; // Mantener la ruta original si no se sube una nueva imagen
            }
        }
    }

    // Obtener las rutas de las imágenes
    $img1 = $rutasImagenes['img1'] ?? null;
    $img2 = $rutasImagenes['img2'] ?? null;
    $img3 = $rutasImagenes['img3'] ?? null;

    $modelo = new ModeloProducto();

    if ($idproducto) {
        // Actualizar el producto existente
        $producto = $modelo->obtenerProductoPorId($idproducto);
        if ($producto) {
            $resultado = $modelo->actualizarProducto($idproducto, $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3);
        } else {
            $_SESSION['registro'] = 'Producto no encontrado.';
            header("Location: ../vista_Admin/productos.php?mensaje=Producto no encontrado");
            exit;
        }
    } else {
        // Agregar un nuevo producto
        $resultado = $modelo->agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3);
    }

    if ($resultado) {
        $_SESSION['registro'] = 'Producto guardado correctamente.';
        header("Location: ../vista_Admin/productos.php?mensaje=Producto guardado exitosamente");
    } else {
        $_SESSION['registro'] = 'Hubo un problema al guardar el producto.';
        header("Location: ../vista_Admin/productos.php?mensaje=Error al guardar producto");
    }
}
?>



