<?php
session_start();
include_once "../../conexion.php"; // Asegúrate de que la ruta sea correcta
include_once "../modelo_admin/mod_editar_cliente.php";

$errores = [];
$datos = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos
    $idusuario_cliente = $_POST['idusuario_cliente'] ?? '';
    $nombre_cliente = $_POST['nombre_cliente'] ?? '';
    $apellido_cliente = $_POST['apellido_cliente'] ?? '';
    $apellido2_cliente = $_POST['apellido2_cliente'] ?? '';
    $celular_cliente = $_POST['celular_cliente'] ?? '';
    $usuario_cliente = $_POST['usuario_cliente'] ?? '';
    $pass_cliente = $_POST['pass_cliente'] ?? '';

    // Validaciones básicas
    if (empty($nombre_cliente)) {
        $errores['nombre_cliente'] = "El nombre es requerido.";
    }
    if (empty($apellido_cliente)) {
        $errores['apellido_cliente'] = "El primer apellido es requerido.";
    }
    if (empty($celular_cliente)) {
        $errores['celular_cliente'] = "El celular es requerido.";
    }
    if (empty($usuario_cliente)) {
        $errores['usuario_cliente'] = "El nombre de usuario es requerido.";
    }
    if (empty($pass_cliente)) {
        $errores['pass_cliente'] = "La contraseña es requerida.";
    }

    // Verificar si el nombre de usuario ya existe
    $modelo = new ModeloCliente();
    $usuario_existente = $modelo->verificarUsuarioExistente($usuario_cliente, $idusuario_cliente);
    
    if ($usuario_existente) {
        $errores['usuario_cliente'] = "El nombre de usuario ya existe. Elige otro.";
    }

    // Validar otros campos según sea necesario

    if (empty($errores)) {
        $resultado = $modelo->actualizarCliente($idusuario_cliente, $nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $usuario_cliente, $pass_cliente);

        if ($resultado) {
            $_SESSION['mensaje'] = "Cliente actualizado exitosamente.";
            header("Location: ../vista_admin/cliente.php");
            exit();
        } else {
            $errores['general'] = "Error al actualizar el cliente.";
        }
    }

    // Guardar datos y errores en sesión para mostrarlos en la vista
    $_SESSION['errores_cliente'] = $errores;
    $_SESSION['datos_cliente'] = $datos;

    // Redirigir a la vista de edición
    header("Location: ../vista_admin/editar_cliente.php?idusuario_cliente=$idusuario_cliente");
    exit();
}
?>
