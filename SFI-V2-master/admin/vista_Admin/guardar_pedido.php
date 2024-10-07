<?php
include_once '../../conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_cliente = $data['id_cliente'];
$productosSeleccionados = $data['productos'];
$estado_solicitud = 'pendiente';

try {
    $conn->begin_transaction();

    // Insertar en solicitud
    $stmtSolicitud = $conn->prepare("INSERT INTO solicitud (fecha, estado, cliente_idcliente) VALUES (NOW(), ?, ?)");
    if (!$stmtSolicitud) {
        throw new Exception('Error en la preparación de la consulta de solicitud: ' . $conn->error);
    }
    $stmtSolicitud->bind_param("si", $estado_solicitud, $id_cliente);
    $stmtSolicitud->execute();
    $id_solicitud = $stmtSolicitud->insert_id;

    // Insertar productos seleccionados
    foreach ($productosSeleccionados as $producto) {
        $id_producto = $producto['idproducto'];
        $stmtProductoSolicitud = $conn->prepare("INSERT INTO producto_solicitud (producto_idproducto, solicitud_idsolicitud) VALUES (?, ?)");
        if (!$stmtProductoSolicitud) {
            throw new Exception('Error en la preparación de la consulta de producto_solicitud: ' . $conn->error);
        }
        $stmtProductoSolicitud->bind_param("ii", $id_producto, $id_solicitud);
        $stmtProductoSolicitud->execute();
    }
    if ($stmtSolicitud === false) {
        die('Error en la consulta: ' . $conn->error);
    }
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

?>
