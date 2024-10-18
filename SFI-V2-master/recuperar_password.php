<?php
function generate_token() {
    return bin2hex(random_bytes(50)); // Genera un token seguro de 50 bytes
}

function send_recovery_email($email, $token) {
    $link = "http://localhost/proyecto_de_grado/reset_password.php?token=$token";
    
    // Configura PHPMailer aquí para enviar el correo con el enlace
    $subject = "Recuperación de contraseña";
    $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: <a href='$link'>Restablecer contraseña</a>";
    
    // Envía el correo (aquí utilizarías PHPMailer)
    // mail($email, $subject, $message); -> Reemplazar por PHPMailer

    // Ejemplo de envío con PHPMailer:
    // ...
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    include('conexion.php'); // Incluir conexión a la base de datos

    // Comprobar si el correo existe
    $query = "SELECT idusuario FROM usuario WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Generar token y fecha de expiración (1 hora)
        $token = generate_token();
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guardar el token y la expiración en la base de datos
        $updateQuery = "UPDATE usuario SET reset_token = ?, reset_token_expiry = ? WHERE idusuario = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ssi', $token, $expiry, $user['idusuario']);
        $stmt->execute();

        // Enviar el correo de recuperación
        send_recovery_email($email, $token);

        echo json_encode(['status' => 'success', 'message' => 'Correo de recuperación enviado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Correo no registrado']);
    }
}

?>
