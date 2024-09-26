<?php
require '../../../vendor/autoload.php';
use WebPConvert\WebPConvert;
include_once "../../conexion.php";
include_once '../modelo_admin/mod_aniadir.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idproducto = $_POST['idproducto'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 1; // Cantidad de productos a duplicar

    // Verificar que el producto exista
    $modelo = new ModeloProducto();
    $productoExistente = $modelo->obtenerProductoPorId($idproducto);

    if (!$productoExistente) {
        echo "Error: Producto no encontrado.";
        exit;
    }

    // Obtener datos del producto existente
    $nombre = $productoExistente['nombre'];
    $precio = $productoExistente['precio'];
    $descuento = $productoExistente['descuento'];
    $descripcion = $productoExistente['descripcion'];
    $talla = $productoExistente['talla'];
    $categoria_idcategoria = $productoExistente['categoria_idcategoria'];
    $img1 = $productoExistente['img1'];
    $img2 = $productoExistente['img2'];
    $img3 = $productoExistente['img3'];

    // Obtener nombre de la categoría
    $nombreCategoria = obtenerNombreCategoriaDesdeBD($categoria_idcategoria);
    $directorioBase = '../vista_Admin/img/categorias/';
    $directorioImagenes = $directorioBase . $nombreCategoria . '/';

    // Crear las copias del producto
    for ($i = 0; $i < $cantidad; $i++) {
        // Generar nombres únicos para las imágenes en cada iteración
        $img1_final = $img1 ? md5(uniqid($img1 . $i, true)) . '.webp' : null;
        $img2_final = $img2 ? md5(uniqid($img2 . $i, true)) . '.webp' : null;
        $img3_final = $img3 ? md5(uniqid($img3 . $i, true)) . '.webp' : null;

        // Rutas de las imágenes
        $ruta_original_img1 = $directorioImagenes . $img1;
        $ruta_destino_img1 = $directorioImagenes . $img1_final;
        $ruta_original_img2 = $directorioImagenes . $img2;
        $ruta_destino_img2 = $directorioImagenes . $img2_final;
        $ruta_original_img3 = $directorioImagenes . $img3;
        $ruta_destino_img3 = $directorioImagenes . $img3_final;

        // Verificar la existencia de las imágenes y duplicarlas
        if ($img1 && file_exists($ruta_original_img1)) {
            copy($ruta_original_img1, $ruta_destino_img1);
        }
        if ($img2 && file_exists($ruta_original_img2)) {
            copy($ruta_original_img2, $ruta_destino_img2);
        }
        if ($img3 && file_exists($ruta_original_img3)) {
            copy($ruta_original_img3, $ruta_destino_img3);
        }

        // Insertar el nuevo producto
        $precioConDescuento = $precio - ($precio * ($descuento / 100));
        $resultado = $modelo->agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1_final, $img2_final, $img3_final, 1);

        if (!$resultado['success']) {
            echo "Error al añadir el producto: " . $resultado['error'] . "<br>";
        } else {
            echo "Producto añadido exitosamente.<br>";
        }
    }

    // Cambiar el estado de los productos agotados a vendido si se añadieron productos iguales
    $resultadoCambioEstado = $modelo->cambiarEstadoAgotadoAVendido($nombre, $precio, $talla, $descuento, $categoria_idcategoria);
    if (!$resultadoCambioEstado['success']) {
        echo "Error al cambiar el estado de productos agotados a vendidos: " . $resultadoCambioEstado['error'];
    } else {
        echo "Productos agotados cambiados a vendidos.<br>";
    }

    $conn->close();
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
