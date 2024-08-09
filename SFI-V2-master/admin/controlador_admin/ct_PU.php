<?php
include_once '../modelo_admin/mod_PU.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ci = $_POST['ci'];
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $celular = $_POST['celular'];
    $idRol = $_POST['idRol'];
    $nombreUsuario = $_POST['nombreUsuario'];
    $pass = $_POST['pass'];
    $foto = isset($_FILES['foto']) ? $_FILES['foto'] : null;
    $idusuario = isset($_POST['idusuario']) ? $_POST['idusuario'] : null;

    $modelo = new ModeloPersonaUsuario();

    try {
        if ($idusuario) {
            $modelo->actualizarPersona($idusuario, $ci, $nombre, $apellido1, $apellido2, $celular, $idRol, $nombreUsuario, $pass, $foto);
        } else {
            $modelo->agregarPersona($ci, $nombre, $apellido1, $apellido2, $celular, $idRol, $nombreUsuario, $pass, $foto);
        }

        // Redirigir o mostrar mensaje de éxito
        $_SESSION['registro'] = 'Datos Registrados';
        header('Location: ../vista_Admin/personal.php');
        exit();
    } catch (Exception $e) {
        // Manejo de errores
        echo "Error: " . $e->getMessage();
    }
}

?>
