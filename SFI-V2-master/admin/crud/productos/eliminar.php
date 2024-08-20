<?php

if (isset($_POST['idproducto']) && isset($_POST['cantidad'])) {
    $idproducto = $_POST['idproducto'];
    $cantidad = (int) $_POST['cantidad'];

    include_once "../../../conexion.php";
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        // Obtener los detalles del producto que se quiere eliminar
        $sql_select_producto = "SELECT p.nombre, p.precio, p.descuento, p.talla, a.categoria_idcategoria 
                                FROM producto p
                                INNER JOIN almacen a ON p.idproducto = a.producto_idproducto
                                WHERE p.idproducto = ?";
        $stmt_select_producto = $conn->prepare($sql_select_producto);
        $stmt_select_producto->bind_param("i", $idproducto);
        $stmt_select_producto->execute();
        $resultado_producto = $stmt_select_producto->get_result();

        if ($resultado_producto->num_rows > 0) {
            $producto = $resultado_producto->fetch_assoc();
        } else {
            throw new Exception("Producto no encontrado.");
        }

        // Obtener el nombre de la categoría
        $sql_nombre_categoria = "SELECT nombre FROM categoria WHERE idcategoria = ?";
        $stmt_nombre_categoria = $conn->prepare($sql_nombre_categoria);
        $stmt_nombre_categoria->bind_param("i", $producto['categoria_idcategoria']);
        $stmt_nombre_categoria->execute();
        $resultado_nombre_categoria = $stmt_nombre_categoria->get_result();

        if ($resultado_nombre_categoria->num_rows > 0) {
            $nombreCategoria = $resultado_nombre_categoria->fetch_assoc()['nombre'];
        } else {
            throw new Exception("No se encontró el nombre de la categoría.");
        }

        // Buscar productos que coincidan con los detalles en la tabla `producto` y `almacen`
        $sql_match_productos = "SELECT p.idproducto 
                                FROM producto p
                                INNER JOIN almacen a ON p.idproducto = a.producto_idproducto
                                WHERE p.nombre = ? AND p.precio = ? AND p.descuento = ? AND p.talla = ? AND a.categoria_idcategoria = ? 
                                ORDER BY p.idproducto ASC
                                LIMIT ?";
        $stmt_match_productos = $conn->prepare($sql_match_productos);
        $stmt_match_productos->bind_param("sddssi", 
                                          $producto['nombre'], 
                                          $producto['precio'], 
                                          $producto['descuento'], 
                                          $producto['talla'], 
                                          $producto['categoria_idcategoria'], 
                                          $cantidad);
        $stmt_match_productos->execute();
        $resultado_match = $stmt_match_productos->get_result();

        if ($resultado_match->num_rows < $cantidad) {
            throw new Exception("No se encontraron suficientes productos para eliminar.");
        }

        // Array para almacenar los IDs de los productos a eliminar
        $idsAEliminar = [];

        while ($fila = $resultado_match->fetch_assoc()) {
            $idsAEliminar[] = $fila['idproducto'];
        }

        // Eliminar cada producto encontrado
        foreach ($idsAEliminar as $id) {
            // Eliminar el producto de la tabla `almacen`
            $sql_delete_almacen = "DELETE FROM almacen WHERE producto_idproducto = ? AND categoria_idcategoria = ?";
            $stmt_delete_almacen = $conn->prepare($sql_delete_almacen);
            $stmt_delete_almacen->bind_param("ii", $id, $producto['categoria_idcategoria']);
            $stmt_delete_almacen->execute();
            

            // Eliminar imágenes asociadas a este producto
            $sql_select_imagenes = "SELECT img1, img2, img3 FROM producto WHERE idproducto = ?";
            $stmt_select_imagenes = $conn->prepare($sql_select_imagenes);
            $stmt_select_imagenes->bind_param("i", $id);
            $stmt_select_imagenes->execute();
            $resultado_imagenes = $stmt_select_imagenes->get_result();

            if ($resultado_imagenes->num_rows > 0) {
                $imagenes = $resultado_imagenes->fetch_assoc();
                $directorioImagenes = "../../vista_Admin/img/categorias/" . $nombreCategoria . "/";

                foreach ($imagenes as $img) {
                    if (!empty($img)) {
                        $rutaCompleta = $directorioImagenes . $img;
                        if (file_exists($rutaCompleta)) {
                            unlink($rutaCompleta);
                        }
                    }
                }
            }

            // Eliminar el producto de la tabla `producto`
            $sql_delete_producto = "DELETE FROM producto WHERE idproducto = ?";
            $stmt_delete_producto = $conn->prepare($sql_delete_producto);
            $stmt_delete_producto->bind_param("i", $id);
            $stmt_delete_producto->execute();
        }

        // Confirmar la transacción
        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "Error al eliminar el producto: " . $e->getMessage();
    }

    // Cerrar la conexión
    $conn->close();
} else {
    echo "ID de producto o cantidad no proporcionados";
}
?>
