<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once "../../../conexion.php";

// Imprimir los valores recibidos por POST
var_dump($_POST);

if (isset($_POST['cancelar_pedido'])) {
    $idpedido = $_POST['idpedido'];

    try {
        // Iniciar la transacción
        $conn->begin_transaction();

        // 1. Obtener el idsolicitud relacionado al pedido
        $sql = "SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idpedido);
        if (!$stmt->execute()) {
            throw new Exception("Error al obtener solicitud_idsolicitud: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (empty($row)) {
            throw new Exception("No se encontró la solicitud relacionada con el pedido.");
        }

        $idsolicitud = $row['solicitud_idsolicitud'];

        // 2. Eliminar las inserciones en la tabla pedido_venta
        $sql = "DELETE FROM pedido_venta WHERE pedido_idpedido = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idpedido);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar pedido_venta: " . $stmt->error);
        }

        // 3. Eliminar el pedido
        $sql = "DELETE FROM pedido WHERE idpedido = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idpedido);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar pedido: " . $stmt->error);
        }

        // 4. Cambiar el estado de los productos a 'disponible'
        $sql = "UPDATE almacen SET estado = 'disponible' WHERE producto_idproducto IN (
            SELECT producto_idproducto FROM producto_solicitud WHERE solicitud_idsolicitud = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idsolicitud);
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar estado de productos: " . $stmt->error);
        }

        // 5. Eliminar los productos relacionados en producto_solicitud
        $sql = "DELETE FROM producto_solicitud WHERE solicitud_idsolicitud = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idsolicitud);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar producto_solicitud: " . $stmt->error);
        }

        // 6. Eliminar la solicitud
        $sql = "DELETE FROM solicitud WHERE idsolicitud = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idsolicitud);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar solicitud: " . $stmt->error);
        }

        // Confirmar la transacción
        $conn->commit();
        echo "Pedido cancelado y productos actualizados correctamente.";

        // Redirigir a la página de pedidos
        header("Location: ../pedidos.php");
        exit(); // Asegúrate de detener la ejecución del script después de la redirección

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "Error al cancelar el pedido: " . $e->getMessage();
    }
}
?>
