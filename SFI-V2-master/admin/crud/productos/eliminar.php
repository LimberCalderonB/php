<?php

if (isset($_POST['idproducto'])) {

    $idproducto = $_POST['idproducto'];
    include_once "../../../conexion.php";
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        // Primero, obtener la categoría del producto
        $sql_select_categoria = "SELECT categoria_idcategoria FROM almacen WHERE producto_idproducto = ?";
        $stmt_select_categoria = $conn->prepare($sql_select_categoria);
        $stmt_select_categoria->bind_param("i", $idproducto);
        $stmt_select_categoria->execute();
        $resultado_categoria = $stmt_select_categoria->get_result();

        if ($resultado_categoria->num_rows > 0) {
            $categoria = $resultado_categoria->fetch_assoc()['categoria_idcategoria'];
        } else {
            throw new Exception("No se encontró la categoría asociada al producto.");
        }

        // Obtener el nombre de la categoría
        $sql_nombre_categoria = "SELECT nombre FROM categoria WHERE idcategoria = ?";
        $stmt_nombre_categoria = $conn->prepare($sql_nombre_categoria);
        $stmt_nombre_categoria->bind_param("i", $categoria);
        $stmt_nombre_categoria->execute();
        $resultado_nombre_categoria = $stmt_nombre_categoria->get_result();

        if ($resultado_nombre_categoria->num_rows > 0) {
            $nombreCategoria = $resultado_nombre_categoria->fetch_assoc()['nombre'];
        } else {
            throw new Exception("No se encontró el nombre de la categoría.");
        }

        // Luego, recuperar las rutas de las imágenes asociadas al producto
        $sql_select_imagenes = "SELECT img1, img2, img3 FROM producto WHERE idproducto = ?";
        $stmt_select_imagenes = $conn->prepare($sql_select_imagenes);
        $stmt_select_imagenes->bind_param("i", $idproducto);
        $stmt_select_imagenes->execute();
        $resultado_imagenes = $stmt_select_imagenes->get_result();

        if ($resultado_imagenes->num_rows > 0) {
            $imagenes = $resultado_imagenes->fetch_assoc();

            // Directorio base donde están almacenadas las imágenes, ajustado con el nombre de la categoría
            $directorioImagenes = "../../vista_Admin/img/categorias/" . $nombreCategoria . "/";

            // Eliminar cada imagen si existe
            foreach ($imagenes as $img) {
                if (!empty($img)) {
                    $rutaCompleta = $directorioImagenes . $img;
                    if (file_exists($rutaCompleta)) {
                        unlink($rutaCompleta);
                    }
                }
            }
        } else {
            throw new Exception("No se encontraron imágenes para el producto.");
        }

        // Luego, elimina las entradas relacionadas en la tabla almacen
        $sql_almacen = "DELETE FROM almacen WHERE producto_idproducto = ?";
        $stmt_almacen = $conn->prepare($sql_almacen);
        $stmt_almacen->bind_param("i", $idproducto);
        $stmt_almacen->execute();
        
        if ($stmt_almacen->affected_rows === 0) {
            throw new Exception("No se encontraron entradas relacionadas en la tabla almacen.");
        }

        // Luego, elimina el producto de la tabla producto
        $sql_producto = "DELETE FROM producto WHERE idproducto = ?";
        $stmt_producto = $conn->prepare($sql_producto);
        $stmt_producto->bind_param("i", $idproducto);
        $stmt_producto->execute();

        if ($stmt_producto->affected_rows === 0) {
            throw new Exception("No se encontró el producto para eliminar.");
        }

        // Si todo va bien, confirmar la transacción
        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        // En caso de error, revertir la transacción
        $conn->rollback();
        echo "Error al eliminar el producto: " . $e->getMessage();
    }

    // Cerrar la conexión
    $conn->close();
} else {
    echo "ID de producto no proporcionado";
}
?>
