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
        $imgPath = isset($_POST['original_' . $imagen]) ? $directorioImagenes . $_POST['original_' . $imagen] : null;
        if ($_POST['remove_' . $imagen] == '1') {
            if ($imgPath && is_file($imgPath)) {
                unlink($imgPath);
            }
            $rutasImagenes[$imagen] = null;
        } else {
            if (isset($_FILES[$imagen]) && $_FILES[$imagen]['error'] == UPLOAD_ERR_OK) {
                if ($imgPath && is_file($imgPath)) {
                    unlink($imgPath);
                }

                $fileTmpPath = $_FILES[$imagen]['tmp_name'];
                $fileName = $_FILES[$imagen]['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $newFileName = md5(time() . $fileName . rand(0, 1000)) . '.webp';
                $dest_path = $directorioImagenes . $newFileName;

                try {
                    WebPConvert::convert($fileTmpPath, $dest_path, [
                        'quality' => 30
                    ]);
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
    }

    $img1 = $rutasImagenes['img1'] ?? null;
    $img2 = $rutasImagenes['img2'] ?? null;
    $img3 = $rutasImagenes['img3'] ?? null;

    if ($categoriaOriginal && $categoriaOriginal != $categoria_idcategoria) {
        $nombreCategoriaOriginal = obtenerNombreCategoriaDesdeBD($categoriaOriginal);
        $directorioOriginal = $directorioBase . $nombreCategoriaOriginal . '/';

        foreach ($imagenes as $imagen) {
            if (isset($_POST['original_' . $imagen]) && $_POST['original_' . $imagen]) {
                $rutaOriginal = $directorioOriginal . $_POST['original_' . $imagen];
                $rutaNueva = $directorioImagenes . $_POST['original_' . $imagen];
                if (is_file($rutaOriginal)) {
                    rename($rutaOriginal, $rutaNueva);
                }
            }
        }
    }

    if ($idproducto) {
        $resultado = $modelo->actualizarProducto($idproducto, $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3);
    } else {
        for ($i = 0; $i < $cantidad; $i++) {
            $img1_final = $img1;
            $img2_final = $img2;
            $img3_final = $img3;
    
            // Solo generar nuevas copias de las imágenes para productos adicionales (i > 0)
            if ($i > 0) {
                $img1_final = $img1 ? md5(time() . $img1 . $i) . '.webp' : null;
                $img2_final = $img2 ? md5(time() . $img2 . $i) . '.webp' : null;
                $img3_final = $img3 ? md5(time() . $img3 . $i) . '.webp' : null;
    
                // Verifica si la imagen original existe antes de copiar
                if ($img1 && file_exists($directorioImagenes . $img1)) {
                    copy($directorioImagenes . $img1, $directorioImagenes . $img1_final);
                }
                if ($img2 && file_exists($directorioImagenes . $img2)) {
                    copy($directorioImagenes . $img2, $directorioImagenes . $img2_final);
                }
                if ($img3 && file_exists($directorioImagenes . $img3)) {
                    copy($directorioImagenes . $img3, $directorioImagenes . $img3_final);
                }
            }
    
            // Agregar el producto con las imágenes correspondientes
            $resultado = $modelo->agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1_final, $img2_final, $img3_final, 1);
        }
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

function obtenerProductoPorId($idProducto) {
    global $conn;

    $sql = "SELECT p.*, c.nombre AS categoria
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            WHERE p.idproducto = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idProducto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();

    $stmt->close();

    return $producto;
}
?>
