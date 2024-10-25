<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once "../../../conexion.php";

// Verificar si se recibió la solicitud de cancelar el pedido
if (isset($_POST['cancelar_pedido'])) {
    $idpedido = $_POST['idpedido'];

    try {
        // Iniciar la transacción
        $conn->begin_transaction();

        // 1. Obtener el idsolicitud, el id de pedido_venta y el estado del pedido
        $sql = "SELECT solicitud_idsolicitud, pedido_venta.idpedido_venta, solicitud.estado 
                FROM pedido 
                JOIN solicitud ON pedido.solicitud_idsolicitud = solicitud.idsolicitud 
                LEFT JOIN pedido_venta ON pedido.idpedido = pedido_venta.pedido_idpedido 
                WHERE pedido.idpedido = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idpedido);
        if (!$stmt->execute()) {
            throw new Exception("Error al obtener solicitud_idsolicitud y estado: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (empty($row)) {
            throw new Exception("No se encontró la solicitud relacionada con el pedido.");
        }

        $idsolicitud = $row['solicitud_idsolicitud'];
        $idpedido_venta = $row['idpedido_venta'];
        $estadoSolicitud = $row['estado'];

        if ($estadoSolicitud === 'pendiente') {
            // 2. Cambiar el estado de los productos a 'disponible' en la tabla 'almacen'
            $sql = "UPDATE almacen SET estado = 'disponible' 
                    WHERE producto_idproducto IN (
                        SELECT producto_idproducto FROM producto_solicitud WHERE solicitud_idsolicitud = ?
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idsolicitud);
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar estado de productos: " . $stmt->error);
            }

            // 3. Eliminar las inserciones en la tabla producto_solicitud
            $sql = "DELETE FROM producto_solicitud WHERE solicitud_idsolicitud = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idsolicitud);
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar producto_solicitud: " . $stmt->error);
            }

            // Código previo...
            
                    } elseif ($estadoSolicitud === 'completado') {
                        // Eliminar ventas relacionadas con el pedido
            
                        // 2. Eliminar los productos relacionados en venta_producto
                        $sql = "DELETE FROM venta_producto WHERE venta_idventa IN (
                                    SELECT idventa FROM venta WHERE pedido_venta_idpedido_venta = ?
                                )";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $idpedido_venta);
                        if (!$stmt->execute()) {
                            throw new Exception("Error al eliminar productos en venta_producto: " . $stmt->error);
                        }
            
                        // 3. Eliminar las ventas relacionadas
                        $sql = "DELETE FROM venta WHERE pedido_venta_idpedido_venta = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $idpedido_venta);
                        if (!$stmt->execute()) {
                            throw new Exception("Error al eliminar las ventas relacionadas: " . $stmt->error);
                        }
            
                        // 4. Eliminar los productos de la tabla almacen
                        $sql = "DELETE FROM almacen WHERE producto_idproducto IN (
                                    SELECT producto_idproducto FROM producto_solicitud WHERE solicitud_idsolicitud = ?
                                )";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $idsolicitud);
                        if (!$stmt->execute()) {
                            throw new Exception("Error al eliminar productos de almacen: " . $stmt->error);
                        }
            
                        // 5. Eliminar las relaciones en producto_solicitud
                        $sql = "DELETE FROM producto_solicitud WHERE solicitud_idsolicitud = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $idsolicitud);
                        if (!$stmt->execute()) {
                            throw new Exception("Error al eliminar producto_solicitud: " . $stmt->error);
                        }
            
                        // 6. Eliminar los productos de la tabla producto
                        $sql = "DELETE FROM producto WHERE idproducto IN (
                                    SELECT producto_idproducto FROM producto_solicitud WHERE solicitud_idsolicitud = ?
                                )";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $idsolicitud);
                        if (!$stmt->execute()) {
                            throw new Exception("Error al eliminar productos de la tabla producto: " . $stmt->error);
                        }
                    }
            
                    // Continuación del código...
            

        // 6. Eliminar las inserciones en la tabla pedido_venta
        if (!empty($idpedido_venta)) {
            $sql = "DELETE FROM pedido_venta WHERE idpedido_venta = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idpedido_venta);
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar referencias en pedido_venta: " . $stmt->error);
            }
        }

        // 7. Eliminar el pedido
        $sql = "DELETE FROM pedido WHERE idpedido = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idpedido);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar pedido: " . $stmt->error);
        }

        // 8. Eliminar la solicitud
        $sql = "DELETE FROM solicitud WHERE idsolicitud = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idsolicitud);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar solicitud: " . $stmt->error);
        }

        // Confirmar la transacción
        $conn->commit();
        echo "Pedido eliminado correctamente.";

        // Redirigir a la página de pedidos
        header("Location: ../pedidos.php");
        exit(); // Asegúrate de detener la ejecución del script después de la redirección

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "Error al eliminar el pedido: " . $e->getMessage();
    }
}
?>
