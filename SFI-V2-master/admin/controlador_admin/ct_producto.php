<?php
require '../../../vendor/autoload.php';

use WebPConvert\WebPConvert;
include_once "../../conexion.php";
include_once '../modelo_admin/mod_producto.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idproducto = $_POST['idproducto'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $descuento = $_POST['descuento'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $talla = $_POST['talla'] ?? '';
    $categoria_idcategoria = $_POST['categoria_idcategoria'] ?? null;
    $cantidad = $_POST['cantidad'] ?? 1;

    if (empty($nombre) || empty($precio) || empty($descripcion) || empty($talla) || empty($categoria_idcategoria)) {
        $_SESSION['registro'] = 'Todos los campos son obligatorios.';
        header("Location: ../vista_Admin/productos.php?mensaje=Error: Campos incompletos");
        exit;
    }

    $precioConDescuento = $precio - ($precio * ($descuento / 100));

    $modelo = new ModeloProducto();

    $categoriaOriginal = null;
    if ($idproducto) {
        $productoExistente = $modelo->obtenerProductoPorId($idproducto);
        if ($productoExistente) {
            $categoriaOriginal = $productoExistente['categoria_idcategoria'];
        } else {
            $_SESSION['registro'] = 'Producto no encontrado.';
            header("Location: ../vista_Admin/productos.php?mensaje=Producto no encontrado");
            exit;
        }
    }

    $nombreCategoria = obtenerNombreCategoriaDesdeBD($categoria_idcategoria);
    $directorioBase = '../vista_Admin/img/categorias/';
    $directorioImagenes = $directorioBase . $nombreCategoria . '/';

    if (!is_dir($directorioImagenes)) {
        mkdir($directorioImagenes, 0777, true);
    }

    $imagenes = ['img1', 'img2', 'img3'];
    $rutasImagenes = [];

    foreach ($imagenes as $imagen) {
        if (isset($_FILES[$imagen]) && $_FILES[$imagen]['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$imagen]['tmp_name'];
            $fileName = $_FILES[$imagen]['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName . rand(0, 1000)) . '.webp';
            $dest_path = $directorioImagenes . $newFileName;

            try {
                WebPConvert::convert($fileTmpPath, $dest_path, ['quality' => 30]);
                $rutasImagenes[$imagen] = $newFileName;
            } catch (Exception $e) {
                $_SESSION['registro'] = 'Error al convertir la imagen a WebP: ' . $e->getMessage();
                header("Location: ../vista_Admin/productos.php?mensaje=Error al convertir la imagen a WebP");
                exit;
            }
        } else {
            $rutasImagenes[$imagen] = $_POST['original_' . $imagen] ?? null;
        }
    }

    $img1 = $rutasImagenes['img1'] ?? null;
    $img2 = $rutasImagenes['img2'] ?? null;
    $img3 = $rutasImagenes['img3'] ?? null;

    // Actualización o creación del producto
    if ($idproducto) {
        $resultado = $modelo->actualizarProducto($idproducto, $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3);
    } else {
        // Crear una sola vez el producto con la imagen compartida
        $resultado = $modelo->agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3, $cantidad);
    }

    if ($resultado) {
        $_SESSION['registro'] = 'Producto guardado correctamente.';
        header("Location: ../vista_Admin/productos.php?mensaje=Producto guardado exitosamente");
    } else {
        $_SESSION['registro'] = 'Hubo un problema al guardar el producto.';
        header("Location: ../vista_Admin/productos.php?mensaje=Error al guardar producto");
    }
}

function obtenerNombreCategoriaDesdeBD($idCategoria) {
    global $conn;

    $sql = "SELECT nombre FROM categoria WHERE idcategoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idCategoria);
    $stmt->execute();
    $stmt->bind_result($nombreCategoria);
    $stmt->fetch();
    $stmt->close();

    return $nombreCategoria;
}
?>
