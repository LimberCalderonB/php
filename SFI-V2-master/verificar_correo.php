<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Asegúrate de que PHPMailer esté correctamente instalado

function generate_token() {
    return bin2hex(random_bytes(50)); // Genera un token seguro de 50 bytes
}

function send_recovery_email($email, $token) {
    $link = "http://localhost/proyecto_de_grado/SFI-V2-master/reset_password.php?token=$token";
    $subject = "Recuperación de contraseña";
    $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: <a href='$link'>Restablecer contraseña</a>";

    // Configuración de PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Usa el servidor SMTP de Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'limbercitoyto@gmail.com'; // Coloca tu dirección de correo aquí
        $mail->Password = 'tdzd mnxe migr vvqh'; // Coloca tu contraseña o la contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('limbercitiyto@gmail.com', 'proyecto_de_grado');
        $mail->addAddress($email); // Correo del destinatario

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    include('conexion.php'); // Incluir conexión a la base de datos

    // Comprobar si el correo existe
    $query = "SELECT idusuario FROM usuario WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    header('Content-Type: application/json'); // Asegúrate de que la respuesta sea JSON

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
        if (send_recovery_email($email, $token)) {
            echo json_encode(['status' => 'success', 'message' => 'Correo de recuperación enviado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'El correo no pudo enviarse.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Correo no registrado']);
    }
}
?>
