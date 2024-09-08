<?php include_once "cabecera.php"; ?>

<?php
// Generar un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idproducto'])) {
    if (!isset($_SESSION['productos_seleccionados'])) {
        $_SESSION['productos_seleccionados'] = [];
    }

    $idproducto = $_POST['idproducto'];
    include_once "../../conexion.php";

    $sql = "SELECT producto.*, categoria.nombre AS categoria_nombre 
            FROM producto 
            JOIN almacen ON producto.idproducto = almacen.producto_idproducto 
            JOIN categoria ON almacen.categoria_idcategoria = categoria.idcategoria
            WHERE producto.idproducto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idproducto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        $_SESSION['productos_seleccionados'][$idproducto] = $producto;
    }

    $conn->close();
}

// Manejar la cancelación del producto
if (isset($_GET['cancelar_id'])) {
    $cancelar_id = $_GET['cancelar_id'];
    if (isset($_SESSION['productos_seleccionados'][$cancelar_id])) {
        unset($_SESSION['productos_seleccionados'][$cancelar_id]);
    }
}

// Calcular el total
$total = 0;
if (!empty($_SESSION['productos_seleccionados'])) {
    foreach ($_SESSION['productos_seleccionados'] as $producto) {
        $precio = $producto['precio'];
        if ($producto['descuento'] > 0) {
            $precio -= $precio * ($producto['descuento'] / 100);
        }
        $total += $precio;
    }
}

// Manejar la realización de la venta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['realizar_venta']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (!empty($_SESSION['productos_seleccionados'])) {
        include_once "../../conexion.php";
        $conn->begin_transaction();

        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("ID de usuario no encontrado en la sesión.");
            }

            $usuario_idusuario = $_SESSION['user_id'];

            // Asegúrate de que la zona horaria esté configurada correctamente
            date_default_timezone_set('America/La_Paz'); // Ajusta la zona horaria según tu ubicación
            $fecha_venta = date("Y-m-d H:i:s");

            // Insertar en la tabla `venta`
            $sql = "INSERT INTO venta (usuario_idusuario, pago, fecha_venta) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ids", $usuario_idusuario, $total, $fecha_venta);
            $stmt->execute();
            $venta_id = $stmt->insert_id; // Obtener el ID de la venta recién insertada

            // Insertar en la tabla `venta_producto` y actualizar el inventario
            foreach ($_SESSION['productos_seleccionados'] as $idproducto => $producto) {
                // Insertar en la tabla `venta_producto`
                $sql = "INSERT INTO venta_producto (venta_idventa, producto_idproducto) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $venta_id, $idproducto);
                $stmt->execute();

                // Actualizar el inventario en la tabla `almacen`
                $sql = "UPDATE almacen SET cantidad = cantidad - 1 WHERE producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();

                // Opcional: Eliminar el producto si la cantidad se vuelve 0
                $sql = "DELETE FROM almacen WHERE cantidad <= 0 AND producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();
            }

            $conn->commit();
            $_SESSION['productos_seleccionados'] = [];
            $total = 0; // Restablecer el total a 0 después de la venta exitosa
            echo "<script>
                    const Toast = Swal.mixin({
                      toast: true,
                      position: 'top-end',
                      showConfirmButton: false,
                      timer: 2000,
                      timerProgressBar: true,
                      didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                      }
                    });
                    Toast.fire({
                      icon: 'success',
                      title: 'Venta realizada exitosamente'
                    });
                  </script>";

        } catch (Exception $e) {
            $conn->rollback();
            echo "Error al realizar la venta: " . $e->getMessage();
        }

        $conn->close();
    } else {
        echo "<script>
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'No hay productos seleccionados para la venta',
                });
              </script>";
    }
}

?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    PRODUCTOS SELECCIONADOS
</div>
<br>
<div class="container">
    <form method="POST" action="pagos.php">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button id="realizar-venta" name="realizar_venta" class="btn btn-primary btn-realizar-venta">
            <i class="fi fi-rr-usd-circle"></i>
            REALIZAR VENTA
        </button>
    </form>
    <div class="total-cost">
        <h5>Total: <?php echo number_format($total, 2); ?> Bs</h5>
    </div>
    <div class="productos-seleccionados">
        <?php if (!empty($_SESSION['productos_seleccionados'])): ?>
            <div class="productos-grid">
                <?php foreach ($_SESSION['productos_seleccionados'] as $producto): ?>
                    <div class="product-card">
                        <div class="product-images">
                            <?php
                                $nombreCategoria = isset($producto['categoria_nombre']) ? htmlspecialchars($producto['categoria_nombre']) : 'default';
                                $directorioImagenes = 'img/categorias/' . $nombreCategoria . '/';
                                $imagenes = [
                                    'img1' => isset($producto['ruta_imagen1']) ? htmlspecialchars($producto['ruta_imagen1']) : '',
                                    'img2' => isset($producto['ruta_imagen2']) ? htmlspecialchars($producto['ruta_imagen2']) : '',
                                    'img3' => isset($producto['ruta_imagen3']) ? htmlspecialchars($producto['ruta_imagen3']) : ''
                                ];
                                
                                // Filtrar las imágenes vacías
                                $imagenes = array_filter($imagenes);
                                
                                // Asegurarse de que al menos una imagen esté activa
                                if (!empty($imagenes)) {
                                    $primerImagen = array_shift($imagenes); // La primera imagen activa
                                }
                            ?>

                            <div class="product-images">
                                <?php if (!empty($primerImagen)): ?>
                                    <img src="<?php echo $primerImagen; ?>" alt="img de producto principal" class="img-responsive product-image active">
                                <?php endif; ?>

                                <?php foreach ($imagenes as $imagen): ?>
                                    <img src="<?php echo $imagen; ?>" alt="img de producto" class="img-responsive product-image">
                                <?php endforeach; ?>
                            </div>

                            <button class="prev-button">
                                <i class="fi fi-rr-angle-small-left"></i>
                            </button>
                            <button class="next-button">
                                <i class="fi fi-rr-angle-small-right"></i>
                            </button>
                        </div>

                        <div class="product-info">
                            <small>Categoria: <?php echo htmlspecialchars($producto['categoria_nombre']); ?></small>
                            <small class="separator">|</small>
                            <small>Talla: <?php echo htmlspecialchars($producto['talla']); ?></small>
                        </div>
                        <div class="product-info">
                            <small>Cantidad:</small>
                        </div>
                        <div class="product-price <?php echo $producto['descuento'] > 0 ? 'discount' : ''; ?>">
                            <?php if ($producto['descuento'] > 0): ?>
                                <span class="original-price"><?php echo htmlspecialchars($producto['precio']); ?>-Bs</span> 
                                | Des: <?php echo htmlspecialchars($producto['descuento']); ?>%
                                | Ahora: <?php echo number_format($producto['precio'] - ($producto['precio'] * ($producto['descuento'] / 100)), 2); ?>-Bs
                                
                            <?php else: ?>
                                <?php echo htmlspecialchars($producto['precio']); ?>-Bs
                            <?php endif; ?>
                        </div>
                        <div class="btn-container">
                            <a href="pagos.php?cancelar_id=<?php echo htmlspecialchars($producto['idproducto']); ?>" class="btn btn-danger">
                                <i class="fi fi-rs-cross"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No hay productos seleccionados.</p>
        <?php endif; ?>
    </div>
</div>
<?php 
include_once "pie.php"; 
include_once "validaciones/val_pagos.php";
?>