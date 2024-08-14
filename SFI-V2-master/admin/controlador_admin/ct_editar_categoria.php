<?php
require_once "../modelo_admin/mod_categoria.php";

// Verificar si se han enviado datos del formulario
if (isset($_POST["idcategoria"]) && isset($_POST["nombre"])) {
    $idcategoria = trim($_POST["idcategoria"]);
    $nuevo_nombre = trim($_POST["nombre"]);

    // VALIDACIÓN DE CAMPO
    if (empty($nuevo_nombre)) {
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "Por favor ingrese un nombre para la categoría.";
        $_SESSION['nombre_categoria'] = $nuevo_nombre;
        header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
        exit();
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nuevo_nombre)) {
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "El nombre de la categoría solo puede contener letras y espacios.";
        $_SESSION['nombre_categoria'] = $nuevo_nombre;
        header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
        exit();
    } else {
        $categoria = new Categoria();
        $categoria->asignar("idcategoria", $idcategoria);
        $categoria->asignar("nombre", $nuevo_nombre);

        // Obtener el nombre actual de la categoría antes de actualizar
        $nombre_actual = $categoria->obtenerNombreCategoria($idcategoria);

        // Verificar si la categoría ya existe (excluyendo la categoría actual)
        $categoria_existente = $categoria->existeCategoria($idcategoria);

        if ($categoria_existente) {
            $_SESSION['error_categoria'] = true;
            $_SESSION['mensaje_categoria'] = "La categoría ya existe.";
            $_SESSION['nombre_categoria'] = $nuevo_nombre;
            header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
            exit();
        } else {
            // Intentar renombrar la carpeta si el nombre ha cambiado
            if ($nombre_actual !== $nuevo_nombre) {
                $carpeta_actual = "../vista_Admin/img/categorias/" . $nombre_actual;
                $nueva_carpeta = "../vista_Admin/img/categorias/" . $nuevo_nombre;

                if (is_dir($carpeta_actual)) {
                    if (!rename($carpeta_actual, $nueva_carpeta)) {
                        $_SESSION['error_categoria'] = true;
                        $_SESSION['mensaje_categoria'] = "Hubo un problema al intentar renombrar la carpeta asociada.";
                        $_SESSION['nombre_categoria'] = $nuevo_nombre;
                        header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
                        exit();
                    }
                } else {
                    // Crear la nueva carpeta si la actual no existe
                    if (!mkdir($nueva_carpeta, 0777, true)) {
                        $_SESSION['error_categoria'] = true;
                        $_SESSION['mensaje_categoria'] = "Hubo un problema al intentar crear la nueva carpeta asociada.";
                        $_SESSION['nombre_categoria'] = $nuevo_nombre;
                        header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria));
                        exit();
                    }
                }
            }

            // Intentar actualizar la categoría
            if ($categoria->actualizarCategoria()) {
                $_SESSION['registro'] = true; // Establecer la variable de sesión para mostrar la alerta
                header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . urlencode($idcategoria)); // Redirigir de nuevo a la página de edición para mostrar la alerta
                exit();
            } else {
                $_SESSION['error_categoria'] = true;
                $_SESSION['mensaje_categoria'] = "Hubo un problema al intentar actualizar la categoría.";
                $_SESSION['nombre_categoria'] = $nuevo_nombre;
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
