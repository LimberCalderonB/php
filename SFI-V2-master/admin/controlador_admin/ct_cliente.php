<?php
session_start();

require_once '../modelo_admin/mod_cliente.php';

if (isset($_POST["nombre_cliente"])) {
    $nombre = trim($_POST["nombre_cliente"]);
    $apellidoP = trim($_POST["apellido_cliente"]);
    $apellidoM = trim($_POST["apellido2_cliente"]);
    $celular = trim($_POST["celular_cliente"]); // Este es el campo a revisar
    $usuario = trim($_POST["usuario_cliente"]);
    $pass = trim($_POST["pass_cliente"]);

    // Array para almacenar los errores
    $errores = [];

    // Validar el campo nombre
    if (empty($nombre)) {
        $errores['nombre_cliente'] = "Por favor ingrese un nombre.";
    } elseif (!preg_match("/^[a-zA-ZñÑ\s]+$/", $nombre)) {
        $errores['nombre_cliente'] = "El nombre solo puede contener letras y espacios.";
    }

    // Validar el apellido paterno
    if (empty($apellidoP)) {
        $errores['apellido_cliente'] = "Por favor ingrese el apellido paterno.";
    }

    // Validar el celular
    if (empty($celular)) {
        $errores['celular_cliente'] = "Por favor ingrese un número de celular.";
    } elseif (!preg_match("/^\d{8,12}$/", $celular)) {
        $errores['celular_cliente'] = "El celular debe tener un numero de celular real.";
    }

    // Validar el nombre de usuario
    if (empty($usuario)) {
        $errores['usuario_cliente'] = "Por favor ingrese un nombre de usuario.";
    }

    // Validar la contraseña
    if (empty($pass)) {
        $errores['pass_cliente'] = "Por favor ingrese una contraseña.";
    } elseif (strlen($pass) < 8) {
        $errores['pass_cliente'] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass) || !preg_match('/[^A-Za-z0-9]/', $pass)) {
        $errores['pass_cliente'] = "La contraseña debe incluir letras, números y al menos un símbolo.";
    }

    // Si hay errores, guardar los errores y los datos en la sesión y redirigir
    if (!empty($errores)) {
        $_SESSION['errores_cliente'] = $errores;
        $_SESSION['datos_cliente'] = $_POST;
        header("Location: ../vista_admin/cliente.php");
        exit();
    } else {
        $modeloCliente = new ModeloCliente();

        if ($modeloCliente->existeUsuario($usuario)) {
            $_SESSION['errores_cliente']['usuario_cliente'] = "El nombre de usuario ya está en uso.";
        }

        // Si hay errores, guardar los errores y los datos en la sesión y redirigir
        if (!empty($_SESSION['errores_cliente'])) {
            $_SESSION['datos_cliente'] = $_POST;
            header("Location: ../vista_admin/cliente.php");
            exit();
        }

        // Si no hay errores, proceder a insertar el cliente
        $idCliente = $modeloCliente->agregarCliente($nombre, $apellidoP, $apellidoM, $celular);

        if ($idCliente) {
            // Luego insertar el usuario para ese cliente
            $resultadoUsuario = $modeloCliente->agregarUsuarioCliente($usuario, $pass, $idCliente);

            if ($resultadoUsuario) {
                $_SESSION['error_cliente'] = false;
                $_SESSION['mensaje_cliente'] = "Cliente agregado con éxito.";
                $_SESSION['registro_exitoso'] = true; 
            } else {
                $_SESSION['error_cliente'] = true;
                $_SESSION['mensaje_cliente'] = "Hubo un problema al agregar el usuario del cliente.";
            }
        } else {
            $_SESSION['error_cliente'] = true;
            $_SESSION['mensaje_cliente'] = "Hubo un problema al agregar el cliente.";
        }

        header("Location: ../vista_admin/cliente.php");
    }
}
?>
