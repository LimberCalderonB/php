<?php
include_once "../../../conexion.php";

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $sql = "SELECT nombre, apellido FROM cliente WHERE nombre LIKE ? OR apellido LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $search = "%$query%";
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="#" class="client-item">' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</a>';
        }
    } else {
        echo '<a href="#" class="client-item">No se encontraron resultados</a>';
    }

    $stmt->close();
    $conn->close();
}
?>
