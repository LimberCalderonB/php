<?php
header('Content-Type: application/json');
$response = array('success' => false, 'message' => '');

if (isset($_POST['idrol'])) {
    $idrol = $_POST['idrol'];
    include_once "../../../conexion.php";
    
    if ($conn->connect_error) {
        $response['message'] = "Error de conexión: " . $conn->connect_error;
        echo json_encode($response);
        exit;
    }

    // Verificar si el rol está asociado a algún usuario
    $checkSql = "SELECT COUNT(*) AS count FROM privilegio WHERE rol_idrol = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $idrol);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $response['message'] = 'No se puede eliminar el rol porque hay usuarios asociados.';
        echo json_encode($response);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Intentar eliminar el rol
    $sql = "DELETE FROM rol WHERE idrol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idrol);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'El rol ha sido eliminado.';
    } else {
        $response['message'] = "Error al eliminar el rol: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    $response['message'] = "ID de rol no proporcionado";
}

echo json_encode($response);
?>

