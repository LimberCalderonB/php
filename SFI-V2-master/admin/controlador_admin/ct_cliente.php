<?php
session_start();

require_once '../modelo_admin/mod_cliente.php';

if (isset($_POST["nombre_cliente"])) {
    // Recoger los datos del formulario
    $nombre_cliente = trim($_POST["nombre_cliente"]);
    $apellido_cliente = trim($_POST["apellido_cliente"]);
    $apellido2_cliente = trim($_POST["apellido2_cliente"]);
    $celular_cliente = trim($_POST["celular_cliente"]);
    $departamento_cliente = trim($_POST["departamento_cliente"]);
    $ci_cliente = trim($_POST["ci_cliente"]); // Agregando CI del cliente

    // Array para almacenar los errores
    $errores = [];
    if (isset($_POST["departamento_cliente"])) {
        $departamento_cliente = $_POST["departamento_cliente"];

        $departamentos_permitidos = ['Chuquisaca', 'La Paz', 'Cochabamba', 'Oruro', 'Potosí', 'Tarija', 'Santa Cruz', 'Beni', 'Pando'];
        if (!in_array($departamento_cliente, $departamentos_permitidos)) {
            $errores['departamento_cliente'] = "Por favor seleccione un departamento válido.";
        }
    }
    
    // Validar el campo nombre
    if (empty($nombre_cliente)) {
        $errores['nombre_cliente'] = "Por favor ingrese un nombre.";
    } elseif (!preg_match("/^[a-zA-ZñÑ\s]+$/", $nombre_cliente)) {
        $errores['nombre_cliente'] = "El nombre solo puede contener letras y espacios.";
    }

    // Validar el apellido paterno
    if (empty($apellido_cliente)) {
        $errores['apellido_cliente'] = "Por favor ingrese el apellido paterno.";
    }

    // Validar el celular
    if (empty($celular_cliente)) {
        $errores['celular_cliente'] = "Por favor ingrese un número de celular.";
    } elseif (!preg_match("/^\d{8,12}$/", $celular_cliente)) {
        $errores['celular_cliente'] = "El celular debe tener un número de celular real.";
    }

    // Validar el CI
    if (empty($ci_cliente)) {
        $errores['ci_cliente'] = "Por favor ingrese el CI del cliente.";
    }

    // Si hay errores, guardar los errores y los datos en la sesión y redirigir
    if (!empty($errores)) {
        $_SESSION['errores_cliente'] = $errores;
        $_SESSION['datos_cliente'] = $_POST;
        header("Location: ../vista_admin/cliente.php");
        exit();
    } else {
        $modeloCliente = new ModeloCliente();

        // VALIDAR LA EXISTENCIA DEL NÚMERO DE CELULAR
        if ($modeloCliente->existeCelular($celular_cliente)) {
            $_SESSION['errores_cliente']['celular_cliente'] = "El número de celular ya está en uso.";
        }

        // VALIDAR LA EXISTENCIA DEL CI
        if ($modeloCliente->existeci($ci_cliente)) {
            $_SESSION['errores_cliente']['ci_cliente'] = "El CI ya está en uso.";
        }

        // Si hay errores, guardar los errores y los datos en la sesión y redirigir
        if (!empty($_SESSION['errores_cliente'])) {
            $_SESSION['datos_cliente'] = $_POST;
            header("Location: ../vista_admin/cliente.php");
            exit();
        }

        // Si no hay errores, proceder a insertar el cliente
        $idCliente = $modeloCliente->agregarCliente($nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $ci_cliente, $departamento_cliente);

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
