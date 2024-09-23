<?php
include_once "../../../conexion.php"; // Asegúrate de incluir tu archivo de conexión

header('Content-Type: application/json');
$query = isset($_GET['query']) ? $_GET['query'] : '';

if (!empty($query)) {
    // Prepara la consulta
    $sql = "SELECT uc.idusuario_cliente, c.nombre_cliente, c.apellido_cliente, c.apellido2_cliente, c.celular_cliente 
    FROM cliente c 
    JOIN usuario_cliente uc ON c.idcliente = uc.cliente_idcliente 
    WHERE c.nombre_cliente LIKE ? OR 
          c.apellido_cliente LIKE ? OR 
          c.apellido2_cliente LIKE ? OR 
          uc.idusuario_cliente LIKE ? OR 
          c.celular_cliente LIKE ?";


    $stmt = $conn->prepare($sql);
    $param = '%' . $query . '%';
    $stmt->bind_param("sssss", $param, $param, $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();

    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }

    echo json_encode($clientes);
}
?>
