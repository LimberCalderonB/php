<?php

header('Content-Type: application/json');
$response = array('success' => false, 'message' => '');

if (isset($_POST['idcategoria'])) {
    $idcategoria = $_POST['idcategoria']; // Asegúrate de usar la variable correcta
    include_once "../../../conexion.php";

    if ($conn->connect_error) {
        $response['message'] = "Error de conexión: " . $conn->connect_error;
        echo json_encode($response);
        exit;
    }

    // Verificar si la categoría tiene productos asociados en la tabla almacen
    $checkSql = "SELECT COUNT(*) AS count FROM almacen WHERE categoria_idcategoria = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $idcategoria);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $response['message'] = 'No se puede eliminar la categoría porque hay productos asociados.';
    } else {
        // Intentar eliminar la categoría
        $deleteSql = "DELETE FROM categoria WHERE idcategoria = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $idcategoria);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'La categoría ha sido eliminada.';
        } else {
            $response['message'] = "Error al eliminar la categoría: " . $stmt->error;
        }
    }
    
    $stmt->close();
    $conn->close();
} else {
    $response['message'] = "ID de categoría no proporcionado";
}

echo json_encode($response);
?>
