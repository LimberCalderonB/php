<?php

if (isset($_POST['idproducto'])) {

    $idproducto = $_POST['idproducto'];
    include_once "../../../conexion.php";
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Iniciar una transacción para asegurar la integridad de los datos
    $conn->begin_transaction();

    try {
        // Primero, elimina las entradas relacionadas en la tabla almacen
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
