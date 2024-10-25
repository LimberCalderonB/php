<?php
include('conexion.php');

$message = ''; // Variable para almacenar los mensajes de alerta
$message_type = 'success'; // Tipo de mensaje por defecto

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar si el token es válido
    $query = "SELECT idusuario, reset_token_expiry FROM usuario WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar si el token ha expirado
        if (new DateTime() < new DateTime($user['reset_token_expiry'])) {
            // Mostrar formulario de nueva contraseña
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                // Actualizar la contraseña y borrar el token
                $updateQuery = "UPDATE usuario SET pass = ?, reset_token = NULL, reset_token_expiry = NULL WHERE idusuario = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param('si', $new_password, $user['idusuario']);
                $stmt->execute();

                $message = 'Contraseña actualizada con éxito.';
            }
        } else {
            $message = 'El enlace de recuperación ha expirado.';
            $message_type = 'error'; // Cambiar a tipo de error
        }
    } else {
        $message = 'Token inválido.';
        $message_type = 'error'; // Cambiar a tipo de error
    }
} else {
    $message = 'No se proporcionó un token.';
    $message_type = 'error'; // Cambiar a tipo de error
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('fondos/fondo1.jpg');
            background-size: cover;
            background-position: center;
        }

        .reset-password-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .material-icons {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .submit-button, .back-button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-button:hover, .back-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para mostrar el mensaje de alerta
        function showToast(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: icon,
                title: title
            });
        }

        // Validación de la contraseña en el lado del cliente
        function validarContrasena() {
            const password = document.getElementById('new_password').value;
            const regex = /^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[A-Za-z0-9!@#$%^&*]{8,}$/;

            if (!regex.test(password)) {
                showToast("error", "La contraseña debe tener al menos 8 caracteres, incluyendo letras, números y caracteres especiales.");
                return false;
            }

            return true;
        }

        // Alternar la visibilidad de la contraseña
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('new_password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }

        // Mostrar mensaje de alerta en caso de haber un mensaje en PHP
        window.onload = function() {
            const message = "<?php echo $message; ?>";
            const messageType = "<?php echo $message_type; ?>"; // Tipo de mensaje

            if (message) {
                showToast(messageType, message);
            }
        }
    </script>
</head>
<body>
<div class="reset-password-container">
    <h2>Restablecer contraseña</h2>
    <form action="" method="POST" onsubmit="return validarContrasena();">
        <div class="form-group">
            <input type="password" name="new_password" id="new_password" placeholder="Nueva contraseña" required>
            <i class="material-icons" id="eye-icon" onclick="togglePasswordVisibility()">visibility</i>
        </div>
        <button type="submit" class="submit-button">Actualizar contraseña</button>
    </form>
    <div class="back-to-login">
        <a href="index.php">Volver al inicio de sesión</a>
    </div>
</div>
</body>
</html>
