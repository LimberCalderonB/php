<?php
include_once "../../conexion.php";
session_start();

if (isset($_POST['idproducto']) && isset($_POST['cantidad'])) {
    $idproducto = $_POST['idproducto'];
    $cantidad = (int)$_POST['cantidad'];

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        $sql_select_producto = "SELECT p.nombre, p.precio, p.descuento, p.talla, p.img1, p.img2, p.img3, a.categoria_idcategoria, c.nombre AS categoria_nombre
                                FROM producto p
                                INNER JOIN almacen a ON p.idproducto = a.producto_idproducto
                                INNER JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
                                WHERE p.idproducto = ?";
        $stmt_select_producto = $conn->prepare($sql_select_producto);
        $stmt_select_producto->bind_param("i", $idproducto);
        $stmt_select_producto->execute();
        $resultado_producto = $stmt_select_producto->get_result();

        if ($resultado_producto->num_rows > 0) {
            $producto = $resultado_producto->fetch_assoc();

            $nombre = isset($producto['nombre']) ? $producto['nombre'] : '';
            $precio = isset($producto['precio']) ? $producto['precio'] : 0;
            $descuento = isset($producto['descuento']) ? $producto['descuento'] : 0;
            $talla = isset($producto['talla']) ? $producto['talla'] : '';
            $categoria_idcategoria = isset($producto['categoria_idcategoria']) ? $producto['categoria_idcategoria'] : 0;
            $categoria_nombre = isset($producto['categoria_nombre']) ? htmlspecialchars($producto['categoria_nombre']) : 'default';
            $img1 = isset($producto['img1']) ? $producto['img1'] : '';
            $img2 = isset($producto['img2']) ? $producto['img2'] : '';
            $img3 = isset($producto['img3']) ? $producto['img3'] : '';

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

            if ($resultado_match->num_rows < $cantidad) {
                throw new Exception("No se encontraron suficientes productos para realizar la venta.");
            }

            if (!isset($_SESSION['productos_seleccionados'])) {
                $_SESSION['productos_seleccionados'] = [];
            }

            // Actualizar el estado de los productos a 'casi_vendido'
            $productos_actualizar = [];
            while ($row = $resultado_match->fetch_assoc()) {
                $productos_actualizar[] = $row['idproducto'];
                $directorioImagenes = 'img/categorias/' . $categoria_nombre . '/';
                
                $ruta_imagen1 = !empty($img1) ? $directorioImagenes . $img1 : '';
                $ruta_imagen2 = !empty($img2) ? $directorioImagenes . $img2 : '';
                $ruta_imagen3 = !empty($img3) ? $directorioImagenes . $img3 : '';

                if (!isset($_SESSION['productos_seleccionados'][$row['idproducto']])) {
                    $row['categoria_nombre'] = $categoria_nombre;
                    $row['talla'] = $talla;
                    $row['descuento'] = $descuento;
                    $row['precio'] = $precio;
                    $row['ruta_imagen1'] = $ruta_imagen1;
                    $row['ruta_imagen2'] = $ruta_imagen2;
                    $row['ruta_imagen3'] = $ruta_imagen3;
                    $_SESSION['productos_seleccionados'][$row['idproducto']] = $row;
                }
            }

            // Actualizar el estado de los productos en la base de datos
            $sql_update_estado = "UPDATE almacen SET estado = 'casi_vendido' WHERE producto_idproducto = ?";
            $stmt_update_estado = $conn->prepare($sql_update_estado);
            foreach ($productos_actualizar as $idproducto_actualizar) {
                $stmt_update_estado->bind_param("i", $idproducto_actualizar);
                if (!$stmt_update_estado->execute()) {
                    throw new Exception("Error al actualizar el estado del producto: " . $stmt_update_estado->error);
                }
            }

            $conn->commit();
            header("Location: pagos.php");
            exit();
        } else {
            throw new Exception("Producto no encontrado.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
    $conn->close();
} else {
    echo "Parámetros insuficientes.";
}
?>
