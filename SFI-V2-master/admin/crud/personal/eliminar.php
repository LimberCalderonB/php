<?php
include_once '../../../conexion.php';

$response = array('success' => false, 'message' => '');

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['idusuario'])) {
    $idusuario = intval($_POST['idusuario']);

    // Verificar si el usuario ha realizado tareas en otras tablas (como la tabla `venta`)
    $sql = "SELECT COUNT(*) FROM venta WHERE usuario_idusuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $idusuario);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Si hay tareas asociadas, no permitir la eliminación
        $response['message'] = 'No se puede eliminar este usuario, ya que ha realizado tareas asociadas.';
    } else {
        // Comenzar la transacción
        $conn->begin_transaction();

        try {
            // Eliminar privilegios relacionados
            $sql = "DELETE FROM privilegio WHERE usuario_idusuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $idusuario);
            $stmt->execute();

            // Obtener idpersona asociado al usuario
            $sql = "SELECT persona_idpersona FROM usuario WHERE idusuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $idusuario);
            $stmt->execute();
            $stmt->bind_result($persona_idpersona);
            $stmt->fetch();
            $stmt->close();

            // Eliminar el usuario
            $sql = "DELETE FROM usuario WHERE idusuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $idusuario);
            $stmt->execute();

            // Eliminar la persona (si es necesario)
            if ($persona_idpersona) {
                $sql = "DELETE FROM persona WHERE idpersona = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $persona_idpersona);
                $stmt->execute();
            }

            // Confirmar transacción
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'El registro ha sido eliminado.';
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Hubo un problema al eliminar el registro: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Solicitud no válida.';
}

echo json_encode($response);

?>
