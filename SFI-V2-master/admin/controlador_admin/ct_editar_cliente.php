<?php
session_start();
include_once "../../conexion.php"; // Asegúrate de que la ruta sea correcta
include_once "../modelo_admin/mod_editar_cliente.php";

$errores = [];
$datos = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos
    $idcliente = $_POST['idcliente'] ?? ''; // Asegúrate de que el ID del cliente se envíe desde el formulario
    $nombre_cliente = $_POST['nombre_cliente'] ?? '';
    $apellido_cliente = $_POST['apellido_cliente'] ?? '';
    $apellido2_cliente = $_POST['apellido2_cliente'] ?? '';
    $celular_cliente = $_POST['celular_cliente'] ?? '';
    $ci_cliente = $_POST['ci_cliente'] ?? ''; // Campo nuevo

    // Validaciones básicas
    if (empty($idcliente)) {
        $errores['idcliente'] = "El ID del cliente es requerido.";
    }
    if (empty($nombre_cliente)) {
        $errores['nombre_cliente'] = "El nombre es requerido.";
    }
    if (empty($apellido_cliente)) {
        $errores['apellido_cliente'] = "El primer apellido es requerido.";
    }
    if (empty($celular_cliente)) {
        $errores['celular_cliente'] = "El celular es requerido.";
    } elseif (strlen($celular_cliente) !== 8) { // Validación para el celular
        $errores['celular_cliente'] = "El celular debe tener exactamente 8 dígitos.";
    }
    if (empty($ci_cliente)) {
        $errores['ci_cliente'] = "El CI es requerido."; // Validación para el nuevo campo
    } elseif (strlen($ci_cliente) < 7 || strlen($ci_cliente) > 12) { // Validación para el CI
        $errores['ci_cliente'] = "El CI debe tener entre 7 y 12 dígitos.";
    }

    // Si no hay errores, verificar existencia del celular y CI
    if (empty($errores)) {
        $modelo = new ModeloCliente();

        // Verificar existencia de CI
        if ($modelo->verificarCIExistente($ci_cliente, $idcliente)) {
            $errores['ci_cliente'] = "El CI ya está registrado.";
        }

        // Si aún no hay errores, proceder a actualizar
        if (empty($errores)) {
            $resultado = $modelo->actualizarCliente($idcliente, $nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $ci_cliente);

            if ($resultado) {
                $_SESSION['mensaje'] = "Cliente actualizado exitosamente.";
                header("Location: ../vista_admin/cliente.php");
                exit();
            } else {
                $errores['general'] = "Error al actualizar el cliente.";
            }
        }
    }

    // Guardar datos y errores en sesión para mostrarlos en la vista
    $_SESSION['errores_cliente'] = $errores;
    $_SESSION['datos_cliente'] = $datos;

    // Redirigir a la vista de edición
    header("Location: ../vista_admin/editar_cliente.php?idcliente=$idcliente");
    exit();
}
?>
