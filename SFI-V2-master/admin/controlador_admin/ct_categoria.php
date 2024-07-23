<?php
require_once "../modelo_admin/mod_categoria.php"; // Incluir el modelo de categoría
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 // Inicia la sesión al principio del archivo

if(isset($_POST["nombre"])) {
    $nombre = trim($_POST["nombre"]);

    // Validar si el nombre está vacío
    if(empty($nombre)) {
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "Por favor ingrese un nombre para la categoría.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
        // Validar el formato del nombre de la categoría
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "El nombre de la categoría solo puede contener letras y espacios.";
    } else {
        // Crear una instancia de la clase Categoria
        $categoria = new Categoria();
        $categoria->asignar("nombre", $nombre);

        // Verificar si la categoría ya existe
        if ($categoria->existeCategoria()) {
            $_SESSION['error_categoria'] = true;
            $_SESSION['mensaje_categoria'] = "La categoría ya existe.";
        } else {
            // Intentar agregar la categoría
            if ($categoria->agregarCategoria()) {
                $_SESSION['registro_exitoso_categoria'] = true;
            } else {
                $_SESSION['error_categoria'] = true;
                $_SESSION['mensaje_categoria'] = "Hubo un problema al intentar agregar la categoría.";
            }
        }
    }
} else {
    $_SESSION['error_categoria'] = true;
    $_SESSION['mensaje_categoria'] = "Error: No se recibieron datos del formulario.";
}

header("Location: ../vista_Admin/categoria.php");
exit();
?>
