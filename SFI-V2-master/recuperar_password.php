<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Aquí puedes agregar la lógica para enviar un correo de recuperación
    // Verificar si el email existe en la base de datos y generar un enlace de recuperación

    // Ejemplo de éxito (responder a la solicitud de fetch)
    if (email_exists_in_database($email)) {
        send_recovery_email($email); // Función hipotética para enviar el correo
        http_response_code(200); // Respuesta exitosa
        echo 'Correo enviado correctamente';
    } else {
        http_response_code(400); // Respuesta de error
        echo 'El correo no está registrado';
    }
} else {
    http_response_code(400); // Respuesta de error si no hay correo
    echo 'Solicitud inválida';
}

function email_exists_in_database($email) {
    // Función para verificar si el correo existe en la base de datos
    // Retorna true si el correo existe, false si no
    return true; // Solo para el ejemplo
}

function send_recovery_email($email) {
    // Función para enviar el correo de recuperación
    // Aquí implementarías el envío real del correo con un enlace único para recuperar la contraseña
}
?>
