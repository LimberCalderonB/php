<?php

header('Content-Type: application/json');
$response = array('success' => false, 'message' => '');

if (isset($_POST['idcategoria'])) {
    $idcategoria = $_POST['idcategoria'];
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
        // Obtener el nombre de la categoría antes de eliminarla
        $nombreCategoriaSql = "SELECT nombre FROM categoria WHERE idcategoria = ?";
        $stmtNombre = $conn->prepare($nombreCategoriaSql);
        $stmtNombre->bind_param("i", $idcategoria);
        $stmtNombre->execute();
        $resultNombre = $stmtNombre->get_result();
        $rowNombre = $resultNombre->fetch_assoc();
        $nombreCategoria = $rowNombre['nombre'];
        
        // Intentar eliminar la categoría
        $deleteSql = "DELETE FROM categoria WHERE idcategoria = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $idcategoria);
        
        if ($stmt->execute()) {
            // Intentar eliminar la carpeta asociada a la categoría
            $carpetaCategoria = "../../vista_Admin/img/categorias/" . $nombreCategoria; // Reemplaza "ruta_especifica" con la ruta adecuada
            if (is_dir($carpetaCategoria)) {
                if (rmdir($carpetaCategoria)) {
                    $response['success'] = true;
                    $response['message'] = 'La categoría y su carpeta asociada han sido eliminadas.';
                } else {
                    $response['success'] = true;
                    $response['message'] = 'La categoría ha sido eliminada, pero no se pudo eliminar la carpeta asociada.';
                }
            } else {
                $response['success'] = true;
                $response['message'] = 'La categoría ha sido eliminada. No se encontró ninguna carpeta asociada.';
            }
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
