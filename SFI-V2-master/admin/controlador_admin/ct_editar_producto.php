<?php
require '../../../vendor/autoload.php';
use WebPConvert\WebPConvert;

// Incluir archivo de conexión
include_once "../../conexion.php"; 
include_once "../modelo_admin/mod_editar_producto.php";

// Recibir los datos del formulario
$idproducto = $_POST['idproducto'];
$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$talla = $_POST['talla'];
$descripcion = $_POST['descripcion'];
$descuento = isset($_POST['descuento']) ? $_POST['descuento'] : null;
$categoria_idcategoria = $_POST['categoria_idcategoria'] ?? null;
$productos_similares = $_POST['productos_similares'] ?? []; // Array de IDs de productos similares

// Verificar campos obligatorios
if (!isset($nombre) || !isset($precio) || !isset($talla) || $descuento === null || !isset($categoria_idcategoria)) {
    $_SESSION['registro'] = 'Todos los campos son obligatorios.';
    header("Location: ../vista_Admin/productos.php?mensaje=Error: Campos incompletos");
    exit;
}

// Obtener la categoría actual y la nueva
$modeloProducto = new ModeloProducto();
$categoria_actual_id = $modeloProducto->obtenerCategoriaPorId($idproducto);
$nombreCategoriaNueva = obtenerNombreCategoriaDesdeBD($categoria_idcategoria);
$nombreCategoriaActual = obtenerNombreCategoriaDesdeBD($categoria_actual_id);

$directorioBase = '../vista_Admin/img/categorias/';
$directorioImagenesNueva = $directorioBase . $nombreCategoriaNueva . '/';
$directorioImagenesActual = $directorioBase . $nombreCategoriaActual . '/';

// Crear la nueva carpeta si no existe
if (!is_dir($directorioImagenesNueva)) {
    mkdir($directorioImagenesNueva, 0777, true);
}

// Lógica para mover las imágenes
$imagenes = ['img1', 'img2', 'img3'];
$rutasImagenes = [];

// Mover imágenes existentes a la nueva carpeta de categoría
foreach ($imagenes as $imagen) {
    $nombreImagenActual = $_POST['original_' . $imagen] ?? null; // Nombre de la imagen original
    $quitarImagen = isset($_POST['remove_' . $imagen]) && $_POST['remove_' . $imagen] == '1'; // Verifica si se debe quitar la imagen

    if ($quitarImagen && $nombreImagenActual) {
        $rutaImagenAEliminar = $directorioImagenesActual . $nombreImagenActual;

        if (file_exists($rutaImagenAEliminar)) {
            unlink($rutaImagenAEliminar); // Elimina la imagen del servidor
        }
        $rutasImagenes[$imagen] = null; // No hay imagen nueva, asigna null
    } else {
        if ($nombreImagenActual) {
            $rutaActual = $directorioImagenesActual . $nombreImagenActual;
            $rutaNueva = $directorioImagenesNueva . $nombreImagenActual;

            if (file_exists($rutaActual)) {
                rename($rutaActual, $rutaNueva); // Mover la imagen
                $rutasImagenes[$imagen] = $nombreImagenActual; // Guardar la ruta nueva
            }
        }

        // Manejo de la nueva imagen
        if (isset($_FILES[$imagen]) && $_FILES[$imagen]['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$imagen]['tmp_name'];
            $fileName = $_FILES[$imagen]['name'];
            $newFileName = md5(time() . $fileName . rand(0, 1000)) . '.webp';
            $dest_path = $directorioImagenesNueva . $newFileName;

            try {
                WebPConvert::convert($fileTmpPath, $dest_path, ['quality' => 30]);
                $rutasImagenes[$imagen] = $newFileName; // Asigna la nueva ruta de la imagen convertida
            } catch (Exception $e) {
                $_SESSION['registro'] = 'Error al convertir la imagen a WebP: ' . $e->getMessage();
                header("Location: ../vista_Admin/productos.php?mensaje=Error al convertir la imagen a WebP");
                exit;
            }
        } else {
            $rutasImagenes[$imagen] = $nombreImagenActual; // Si no hay nueva imagen, usar la original
        }
    }
}

// Instanciar el modelo y actualizar productos
foreach ($productos_similares as $id_similar) {
    $actualizacionExito = $modeloProducto->actualizarProducto(
        $id_similar,
        $nombre,
        $precio,
        $descuento,
        @$precioConDescuento,
        $descripcion,
        $talla,
        $categoria_idcategoria,
        $rutasImagenes['img1'] ?? null,
        $rutasImagenes['img2'] ?? null,
        $rutasImagenes['img3'] ?? null
    );

    if ($actualizacionExito) {
        echo "Producto con ID $id_similar actualizado con éxito.<br>";
    } else {
        echo "Error al actualizar el producto con ID $id_similar.<br>";
    }
}

function obtenerNombreCategoriaDesdeBD($idCategoria) {
    global $conn;

    $sql = "SELECT nombre FROM categoria WHERE idcategoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idCategoria);
    $stmt->execute();
    $stmt->bind_result($nombreCategoria);
    $stmt->fetch();
    $stmt->close();

    return $nombreCategoria;
}

// Redirigir o mostrar un mensaje final
header("Location: ../vista_Admin/productos.php?mensaje=actualizacion_exitosa");
exit();
?>
