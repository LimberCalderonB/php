<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
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

        .forgot-password-container {
            background-color: rgba(255, 255, 255, 0.8); /* Fondo semitransparente */
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
        }

        .forgot-password-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .recover-button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .recover-button:hover {
            background-color: #0056b3;
        }

        .back-to-login {
            margin-top: 20px;
            text-align: center;
        }

        .back-to-login a {
            color: #007bff;
            text-decoration: none;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        // Validación del correo en el lado del cliente
        function validarCorreo() {
            const email = document.getElementById('email').value;
            const regex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

            if (!regex.test(email)) {
                alert('Por favor, ingresa un correo válido de Gmail.');
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    
<div class="forgot-password-container">
    <h2>Recuperación de Contraseña</h2>
    <form id="forgotPasswordForm" action="verificar_correo.php" method="POST" onsubmit="return validarCorreo();">
        <div class="form-group">
            <input type="email" id="email" name="email" placeholder="Introduce tu correo electrónico" required>
        </div>

        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <button type="submit" class="recover-button" id="recoverButton">Enviar</button>
    </form>
    <div class="back-to-login">
        <a href="index.php">Volver al inicio de sesión</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
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

        $('#forgotPasswordForm').on('submit', function(e) {
            e.preventDefault();

            var email = $('#email').val();
            var $button = $('#recoverButton');

            // Deshabilitar el botón para evitar múltiples envíos
            $button.prop('disabled', true).text('Enviando...');

            // Validar que sea un correo @gmail.com
            if (!email.endsWith("@gmail.com")) {
                Toast.fire({
                    icon: 'error',
                    title: 'El correo debe ser de dominio @gmail.com'
                });
                $button.prop('disabled', false).text('Enviar'); // Rehabilitar el botón
                return;
            }

            // Enviar solicitud AJAX a verificar_correo.php
            $.ajax({
                url: 'verificar_correo.php',
                type: 'POST',
                data: { email: email },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        $('#forgotPasswordForm')[0].reset(); // Limpiar el formulario
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                    $button.prop('disabled', false).text('Enviar'); // Rehabilitar el botón
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Hubo un problema con la solicitud. Intenta de nuevo.'
                    });
                    $button.prop('disabled', false).text('Enviar'); // Rehabilitar el botón
                }
            });
        });
    });
</script>

</body>
</html>
