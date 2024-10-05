<?php
session_start();
require_once '../../../conexion.php'; // Incluye tu archivo de conexión a la base de datos

if (isset($_POST['idcliente'])) {
    $idcliente = intval($_POST['idcliente']);

    // Iniciar la transacción
    mysqli_begin_transaction($conn);

    try {
        // Eliminamos el cliente de la tabla cliente directamente
        $query_cliente = "DELETE FROM cliente WHERE idcliente = ?";
        $stmt_cliente = mysqli_prepare($conn, $query_cliente);
        mysqli_stmt_bind_param($stmt_cliente, "i", $idcliente);
        mysqli_stmt_execute($stmt_cliente);
        mysqli_stmt_close($stmt_cliente);

        // Si todo va bien, confirmamos la transacción
        mysqli_commit($conn);

        // Devolver respuesta JSON
        echo json_encode(['success' => true, 'message' => 'Cliente eliminado con éxito.']);
    } catch (Exception $e) {
        // Si hay un error, revertimos la transacción
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el cliente: ' . $e->getMessage()]);
    }
}
?>
