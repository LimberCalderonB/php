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
        // Obtener los detalles del producto que se quiere eliminar, primero revisando los productos con estado 'disponible'
        $sql_select_producto = "SELECT p.nombre, p.precio, p.descuento, p.talla, a.categoria_idcategoria, a.estado 
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

        // Intentar eliminar productos con estado 'disponible'
        $sql_match_productos_disponibles = "SELECT p.idproducto 
                                            FROM producto p
                                            INNER JOIN almacen a ON p.idproducto = a.producto_idproducto
                                            WHERE p.nombre = ? AND p.precio = ? AND p.descuento = ? AND p.talla = ? 
                                            AND a.categoria_idcategoria = ? AND a.estado = 'disponible'
                                            ORDER BY p.idproducto ASC
                                            LIMIT ?";
        $stmt_match_productos_disponibles = $conn->prepare($sql_match_productos_disponibles);
        $stmt_match_productos_disponibles->bind_param("sddssi", 
                                                      $producto['nombre'], 
                                                      $producto['precio'], 
                                                      $producto['descuento'], 
                                                      $producto['talla'], 
                                                      $producto['categoria_idcategoria'], 
                                                      $cantidad);
        $stmt_match_productos_disponibles->execute();
        $resultado_match_disponibles = $stmt_match_productos_disponibles->get_result();

        if ($resultado_match_disponibles->num_rows == $cantidad) {
            $idsAEliminar = [];

            while ($fila = $resultado_match_disponibles->fetch_assoc()) {
                $idsAEliminar[] = $fila['idproducto'];
            }

            // Eliminar los productos con estado 'disponible'
            foreach ($idsAEliminar as $id) {
                eliminarProducto($id, $producto['categoria_idcategoria'], $nombreCategoria, $conn);
            }
        } else {
            // Si no hay suficientes productos con estado 'disponible', intentar actualizar productos 'agotado' a 'vendido'
            $sql_match_productos_agotados = "SELECT p.idproducto, a.cantidad 
                                             FROM producto p
                                             INNER JOIN almacen a ON p.idproducto = a.producto_idproducto
                                             WHERE p.nombre = ? AND p.precio = ? AND p.descuento = ? AND p.talla = ? 
                                             AND a.categoria_idcategoria = ? AND a.estado = 'agotado'
                                             ORDER BY p.idproducto ASC";
            $stmt_match_productos_agotados = $conn->prepare($sql_match_productos_agotados);
            $stmt_match_productos_agotados->bind_param("sddsi", 
                                                       $producto['nombre'], 
                                                       $producto['precio'], 
                                                       $producto['descuento'], 
                                                       $producto['talla'], 
                                                       $producto['categoria_idcategoria']);
            $stmt_match_productos_agotados->execute();
            $resultado_match_agotados = $stmt_match_productos_agotados->get_result();

            if ($resultado_match_agotados->num_rows > 0) {
                $idsAActualizar = [];

                // Cambiar el estado de los productos 'agotado' a 'vendido' solo si su cantidad es 0
                while ($fila = $resultado_match_agotados->fetch_assoc()) {
                    if ($fila['cantidad'] == 0) {
                        $idsAActualizar[] = $fila['idproducto'];
                    } else {
                        throw new Exception("Error: No se puede cambiar el estado de un producto 'agotado' si su cantidad no es 0.");
                    }
                }

                foreach ($idsAActualizar as $id) {
                    actualizarEstadoProducto($id, 'vendido', $conn, $nombreCategoria);
                }
            } else {
                throw new Exception("No se encontraron productos disponibles o agotados para eliminar o actualizar.");
            }
        }

        // Confirmar la transacción
        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "Error al eliminar o actualizar el producto: " . $e->getMessage();
    }

    // Cerrar la conexión
    $conn->close();
} else {
    echo "ID de producto o cantidad no proporcionados";
}

// Función para eliminar el producto
function eliminarProducto($idproducto, $categoria_idcategoria, $nombreCategoria, $conn) {
    // Eliminar el producto de la tabla `almacen`
    $sql_delete_almacen = "DELETE FROM almacen WHERE producto_idproducto = ? AND categoria_idcategoria = ?";
    $stmt_delete_almacen = $conn->prepare($sql_delete_almacen);
    $stmt_delete_almacen->bind_param("ii", $idproducto, $categoria_idcategoria);
    $stmt_delete_almacen->execute();
    
    // Eliminar el producto de la tabla `producto`
    $sql_delete_producto = "DELETE FROM producto WHERE idproducto = ?";
    $stmt_delete_producto = $conn->prepare($sql_delete_producto);
    $stmt_delete_producto->bind_param("i", $idproducto);
    $stmt_delete_producto->execute();
}

// Función para actualizar el estado del producto y eliminar imagen solo si es 'agotado' -> 'vendido'
function actualizarEstadoProducto($idproducto, $nuevoEstado, $conn, $nombreCategoria) {
    // Verificar si el estado actual es 'agotado'
    $sql_verificar_estado = "SELECT estado FROM almacen WHERE producto_idproducto = ?";
    $stmt_verificar_estado = $conn->prepare($sql_verificar_estado);
    $stmt_verificar_estado->bind_param("i", $idproducto);
    $stmt_verificar_estado->execute();
    $resultado_estado = $stmt_verificar_estado->get_result();

    if ($resultado_estado->num_rows > 0) {
        $estado_actual = $resultado_estado->fetch_assoc()['estado'];

        // Si el estado actual es 'agotado' y el nuevo estado es 'vendido', eliminar la imagen
        if ($estado_actual === 'agotado' && $nuevoEstado === 'vendido') {
            // Eliminar imágenes asociadas al producto
            $sql_select_imagenes = "SELECT img1, img2, img3 FROM producto WHERE idproducto = ?";
            $stmt_select_imagenes = $conn->prepare($sql_select_imagenes);
            $stmt_select_imagenes->bind_param("i", $idproducto);
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
        }
    }

    // Actualizar el estado del producto en la tabla `almacen`
    $sql_update_estado = "UPDATE almacen SET estado = ? WHERE producto_idproducto = ?";
    $stmt_update_estado = $conn->prepare($sql_update_estado);
    $stmt_update_estado->bind_param("si", $nuevoEstado, $idproducto);
    $stmt_update_estado->execute();
}

?>
