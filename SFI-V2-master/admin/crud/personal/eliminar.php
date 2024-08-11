<?php
include_once '../../../conexion.php'; // Asegúrate de que este archivo incluya la conexión `$conn`

$response = array('success' => false, 'message' => '');

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['idusuario'])) {
    $idusuario = intval($_POST['idusuario']);

    // Comenzar transacción
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

        // Enviar respuesta JSON
        $response['success'] = true;
        $response['message'] = 'The record has been deleted.';
    } catch (Exception $e) {
        // Rollback en caso de error
        $conn->rollback();
        $response['message'] = 'There was a problem deleting the record: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request.';
}

// Devolver respuesta JSON
echo json_encode($response);
?>
