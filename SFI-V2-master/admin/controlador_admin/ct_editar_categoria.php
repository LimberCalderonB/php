<?php
require_once "../modelo_admin/mod_categoria.php";



// Verificar si se han enviado datos del formulario
if (isset($_POST["idcategoria"]) && isset($_POST["nombre"])) {
    $idcategoria = trim($_POST["idcategoria"]);
    $nombre = trim($_POST["nombre"]);

    // VALIDACIÓN DE CAMPO
    if (empty($nombre)) {
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "Por favor ingrese un nombre para la categoría.";
        $_SESSION['nombre_categoria'] = $nombre;
        header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
        exit();
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "El nombre de la categoría solo puede contener letras y espacios.";
        $_SESSION['nombre_categoria'] = $nombre;
        header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
        exit();
    } else {
        $categoria = new Categoria();
        $categoria->asignar("idcategoria", $idcategoria);
        $categoria->asignar("nombre", $nombre);

        // Verificar si la categoría ya existe (excluyendo la categoría actual)
        $categoria_existente = $categoria->existeCategoria($idcategoria);

        if ($categoria_existente) {
            $_SESSION['error_categoria'] = true;
            $_SESSION['mensaje_categoria'] = "La categoría ya existe.";
            $_SESSION['nombre_categoria'] = $nombre;
            header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
            exit();
        } else {
            // Intentar actualizar la categoría
            if ($categoria->actualizarCategoria()) {
                $_SESSION['registro'] = true; // Establecer la variable de sesión para mostrar la alerta
                header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria)); // Redirigir de nuevo a la página de edición para mostrar la alerta
                exit();
            } else {
                $_SESSION['error_categoria'] = true;
                $_SESSION['mensaje_categoria'] = "Hubo un problema al intentar actualizar la categoría.";
                $_SESSION['nombre_categoria'] = $nombre;
                header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
                exit();
            }
        }
    }
} else {
    echo "Datos no recibidos.";
    exit();
}
?>
