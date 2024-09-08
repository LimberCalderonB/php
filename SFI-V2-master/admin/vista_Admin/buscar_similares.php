<?php
// Incluir la conexión a la base de datos y sesión
include_once "../../conexion.php";
session_start();

// Verificar que se hayan enviado los parámetros necesarios
if (isset($_POST['idproducto']) && isset($_POST['cantidad'])) {
    $idproducto = $_POST['idproducto'];
    $cantidad = (int)$_POST['cantidad'];

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        // Obtener los detalles del producto seleccionado
        $sql_select_producto = "SELECT p.nombre, p.precio, p.descuento, p.talla, p.img1, p.img2, p.img3, a.categoria_idcategoria, c.nombre AS categoria_nombre
                                FROM producto p
                                INNER JOIN almacen a ON p.idproducto = a.producto_idproducto
                                INNER JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
                                WHERE p.idproducto = ?";
        $stmt_select_producto = $conn->prepare($sql_select_producto);
        $stmt_select_producto->bind_param("i", $idproducto);
        $stmt_select_producto->execute();
        $resultado_producto = $stmt_select_producto->get_result();

        // Verificar si la consulta devolvió un resultado
        if ($resultado_producto->num_rows > 0) {
            $producto = $resultado_producto->fetch_assoc();

            // Verificar que los campos esperados existan en el array
            $nombre = isset($producto['nombre']) ? $producto['nombre'] : '';
            $precio = isset($producto['precio']) ? $producto['precio'] : 0;
            $descuento = isset($producto['descuento']) ? $producto['descuento'] : 0;
            $talla = isset($producto['talla']) ? $producto['talla'] : '';
            $categoria_idcategoria = isset($producto['categoria_idcategoria']) ? $producto['categoria_idcategoria'] : 0;
            $categoria_nombre = isset($producto['categoria_nombre']) ? htmlspecialchars($producto['categoria_nombre']) : 'default';
            $img1 = isset($producto['img1']) ? $producto['img1'] : '';
            $img2 = isset($producto['img2']) ? $producto['img2'] : '';
            $img3 = isset($producto['img3']) ? $producto['img3'] : '';

            // Construir el directorio de imágenes basado en el nombre de la categoría
            $directorioImagenes = 'img/categorias/' . $categoria_nombre . '/';
        } else {
            throw new Exception("Producto no encontrado.");
        }

        // Buscar productos que coincidan con los detalles
        $sql_match_productos = "SELECT p.idproducto 
                                FROM producto p
                                INNER JOIN almacen a ON p.idproducto = a.producto_idproducto
                                WHERE p.nombre = ? AND p.precio = ? AND p.descuento = ? AND p.talla = ? AND a.categoria_idcategoria = ? 
                                ORDER BY p.idproducto ASC
                                LIMIT ?";
        $stmt_match_productos = $conn->prepare($sql_match_productos);
        $stmt_match_productos->bind_param("sddssi", 
                                          $nombre, 
                                          $precio, 
                                          $descuento, 
                                          $talla, 
                                          $categoria_idcategoria, 
                                          $cantidad);
        $stmt_match_productos->execute();
        $resultado_match = $stmt_match_productos->get_result();

        // Verificar si se encontraron suficientes productos
        if ($resultado_match->num_rows < $cantidad) {
            throw new Exception("No se encontraron suficientes productos para realizar la venta.");
        }

        // Almacenar los productos encontrados en la sesión
        $_SESSION['productos_seleccionados'] = [];

        while ($row = $resultado_match->fetch_assoc()) {
            // Construir el directorio de imágenes basado en el nombre de la categoría
            $directorioImagenes = 'img/categorias/' . $categoria_nombre . '/';
            
            // Construir las rutas completas de las imágenes para cada producto
            $ruta_imagen1 = !empty($img1) ? $directorioImagenes . $img1 : ''; // Ruta completa para imagen 1
            $ruta_imagen2 = !empty($img2) ? $directorioImagenes . $img2 : ''; // Ruta completa para imagen 2
            $ruta_imagen3 = !empty($img3) ? $directorioImagenes . $img3 : ''; // Ruta completa para imagen 3

            // Agregar los detalles del producto a la sesión
            $row['categoria_nombre'] = $categoria_nombre; // Agregar nombre de categoría
            $row['talla'] = $talla; // Agregar talla
            $row['descuento'] = $descuento; // Agregar descuento
            $row['precio'] = $precio; // Agregar precio
            $row['ruta_imagen1'] = $ruta_imagen1; // Agregar ruta completa de imagen 1
            $row['ruta_imagen2'] = $ruta_imagen2; // Agregar ruta completa de imagen 2
            $row['ruta_imagen3'] = $ruta_imagen3; // Agregar ruta completa de imagen 3
            $_SESSION['productos_seleccionados'][$row['idproducto']] = $row;
        }

        // Confirmar la transacción
        $conn->commit();
        
        // Redirigir a pagos.php con los productos seleccionados
        header("Location: pagos.php");
        exit();

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Cerrar la conexión
    $conn->close();
} else {
    echo "Parámetros insuficientes.";
}
?>
