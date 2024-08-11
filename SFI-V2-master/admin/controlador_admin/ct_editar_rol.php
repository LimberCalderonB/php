<?php
require_once "../modelo_admin/mod_rol.php";

session_start(); // Iniciar sesión para manejar las variables de sesión

// Verificar si se han enviado datos del formulario
if (isset($_POST["idrol"]) && isset($_POST["nombre"])) {
    $idrol = trim($_POST["idrol"]);
    $nombre = trim($_POST["nombre"]);

    // VALIDACIÓN DE CAMPO
    if (empty($nombre)) {
        $_SESSION['error_rol'] = true;
        $_SESSION['mensaje_rol'] = "Por favor ingrese un nombre para el rol.";
        $_SESSION['nombre_rol'] = $nombre;
        header("Location: ../vista_Admin/editar_rol.php?idrol=" . urlencode($idrol));
        exit();
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
        $_SESSION['error_rol'] = true;
        $_SESSION['mensaje_rol'] = "El nombre del rol solo puede contener letras y espacios.";
        $_SESSION['nombre_rol'] = $nombre;
        header("Location: ../vista_Admin/editar_rol.php?idrol=" . urlencode($idrol));
        exit();
    } else {
        $rol = new Rol();
        $rol->asignar("idrol", $idrol);
        $rol->asignar("nombre", $nombre);

        // Verificar si el rol ya existe (excluyendo el rol actual)
        $rol_existente = $rol->existeRol($idrol);

        if ($rol_existente) {
            $_SESSION['error_rol'] = true;
            $_SESSION['mensaje_rol'] = "El rol ya existe.";
            $_SESSION['nombre_rol'] = $nombre;
            header("Location: ../vista_Admin/editar_rol.php?idrol=" . urlencode($idrol));
            exit();
        } else {
            // Intentar actualizar el rol
            if ($rol->actualizarRol()) {
                $_SESSION['registro'] = true; // Establecer la variable de sesión para mostrar la alerta
                header("Location: ../vista_Admin/editar_rol.php?idrol=" . urlencode($idrol)); // Redirigir de nuevo a la página de edición para mostrar la alerta
                exit();
            } else {
                $_SESSION['error_rol'] = true;
                $_SESSION['mensaje_rol'] = "Hubo un problema al intentar actualizar el rol.";
                $_SESSION['nombre_rol'] = $nombre;
                header("Location: ../vista_Admin/editar_rol.php?idrol=" . urlencode($idrol));
                exit();
            }
        }
    }
} else {
    echo "Datos no recibidos.";
    exit();
}
?>
