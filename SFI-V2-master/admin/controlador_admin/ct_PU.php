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

    $modelo = new ModeloPersonaUsuario();
    $modelo->agregarPersona($ci, $nombre, $apellido1, $apellido2, $celular, $idRol, $nombreUsuario, $pass, $foto);

    // Redirigir o mostrar mensaje de éxito
    $_SESSION['registro'] = 'Persona agregada correctamente.';
    header('Location: ../vista_Admin/personal.php');
    exit();
}


?>
