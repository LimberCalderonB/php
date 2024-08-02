<?php
include_once "../../../conexion.php";

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $sql = "SELECT nombre_cliente, apellido_cliente FROM cliente WHERE nombre_cliente LIKE ? OR apellido_cliente LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $search = "%$query%";
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="#" class="client-item">' . htmlspecialchars($row['nombre_cliente'] . ' ' . $row['apellido_cliente']) . '</a>';
        }
    } else {
        echo '<a href="#" class="client-item">No se encontraron resultados</a>';
    }

    $stmt->close();
    $conn->close();
}
?>
