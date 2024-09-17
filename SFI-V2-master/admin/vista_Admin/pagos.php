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

    // Obtener los detalles del producto
    $sql = "SELECT producto.*, categoria.nombre AS categoria_nombre 
            FROM producto 
            JOIN almacen ON producto.idproducto = almacen.producto_idproducto 
            JOIN categoria ON almacen.categoria_idcategoria = categoria.idcategoria
            WHERE producto.idproducto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idproducto);
    $stmt->execute();
    $result = $stmt->get_result();

    
}

if (isset($_GET['cancelar_id'])) {
    $cancelar_id = $_GET['cancelar_id'];
    if (isset($_SESSION['productos_seleccionados'][$cancelar_id])) {
        unset($_SESSION['productos_seleccionados'][$cancelar_id]);
        
        // Revertir el estado del producto a 'disponible'
        include_once "../../conexion.php";
        $sql = "UPDATE almacen SET estado = 'disponible' WHERE producto_idproducto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cancelar_id);
        if (!$stmt->execute()) {
            echo "Error en la actualización: " . $stmt->error;
        }
        $conn->close();
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

            date_default_timezone_set('America/La_Paz');
            $fecha_venta = date("Y-m-d H:i:s");

            $sql = "INSERT INTO venta (usuario_idusuario, pago, fecha_venta) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ids", $usuario_idusuario, $total, $fecha_venta);
            $stmt->execute();
            $venta_id = $stmt->insert_id;

            foreach ($_SESSION['productos_seleccionados'] as $idproducto => $producto) {
                $sql = "INSERT INTO venta_producto (venta_idventa, producto_idproducto) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $venta_id, $idproducto);
                $stmt->execute();

                $sql = "UPDATE almacen SET cantidad = cantidad - 1 WHERE producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                if (!$stmt->execute()) {
                    echo "Error en la actualización de cantidad: " . $stmt->error;
                }

                $sql = "DELETE FROM almacen WHERE cantidad <= 0 AND producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();
            }

            $conn->commit();
            $_SESSION['productos_seleccionados'] = [];
            $total = 0;
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
            <i class="fi fi-rr-dollar"></i>
            REALIZAR VENTA
        </button>
    </form>
    <div class="total-cost">
        <h5>Total: <?php echo number_format($total, 2); ?> Bs</h5>
    </div>

    <div class="productos-seleccionados">
        <?php if (!empty($_SESSION['productos_seleccionados'])): ?>
            <div class="productos">

                <?php
                // 1. Agrupar productos por categoría
                $productos_por_categoria = [];
                foreach ($_SESSION['productos_seleccionados'] as $producto) {
                    $categoria = $producto['categoria_nombre'];
                    if (!isset($productos_por_categoria[$categoria])) {
                        $productos_por_categoria[$categoria] = [];
                    }
                    $productos_por_categoria[$categoria][] = $producto;
                }

                // 2. Mostrar los productos agrupados por categoría
                foreach ($productos_por_categoria as $categoria => $productos): ?>
                    <div class="categoria">
                        <h6 class="categoria-titulo">Categoria: <?php echo htmlspecialchars($categoria); ?></h6> <!-- Nombre de la categoría -->

                        <?php foreach ($productos as $producto): ?>
                            <div class="product-card">
                                <div class="product-info">
                                    <small><?php echo htmlspecialchars($producto['nombre']); ?></small>
                                    <small class="separator">|</small>
                                    <small>Talla: <?php echo htmlspecialchars($producto['talla']); ?></small>
                                </div>
                                <div class="product-price <?php echo $producto['descuento'] > 0 ? 'discount' : ''; ?>">
                                    <?php if ($producto['descuento'] > 0): ?>
                                        <span class="original-price"><?php echo htmlspecialchars($producto['precio']); ?>-Bs</span> 
                                        Des: <?php echo htmlspecialchars($producto['descuento']); ?>%
                                        |Ahora: <?php echo number_format($producto['precio'] - ($producto['precio'] * ($producto['descuento'] / 100)), 2); ?>-Bs
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($producto['precio']); ?>-Bs
                                    <?php endif; ?>
                                </div>
                                <div class="btn-container">
                                    <a href="pagos.php?cancelar_id=<?php echo htmlspecialchars($producto['idproducto']); ?>" class="btn btn-danger">
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
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


