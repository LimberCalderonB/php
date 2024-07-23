<?php

require_once "../modelo_admin/mod_rol.php";

session_start(); // Inicia la sesión al principio del archivo

if(isset($_POST["nombre"])) {
    $nombre = htmlspecialchars($_POST["nombre"]);
    if(empty($nombre)) {
        $_SESSION['error_rol'] = true;
        $_SESSION['mensaje_rol'] = "Por favor ingrese un nombre para el rol.";
        header("Location: ../vista_Admin/rol.php");
        exit();
    }
    // Validar el formato del nombre del rol
    if (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
        $_SESSION['error_rol'] = true;
        $_SESSION['mensaje_rol'] = "El nombre del rol solo puede contener letras y espacios.";
        header("Location: ../vista_Admin/rol.php");
        exit();
    }
    $rol = new Rol();
    
    $rol->asignar("nombre", $nombre);
    $resultado = $rol->agregarRol();
    
    if($resultado) {
        $_SESSION['registro_exitoso_rol'] = true;
    } else {
        $_SESSION['error_rol'] = true;
        $_SESSION['mensaje_rol'] = "El rol ya existe.";
    }
} else {
    $_SESSION['error_rol'] = true;
    $_SESSION['mensaje_rol'] = "Error: No se recibieron datos del formulario.";
}

header("Location: ../vista_Admin/rol.php");
exit();
?>
