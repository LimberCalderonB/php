<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .login-container {
            background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco con transparencia */
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
            margin-bottom: 12px;
            padding-bottom: 10px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .form-group input.error {
            border-color: red;
        }

        .form-group .error-message {
            color: red;
            font-size: 14px;
            position: absolute;
            bottom: -5px;
            left: 0;
            display: none;
        }

        .form-group .eye-icon {
            position: absolute;
            right: 1px;
            top: 23px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #3f51b5;
        }

        .forgot-password {
            margin-bottom: 20px;
            font-size: 14px;
            color: #3f51b5;
            cursor: pointer;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            background-color: #3f51b5;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        .login-button:hover {
            background-color: #303f9f;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Inicio de Sesión</h2>
    <form id="loginForm" action="login_process.php" method="POST">
        <div class="form-group">
            <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
        </div>
        <div class="form-group">
            <i class="material-icons eye-icon" onclick="togglePasswordVisibility()">visibility</i>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
        </div>

        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        <div class="forgot-password">
            <a href="forgot_password.php">¿Se te olvidó la contraseña?</a>
        </div>

        <button type="submit" class="login-button">Iniciar Sesión</button>
    </form>
</div>

<!-- Incluye el script de reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render=6LfrfGYqAAAAABKGK4VpzbiF58x532gyM7_fC7CZ"></script>
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6LfrfGYqAAAAABKGK4VpzbiF58x532gyM7_fC7CZ', {action: 'login'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
</script>

<script>
    // Función para alternar la visibilidad de la contraseña
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.querySelector('.eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            eyeIcon.textContent = 'visibility';
        }
    }

    // Mostrar alerta de error si hay un mensaje de error en la sesión
    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $_SESSION['error']; ?>',
            position: 'top-end',
            toast: true,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['error']); // Limpiar el error después de mostrarlo ?>
    <?php endif; ?>
</script>

</body>
</html>
