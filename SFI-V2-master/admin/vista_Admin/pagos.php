<?php include_once "cabecera.php"; ?>
<?php

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
            WHERE producto.idproducto = $idproducto";
    $result = $conn->query($sql);

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
        if ($producto['descuento'] > 0) {
            $precioConDescuento = $producto['precio'] - ($producto['precio'] * ($producto['descuento'] / 100));
            $total += $precioConDescuento;
        } else {
            $total += $producto['precio'];
        }
    }
}

// Manejar la realización de la venta
if (isset($_POST['realizar_venta'])) {
    include_once "../../conexion.php";
    $conn->begin_transaction();
    $cliente_nombre = $_POST['cliente_nombre'];
    try {
        // Obtener los datos del usuario actual
        $usuario_id = $_SESSION['usuario_id']; // Suponiendo que el ID del usuario está guardado en la sesión
        $sql = "SELECT nombre, apellido1 FROM persona WHERE idpersona = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->bind_result($nombre_responsable, $apellido_responsable);
        $stmt->fetch();
        $stmt->close();

        // Registrar la venta y eliminar los productos
        foreach ($_SESSION['productos_seleccionados'] as $idproducto => $producto) {
            $pago = $producto['precio'];
            $fecha_actualizacion = date("Y-m-d H:i:s");

            // Insertar la venta en la tabla venta
            $sql = "INSERT INTO venta (persona_idpersona, pago, producto_idproducto, fecha_actualizacion) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idss", $usuario_id, $pago, $idproducto, $fecha_actualizacion);
            $stmt->execute();

            // Eliminar el producto vendido
            $sql = "DELETE FROM producto WHERE idproducto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idproducto);
            $stmt->execute();

            // Actualizar la cantidad en el almacén
            $sql = "UPDATE almacen SET cantidad = cantidad - 1 WHERE producto_idproducto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $idproducto);
            $stmt->execute();
        }

        $conn->commit();
        $_SESSION['productos_seleccionados'] = [];
        echo "Venta realizada exitosamente.";

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error al realizar la venta: " . $e->getMessage();
    }
    
    $conn->close();
}
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    PRODUCTOS SELECCIONADOS
</div>
<br>
<div class="container">
    <div class="top-bar">
        <div class="dropdown">
            <input type="text" id="client-search" placeholder="Buscar cliente..." class="product-search">
            <div class="dropdown-content" id="client-results">
                <!-- Aquí irán las opciones de búsqueda -->
            </div>
        </div>
        <form method="POST" action="pagos.php">
            <button id="realizar-venta" name="realizar_venta" class="btn btn-primary btn-realizar-venta">
                <i class="fi fi-rr-usd-circle"></i>
                REALIZAR VENTA
            </button>
        </form>
    </div>
    <div class="total-cost">
        <h5>Total: <?php echo number_format($total, 2); ?> Bs</h5>
    </div>
    <div class="productos-seleccionados">
        <?php if (!empty($_SESSION['productos_seleccionados'])): ?>
            <div class="productos-grid">
                <?php foreach ($_SESSION['productos_seleccionados'] as $producto): ?>
                    <div class="product-card">
                        <div class="product-images">
                            <?php if (!empty($producto['img1'])): ?>
                                <img src="<?php echo htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img1']); ?>" alt="img de producto 1" class="product-image active">
                            <?php endif; ?>
                            <?php if (!empty($producto['img2'])): ?>
                                <img src="<?php echo htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img2']); ?>" alt="img de producto 2" class="product-image">
                            <?php endif; ?>
                            <?php if (!empty($producto['img3'])): ?>
                                <img src="<?php echo htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img3']); ?>" alt="img de producto 3" class="product-image">
                            <?php endif; ?>
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
<!--BUSCADOR DE CLIENTE--->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#client-search').on('input', function() {
        let searchQuery = $(this).val();

        if (searchQuery.length > 2) {
            $.ajax({
                url: 'buscador/buscar_cliente.php',
                method: 'POST',
                data: { query: searchQuery },
                success: function(data) {
                    $('#client-results').html(data);
                }
            });
        } else {
            $('#client-results').html('');
        }
    });

    $(document).on('click', '.client-item', function() {
    let clientName = $(this).text();
    $('#client-search').val(clientName);
    $('#client_nombre').val(clientName);
    $('#client-results').html('');
});
});
</script>
