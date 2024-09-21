<?php
session_start();
require_once '../../../conexion.php'; // Incluye tu archivo de conexión a la base de datos

if (isset($_POST['idusuario_cliente'])) {
    $idusuario_cliente = intval($_POST['idusuario_cliente']);

    // Iniciar la transacción
    mysqli_begin_transaction($conn);

    try {
        // Primero, obtenemos el ID del cliente asociado al usuario
        $query_cliente_id = "SELECT cliente_idcliente FROM usuario_cliente WHERE idusuario_cliente = ?";
        $stmt_cliente_id = mysqli_prepare($conn, $query_cliente_id);
        mysqli_stmt_bind_param($stmt_cliente_id, "i", $idusuario_cliente);
        mysqli_stmt_execute($stmt_cliente_id);
        mysqli_stmt_bind_result($stmt_cliente_id, $cliente_idcliente);
        mysqli_stmt_fetch($stmt_cliente_id);
        mysqli_stmt_close($stmt_cliente_id);

        if ($cliente_idcliente) {
            // Luego, eliminamos el usuario de la tabla usuario_cliente
            $query_usuario = "DELETE FROM usuario_cliente WHERE idusuario_cliente = ?";
            $stmt_usuario = mysqli_prepare($conn, $query_usuario);
            mysqli_stmt_bind_param($stmt_usuario, "i", $idusuario_cliente);
            mysqli_stmt_execute($stmt_usuario);
            mysqli_stmt_close($stmt_usuario);

            // Finalmente, eliminamos el cliente de la tabla cliente
            $query_cliente = "DELETE FROM cliente WHERE idcliente = ?";
            $stmt_cliente = mysqli_prepare($conn, $query_cliente);
            mysqli_stmt_bind_param($stmt_cliente, "i", $cliente_idcliente);
            mysqli_stmt_execute($stmt_cliente);
            mysqli_stmt_close($stmt_cliente);

            // Si todo va bien, confirmamos la transacción
            mysqli_commit($conn);

            // Devolver respuesta JSON
            echo json_encode(['success' => true, 'message' => 'Cliente y usuario eliminados con éxito.']);
        } else {
            // Si no se encuentra el cliente, revertimos la transacción
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'No se encontró el cliente asociado.']);
        }
    } catch (Exception $e) {
        // Si hay un error, revertimos la transacción
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el cliente: ' . $e->getMessage()]);
    }
}
?>
