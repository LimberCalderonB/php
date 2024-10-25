<?php
// Inicia la sesión
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Incluye la conexión a la base de datos
include('conexion.php');

// Verifica si los campos se enviaron correctamente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $recaptchaResponse = $_POST['g-recaptcha-response']; // Obtiene la respuesta de reCAPTCHA v3

    // Verifica la respuesta de reCAPTCHA
    $secretKey = '6LfrfGYqAAAAAIS2iQEdITfDNkywxCR6Oj-dXnWp';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse");
    $responseKeys = json_decode($response, true);

    // Verifica si el reCAPTCHA es válido y su puntuación
    if ($responseKeys["success"] && $responseKeys["score"] >= 0.1) {
        // Si la puntuación es válida, procede con el login
        // Prepara la consulta para obtener el usuario por el nombre de usuario
        $query = "SELECT * FROM usuario WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica si se encontró un usuario
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifica la contraseña ingresada con la contraseña encriptada
            if (password_verify($password, $user['pass'])) {
                // La contraseña es correcta, inicia la sesión
                $_SESSION['usuario_id'] = $user['idusuario'];
                $_SESSION['nombreUsuario'] = $user['nombreUsuario'];

                // Redirige al usuario al panel de control o página de inicio
                header("Location: admin/vista_Admin/home.php");
                exit();
            } else {
                // La contraseña es incorrecta
                $_SESSION['error'] = "Contraseña incorrecta";
                header("Location: index.php");
                exit();
            }
        } else {
            // No se encontró ningún usuario con ese nombre
            $_SESSION['error'] = "Usuario no encontrado";
            header("Location: index.php");
            exit();
        }
    } else {
        // Si la puntuación es baja, rechaza el intento
        $_SESSION['error'] = "reCAPTCHA fallido. Intento sospechoso.";
        header("Location: index.php");
        exit();
    }
}
?>
