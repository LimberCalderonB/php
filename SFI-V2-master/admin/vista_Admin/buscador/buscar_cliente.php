<?php
include_once "../../../conexion.php"; // Asegúrate de que la conexión a la base de datos esté bien configurada

if (isset($_GET['query'])) {
    $busqueda = $conn->real_escape_string($_GET['query']);
    
    // Consulta para buscar coincidencias en la tabla cliente
    $sql = "SELECT idcliente, nombre_cliente, apellido_cliente, apellido2_cliente, celular_cliente, ci_cliente, departamento_cliente
            FROM cliente
            WHERE nombre_cliente LIKE '%$busqueda%' 
               OR apellido_cliente LIKE '%$busqueda%' 
               OR apellido2_cliente LIKE '%$busqueda%' 
               OR celular_cliente LIKE '%$busqueda%'
               OR ci_cliente LIKE '%$busqueda%'
               OR departamento_cliente LIKE '%$busqueda%'
            LIMIT 5"; // Limitar a 5 resultados

    $result = $conn->query($sql);
    $clientes = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
    }

    // Devolver los resultados como JSON
    echo json_encode($clientes);
}
?>
