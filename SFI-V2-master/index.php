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
            background-color: #f5f5f5;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
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
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #3f51b5;
        }

        .form-group .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
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
        <!-- Campo de Usuario -->
        <div class="form-group">
            <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
        </div>

        <!-- Campo de Contraseña -->
        <div class="form-group">
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <i class="material-icons eye-icon" onclick="togglePasswordVisibility()">visibility</i>
        </div>

        <!-- Botón ¿Se te olvidó la contraseña? -->
        <div class="forgot-password" onclick="forgotPassword()">
            ¿Se te olvidó la contraseña?
        </div>

        <!-- Botón de Iniciar Sesión -->
        <button type="submit" class="login-button">Iniciar Sesión</button>
    </form>
</div>

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

    // Función para la opción de "¿Se te olvidó la contraseña?"
    function forgotPassword() {
        Swal.fire({
            icon: 'info',
            title: 'Recuperación de contraseña',
            text: 'Por favor, contacta al soporte técnico para recuperar tu contraseña.'
        });
    }
</script>

</body>
</html>
