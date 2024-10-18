<?php
include('conexion.php'); // Asegúrate de incluir la conexión a tu base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Consulta para comprobar si el correo existe en la base de datos
    $query = "SELECT * FROM usuario WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si el correo existe, enviar una respuesta JSON
    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
}
?>
