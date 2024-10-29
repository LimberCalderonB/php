<?php
session_start();
include_once "../../conexion.php";
require_once '../modelo_admin/mod_cliente.php';

if (isset($_POST["nombre_cliente"])) {
    // Recoger los datos del formulario
    $nombre_cliente = trim($_POST["nombre_cliente"]);
    $apellido_cliente = trim($_POST["apellido_cliente"]);
    $apellido2_cliente = trim($_POST["apellido2_cliente"]);
    $celular_cliente = trim($_POST["celular_cliente"]);
    $ci_cliente = trim($_POST["ci_cliente"]); // Agregando CI del cliente

    // Array para almacenar los errores
    $errores = [];

    // Validar el campo nombre
    if (empty($nombre_cliente)) {
        $errores['nombre_cliente'] = "Por favor ingrese un nombre.";
    } elseif (!preg_match("/^[a-zA-ZñÑ\s]+$/", $nombre_cliente)) {
        $errores['nombre_cliente'] = "El nombre solo puede contener letras y espacios.";
    }

    // Validar el apellido paterno
    if (empty($apellido_cliente)) {
        $errores['apellido_cliente'] = "Por favor ingrese el apellido paterno.";
    } elseif (!preg_match("/^[a-zA-ZñÑ\s]+$/", $apellido_cliente)) {
        $errores['apellido_cliente'] = "El apellido solo puede contener letras y espacios.";
    }

    // Validar el segundo apellido (opcional)
    if (!empty($apellido2_cliente) && !preg_match("/^[a-zA-ZñÑ\s]+$/", $apellido2_cliente)) {
        $errores['apellido2_cliente'] = "El segundo apellido solo puede contener letras y espacios.";
    }

    // Validar el celular
    if (empty($celular_cliente)) {
        $errores['celular_cliente'] = "Por favor ingrese un número de celular.";
    } elseif (!preg_match("/^\d{8,12}$/", $celular_cliente)) {
        $errores['celular_cliente'] = "El celular debe contener entre 8 y 12 dígitos.";
    }

    // Validar el CI
    if (empty($ci_cliente)) {
        $errores['ci_cliente'] = "Por favor ingrese el CI del cliente.";
    } elseif (!preg_match('/^[A-Za-z0-9-]{7,12}$/', $ci_cliente)) {
        $errores['ci_cliente'] = "El CI debe tener entre 7 a 12 caracteres.";
    } else {
        // Crear una instancia de ModeloCliente
        $modeloCliente = new ModeloCliente();

        // Verificar si el CI ya existe en otro cliente
        $idcliente = $_POST['idcliente'] ?? null; // Asegúrate de obtener el ID del cliente si es edición
        if ($modeloCliente->verificarCiExistente($ci_cliente, $idcliente)) {
            $errores['ci_cliente'] = "El CI ingresado ya está registrado para otro cliente.";
        }
    }

    // Si hay errores, guardar los errores y los datos en la sesión y redirigir
    if (!empty($errores)) {
        $_SESSION['errores_cliente'] = $errores;
        $_SESSION['datos_cliente'] = $_POST;
        header("Location: ../vista_admin/cliente.php");
        exit();
    } else {
        $idCliente = $modeloCliente->agregarCliente($nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $ci_cliente);

        if ($idCliente) {
            $_SESSION['error_cliente'] = false;
            $_SESSION['mensaje_cliente'] = "Cliente agregado con éxito.";
            $_SESSION['registro_exitoso'] = true;
        } else {
            $_SESSION['error_cliente'] = true;
            $_SESSION['mensaje_cliente'] = "Hubo un problema al agregar el cliente.";
        }

        header("Location: ../vista_admin/cliente.php");
    }
}
?>
