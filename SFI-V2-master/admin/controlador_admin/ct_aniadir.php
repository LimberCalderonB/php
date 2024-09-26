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

    // No se necesitan duplicar las imágenes, solo se referencian las existentes
    for ($i = 0; $i < $cantidad; $i++) {
        // Se mantienen las mismas imágenes sin generar nombres únicos
        $img1_final = $img1;
        $img2_final = $img2;
        $img3_final = $img3;

        // Insertar el nuevo producto con las imágenes referenciadas
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
