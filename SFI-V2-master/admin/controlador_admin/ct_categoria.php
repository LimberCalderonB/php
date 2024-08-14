<?php
require_once "../modelo_admin/mod_categoria.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST["nombre"])) {
    $idcategoria = isset($_POST["idcategoria"]) ? trim($_POST["idcategoria"]) : '';
    $nombre = trim($_POST["nombre"]);

    // VALIDACIÓN DE CAMPO
    if (empty($nombre)) {
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "Por favor ingrese un nombre para la categoría.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nombre)) {
        $_SESSION['error_categoria'] = true;
        $_SESSION['mensaje_categoria'] = "El nombre de la categoría solo puede contener letras y espacios.";
    } else {
        $categoria = new Categoria();
        $categoria->asignar("nombre", $nombre);

        // VERIFICAR EXISTENCIA PARA AGREGAR
        if ($idcategoria == '') {
            $categoria_existente_agregar = $categoria->existeCategoria();

            if ($categoria_existente_agregar) {
                $_SESSION['error_categoria'] = true;
                $_SESSION['mensaje_categoria'] = "La categoría ya existe.";
                $_SESSION['nombre_categoria'] = $nombre;
            } else {
                // Intentar agregar la categoría
                if ($categoria->agregarCategoria()) {
                    // Crear la carpeta con el nombre de la categoría
                    $carpetaCategoria = "../vista_Admin/img/categorias/" . $nombre;
                    if (!file_exists($carpetaCategoria)) {
                        if (mkdir($carpetaCategoria, 0777, true)) {
                            $_SESSION['registro_exitoso_categoria'] = true;
                        } else {
                            $_SESSION['error_categoria'] = true;
                            $_SESSION['mensaje_categoria'] = "La categoría se creó, pero no se pudo crear la carpeta.";
                        }
                    } else {
                        $_SESSION['registro_exitoso_categoria'] = true; // Carpeta ya existe, continuar normalmente
                    }
                    header("Location: ../vista_Admin/categoria.php");
                    exit();
                } else {
                    $_SESSION['error_categoria'] = true;
                    $_SESSION['mensaje_categoria'] = "Hubo un problema al intentar agregar la categoría.";
                    $_SESSION['nombre_categoria'] = $nombre;
                }
            }
        } else {
            // VERIFICAR EXISTENCIA PARA EDITAR
            $categoria_existente_editar = $categoria->existeCategoria();

            if ($categoria_existente_editar && $categoria_existente_editar['idcategoria'] != $idcategoria) {
                $_SESSION['error_categoria'] = true;
                $_SESSION['mensaje_categoria'] = "La categoría ya existe.";
                $_SESSION['nombre_categoria'] = $nombre;
            } 
        }
    }
} else {
    $_SESSION['error_categoria'] = true;
    $_SESSION['mensaje_categoria'] = "Error: No se recibieron datos del formulario.";
}

// Redirigir a la página correspondiente según el contexto
if (isset($_POST['editar'])) {
    header("Location: ../vista_Admin/editar_categoria.php?idcategoria=" . htmlspecialchars($_POST['idcategoria']));
} else {
    header("Location: ../vista_Admin/categoria.php");
}
exit();
?>
