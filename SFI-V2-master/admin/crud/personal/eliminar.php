<?php
error_log("Archivo eliminar.php alcanzado");
include_once '../modelo_admin/mod_PU.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $idprivilegio = $_POST['idprivilegio'];

    // Crear instancia de la clase de conexión
    $conexion = new conexionBase();

    try {
        // Iniciar transacción
        $conexion->beginTransaction();

        // Eliminar registros de la tabla privilegio
        $query = "DELETE FROM privilegio WHERE idprivilegio = ?";
        $conexion->executeQuery($query, [$idprivilegio]);

        // Obtener el ID del usuario asociado
        $query = "SELECT idusuario FROM usuario WHERE privilegio_idprivilegio = ?";
        $result = $conexion->executeQuery($query, [$idprivilegio]);

        if (count($result) > 0) {
            $userId = $result[0]['idusuario'];

            // Eliminar el usuario asociado
            $query = "DELETE FROM usuario WHERE idusuario = ?";
            $conexion->executeQuery($query, [$userId]);

            // Obtener el ID de la persona asociada
            $query = "SELECT persona_idpersona FROM usuario WHERE idusuario = ?";
            $result = $conexion->executeQuery($query, [$userId]);

            if (count($result) > 0) {
                $personaId = $result[0]['persona_idpersona'];

                // Eliminar la persona asociada
                $query = "DELETE FROM persona WHERE idpersona = ?";
                $conexion->executeQuery($query, [$personaId]);
            }
        }

        // Confirmar transacción
        $conexion->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Deshacer transacción en caso de error
        $conexion->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    error_log(print_r($_POST, true));
}
?>
