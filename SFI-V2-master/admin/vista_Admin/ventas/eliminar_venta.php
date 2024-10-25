<?php
include_once "../../../conexion.php";

// Verificar si se ha enviado el ID de la venta
if (isset($_POST['idventa'])) {
    $idVenta = $_POST['idventa'];
    
    // Llamar a la función para eliminar la venta
    eliminarVenta($idVenta, $conn);
    
    // Redirigir o mostrar un mensaje de éxito
    header("Location: ../ventas.php?mensaje=Venta eliminada correctamente");
    exit();
} else {
    echo "ID de venta no especificado.";
}

// Función para eliminar una venta
function eliminarVenta($idVenta, $conn) {
    // Consultar si la venta está asociada a un pedido
    $query = "SELECT pedido_venta_idpedido_venta FROM venta WHERE idventa = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idVenta);
    $stmt->execute();
    $stmt->bind_result($pedidoVentaId);
    $stmt->fetch();
    $stmt->close();

    // Obtener los productos vendidos
    $productosVendidos = [];
    $queryProductos = "SELECT producto_idproducto FROM venta_producto WHERE venta_idventa = ?";
    $stmt = $conn->prepare($queryProductos);
    $stmt->bind_param("i", $idVenta);
    $stmt->execute();
    $stmt->bind_result($productoId);
    while ($stmt->fetch()) {
        $productosVendidos[] = $productoId;
    }
    $stmt->close();

    if (is_null($pedidoVentaId)) {
        // Caso 1: Venta Directa
        // 1. Eliminar productos relacionados de venta_producto
        $deleteVentaProducto = "DELETE FROM venta_producto WHERE venta_idventa = ?";
        $stmt = $conn->prepare($deleteVentaProducto);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        $stmt->close();

        // 2. Eliminar la venta
        $deleteVenta = "DELETE FROM venta WHERE idventa = ?";
        $stmt = $conn->prepare($deleteVenta);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        $stmt->close();

        // 3. Eliminar los productos de la tabla producto
        foreach ($productosVendidos as $productoId) {
            // Primero eliminar de la tabla almacen
            $deleteAlmacen = "DELETE FROM almacen WHERE producto_idproducto = ?";
            $stmt = $conn->prepare($deleteAlmacen);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $stmt->close();

            // Después eliminar de la tabla producto
            $deleteProducto = "DELETE FROM producto WHERE idproducto = ?";
            $stmt = $conn->prepare($deleteProducto);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $stmt->close();
        }

        echo "Venta directa eliminada con ID: $idVenta\n";

    } else {
        // Caso 2: Venta de Pedido
        // 1. Eliminar productos relacionados de venta_producto
        $deleteVentaProducto = "DELETE FROM venta_producto WHERE venta_idventa = ?";
        $stmt = $conn->prepare($deleteVentaProducto);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        $stmt->close();

        // 2. Eliminar la venta
        $deleteVenta = "DELETE FROM venta WHERE idventa = ?";
        $stmt = $conn->prepare($deleteVenta);
        $stmt->bind_param("i", $idVenta);
        $stmt->execute();
        $stmt->close();

        // 3. Eliminar el registro de pedido_venta
        $deletePedidoVenta = "DELETE FROM pedido_venta WHERE idpedido_venta = ?";
        $stmt = $conn->prepare($deletePedidoVenta);
        $stmt->bind_param("i", $pedidoVentaId);
        $stmt->execute();
        $stmt->close();

        // 4. Eliminar las relaciones en producto_solicitud ANTES de eliminar el pedido
        foreach ($productosVendidos as $productoId) {
            $deleteProductoSolicitud = "DELETE FROM producto_solicitud WHERE producto_idproducto = ?";
            $stmt = $conn->prepare($deleteProductoSolicitud);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $stmt->close();
        }

        // 5. Eliminar el pedido
        $deletePedido = "DELETE FROM pedido WHERE idpedido = ?";
        $stmt = $conn->prepare($deletePedido);
        $stmt->bind_param("i", $pedidoVentaId);
        $stmt->execute();
        $stmt->close();

        // 6. Eliminar la solicitud
        $deleteSolicitud = "DELETE FROM solicitud WHERE idsolicitud = (SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = ?)";
        $stmt = $conn->prepare($deleteSolicitud);
        $stmt->bind_param("i", $pedidoVentaId);
        $stmt->execute();
        $stmt->close();

        // 7. Eliminar los productos de la tabla almacen y después de la tabla producto
        foreach ($productosVendidos as $productoId) {
            // Primero eliminar de la tabla almacen
            $deleteAlmacen = "DELETE FROM almacen WHERE producto_idproducto = ?";
            $stmt = $conn->prepare($deleteAlmacen);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $stmt->close();

            // Después eliminar de la tabla producto
            $deleteProducto = "DELETE FROM producto WHERE idproducto = ?";
            $stmt = $conn->prepare($deleteProducto);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $stmt->close();
        }

        echo "Venta de pedido eliminada con ID: $idVenta\n";
    }
}

// Cerrar conexión
$conn->close();
?>
