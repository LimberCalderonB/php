
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/sweetalert2.css">
    <link rel="stylesheet" href="css/material.min.css">
    <link rel="stylesheet" href="css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/jquery-1.11.2.min.js"><\/script>')</script>
    <script src="js/material.min.js"></script>
    <script src="js/sweetalert2.min.js"></script>
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/main.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
</head>
<body>
    <div class="login-wrap cover">
        <div class="container-login">
            <p class="text-center" style="font-size: 80px;">
                <i class="zmdi zmdi-account-circle"></i>
            </p>
            <p class="text-center text-condensedLight">INICIO DE SESION</p>
            <form id="loginForm" action="login_process.php" method="post">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input class="mdl-textfield__input" type="text" id="userName" name="username">
                    <label class="mdl-textfield__label" for="userName">Usuario</label>
                </div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <input class="mdl-textfield__input" type="password" id="pass" name="password">
                    <label class="mdl-textfield__label" for="pass">Contraseña</label>
                    <button type="button" onclick="togglePasswordVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none;">
                        <i id="passwordIcon" class="zmdi zmdi-eye"></i>
                    </button>
                </div>
                <button class="mdl-button mdl-js-button" type="button" onclick="validateForm()" style="color: #3F51B5; margin: 0 auto; display: block;">
                    INICIAR SESIÓN
                </button>
            </form>
        </div>
    </div>    

<script>
    function validateForm() {
        var username = document.getElementById('userName').value;
        var password = document.getElementById('pass').value;

        if (!username.trim() && !password.trim()) {
            showError('Por favor, complete todos los campos');
            return false; 
        } else if (!username.trim()) {
            showError('Por favor, ingrese su nombre de usuario');
            return false; 
        } else if (!password.trim()) {
            showError('Por favor, ingrese su contraseña');
            return false; 
        } else {
            document.getElementById('loginForm').submit();
        }
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: message,
        });
    }

    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("pass");
        var passwordIcon = document.getElementById("passwordIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordIcon.classList.remove("zmdi-eye");
            passwordIcon.classList.add("zmdi-eye-off");
        } else {
            passwordInput.type = "password";
            passwordIcon.classList.remove("zmdi-eye-off");
            passwordIcon.classList.add("zmdi-eye");
        }
    }

    // Evitar la navegación hacia atrás
    
</script>
</body>
</html>
