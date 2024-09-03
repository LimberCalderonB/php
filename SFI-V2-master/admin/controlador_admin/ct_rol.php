<?php
require_once "../modelo_admin/mod_rol.php";

session_start(); // Iniciar sesión para manejar las variables de sesión

// Manejar la adición de un nuevo rol
if (isset($_POST["nombre"])) {
    $nombre = trim($_POST["nombre"]);

    // VALIDACIÓN DE CAMPO
    if (empty($nombre)) {
        $_SESSION['error_rol'] = true;
        $_SESSION['mensaje_rol'] = "Por favor ingrese un nombre para el rol.";
        $_SESSION['nombre_rol'] = $nombre;
        header("Location: ../vista_Admin/rol.php");
        exit();
    } elseif (!preg_match("/^[a-zA-Zñ-Ñ-´\s]+$/", $nombre)) {
        $_SESSION['error_rol'] = true;
        $_SESSION['mensaje_rol'] = "El nombre del rol solo puede contener letras y espacios.";
        $_SESSION['nombre_rol'] = $nombre;
        header("Location: ../vista_Admin/rol.php");
        exit();
    } else {
        $rol = new Rol();
        $rol->asignar("nombre", $nombre);

        // Verificar si el rol ya existe
        $rol_existente_agregar = $rol->existeRol();

        if ($rol_existente_agregar) {
            $_SESSION['error_rol'] = true;
            $_SESSION['mensaje_rol'] = "El rol ya existe.";
            $_SESSION['nombre_rol'] = $nombre;
            header("Location: ../vista_Admin/rol.php");
            exit();
        } else {
            // Intentar agregar el rol
            if ($rol->agregarRol()) {
                $_SESSION['registro_exitoso_rol'] = true;
                header("Location: ../vista_Admin/rol.php");
                exit();
            } else {
                $_SESSION['error_rol'] = true;
                $_SESSION['mensaje_rol'] = "Hubo un problema al intentar agregar el rol.";
                $_SESSION['nombre_rol'] = $nombre;
                header("Location: ../vista_Admin/rol.php");
                exit();
            }
        }
    }
} else {
    echo "Datos no recibidos.";
    exit();
}
?>
