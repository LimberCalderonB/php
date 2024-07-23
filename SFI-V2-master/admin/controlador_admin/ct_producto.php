<?php
include_once '../modelo_admin/mod_producto.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoge los datos del formulario
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descuento = $_POST['descuento'];
    $descripcion = $_POST['descripcion'];
    $talla = $_POST['talla'];
    $categoria_idcategoria = $_POST['categoria_idcategoria'];
    $estado = $_POST['estado'];

    // Manejo del archivo de imagen
    // Manejo de los archivos de imagen
$imagenes = ['img1', 'img2', 'img3'];
$rutasImagenes = [];

foreach ($imagenes as $imagen) {
    if (isset($_FILES[$imagen]) && $_FILES[$imagen]['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES[$imagen]['tmp_name'];
        $fileName = $_FILES[$imagen]['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = '../vista_Admin/img/vestidos/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $rutasImagenes[$imagen] = $newFileName;
        } else {
            $rutasImagenes[$imagen] = null; // En caso de error al subir la foto
        }
    } else {
        $rutasImagenes[$imagen] = null; // No se ha subido ninguna foto
    }
}

// Almacenar las rutas de las imágenes en variables separadas
$img1 = $rutasImagenes['img1'] ?? null;
$img2 = $rutasImagenes['img2'] ?? null;
$img3 = $rutasImagenes['img3'] ?? null;

// Código adicional para insertar estos valores en la base de datos


    $modelo = new ModeloProducto();
    $resultado = $modelo->agregarProducto($nombre, $precio, $descuento, $descripcion, $talla, $categoria_idcategoria, $estado, $img1, $img2, $img3);

    if ($resultado) {
        header("Location: ../vista_Admin/productos.php?mensaje=Producto agregado exitosamente");
    } else {
        header("Location: ../vista_Admin/productos.php?error=Hubo un problema al agregar el producto");
    }
    // Redirigir o mostrar mensaje de éxito
    $_SESSION['registro'] = 'Persona agregada correctamente.';
    header('Location: ../vista_Admin/productos.php');
    exit();
} else {
    // Manejo de GET para mostrar productos
    $modelo = new ModeloProducto();
    $productos = $modelo->obtenerProductos();

    // Incluye la vista y pasa los productos
    include '../vista_Admin/productos.php';

    
}
?>
