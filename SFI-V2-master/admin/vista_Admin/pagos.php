<?php 
include_once "cabecera.php"; 
include_once "../../conexion.php";

// Generar un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idproducto'])) {
    if (!isset($_SESSION['productos_seleccionados'])) {
        $_SESSION['productos_seleccionados'] = [];
    }

    $idproducto = $_POST['idproducto'];

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
    $productoDetalles = $result->fetch_assoc();
    
    if ($productoDetalles) {
        // Agregar producto a la sesión
        $_SESSION['productos_seleccionados'][$idproducto] = $productoDetalles;
    }
}

// Cancelar productos seleccionados
if (isset($_GET['cancelar_id']) && isset($_GET['cantidad'])) {
    $cancelar_id = $_GET['cancelar_id'];
    $cantidad_a_cancelar = (int)$_GET['cantidad'];

    if ($cantidad_a_cancelar > 0 && isset($_SESSION['productos_seleccionados'][$cancelar_id])) {
        $producto_a_cancelar = $_SESSION['productos_seleccionados'][$cancelar_id];

        // Filtrar los productos similares
        $productos_similares = array_filter($_SESSION['productos_seleccionados'], function($producto) use ($producto_a_cancelar) {
            return $producto['nombre'] == $producto_a_cancelar['nombre'] &&
                   $producto['precio'] == $producto_a_cancelar['precio'] &&
                   $producto['talla'] == $producto_a_cancelar['talla'] &&
                   $producto['descuento'] == $producto_a_cancelar['descuento'] &&
                   $producto['categoria_nombre'] == $producto_a_cancelar['categoria_nombre'];
        });

        $contador = 0;
        foreach ($productos_similares as $id => $producto) {
            if ($contador < $cantidad_a_cancelar) {
                unset($_SESSION['productos_seleccionados'][$id]); // Eliminar el producto de la sesión
                $contador++;

                // Revertir el estado del producto a 'disponible'
                $sql = "UPDATE almacen SET estado = 'disponible' WHERE producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $producto['idproducto']);
                $stmt->execute();
            } else {
                break;
            }
        }
    }
}

// Calcular el total de la compra
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

// Cancelar todos los productos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_todo'])) {
    // Consultar productos con estado 'agotado' y cantidad 1
    $sql = "SELECT producto_idproducto FROM almacen WHERE estado = 'agotado' AND cantidad = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Actualizar el estado a 'disponible' para los productos encontrados
    while ($producto = $resultado->fetch_assoc()) {
        $idProducto = $producto['producto_idproducto'];
        $sql_update = "UPDATE almacen SET estado = 'disponible' WHERE producto_idproducto = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $idProducto);
        $stmt_update->execute();
    }

    // Limpiar los productos seleccionados de la sesión
    unset($_SESSION['productos_seleccionados']);
    unset($_SESSION['idcliente']);
    unset($total); 

    // Redireccionar a pagos.php
    @header("Location: pagos.php");
    exit(); // Asegurarse de que no se ejecute más código después de la redirección
}


if (isset($_SESSION['idpedido'], $_SESSION['idsolicitud'], $_SESSION['idcliente'], $_SESSION['productos_seleccionados'])) {
    /*echo "<h3>Pedido ID: " . $_SESSION['idpedido'] . "</h3>";
    echo "<h3>Solicitud ID: " . $_SESSION['idsolicitud'] . "</h3>";
    echo "<h3>Cliente: " . $_SESSION['nombre_cliente'] . "</h3>";

    echo "<h4>Productos vinculados:</h4>";
    echo "<ul>";*/
    foreach ($_SESSION['productos_seleccionados'] as $producto) {
    //    echo "<li>" . $producto['nombre'] . " - Precio: " . $producto['precio'] . " - Talla: " . $producto['talla'] . " - Descuento: " . $producto['descuento'] . " - Categoría: " . $producto['categoria_nombre'] . "</li>";
    }
    echo "</ul>";
}
// Procesar la venta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['realizar_venta']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (!empty($_SESSION['productos_seleccionados'])) {
        include_once "../../conexion.php";
        $conn->begin_transaction();

        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("ID de usuario no encontrado en la sesión.");
            }

            $usuario_idusuario = $_SESSION['user_id'];
            $total = 0;

            // Verificar si se está realizando una venta de un pedido
            $es_venta_pedido = isset($_SESSION['idpedido']) ? true : false;
            $pedido_venta_idpedido_venta = null;

            if ($es_venta_pedido) {
                $idpedido = $_SESSION['idpedido'];

                // Insertar en la tabla pedido_venta
                $sql = "INSERT INTO pedido_venta (pedido_idpedido) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idpedido);
                $stmt->execute();
                $pedido_venta_idpedido_venta = $stmt->insert_id;

                // Actualizar el estado del pedido a 'completado' en la tabla solicitud
                $sql = "UPDATE solicitud SET estado = 'completado' WHERE idsolicitud = (SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idpedido);
                $stmt->execute();
            }

            // Insertar la venta (colocamos NULL para venta normal)
            date_default_timezone_set('America/La_Paz');
            $fecha_venta = date("Y-m-d H:i:s");

            $sql = "INSERT INTO venta (usuario_idusuario, pago, fecha_venta, pedido_venta_idpedido_venta) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // Si no es una venta de pedido, $pedido_venta_idpedido_venta será NULL
            if (!$es_venta_pedido) {
                $pedido_venta_idpedido_venta = NULL;
            }
            $stmt->bind_param("idsi", $usuario_idusuario, $total, $fecha_venta, $pedido_venta_idpedido_venta);
            $stmt->execute();
            $venta_id = $stmt->insert_id;

            // Procesar los productos vendidos
            foreach ($_SESSION['productos_seleccionados'] as $producto) {
                $idproducto = $producto['idproducto'];

                $sql = "INSERT INTO venta_producto (venta_idventa, producto_idproducto) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $venta_id, $idproducto);
                $stmt->execute();

                // Reducir la cantidad en el almacén
                $sql = "UPDATE almacen SET cantidad = cantidad - 1 WHERE producto_idproducto = ? AND cantidad > 0";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();

                // Actualizar el estado del producto a 'agotado' si la cantidad llega a 0
                $sql = "UPDATE almacen SET estado = 'agotado' WHERE producto_idproducto = ? AND cantidad = 0";
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();
            }

            $conn->commit();

            // Limpiar la sesión de productos seleccionados y cliente
            $_SESSION['productos_seleccionados'] = [];
            unset($_SESSION['idcliente']);
            unset($_SESSION['nombre_cliente']);
            unset($_SESSION['idpedido']);
            unset($_SESSION['productos_seleccionados']);


            $total = 0;

            // Mensaje de éxito
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
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                  </script>";
                  echo "<script>window.location.reload();</script>";

        } catch (Exception $e) {
            $conn->rollback();
            echo "Error al realizar la venta: " . $e->getMessage();
        }
        $conn->close();
    }
    unset($_SESSION['idpedido']);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_pedido']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (!empty($_SESSION['productos_seleccionados'])) {
        include_once "../../conexion.php";
        $conn->begin_transaction();

        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("ID de usuario no encontrado en la sesión.");
            }

            $usuario_idusuario = $_SESSION['user_id'];
            $solicitud_id = null;

            // Verificar si hay un cliente seleccionado
            if (!empty($_POST['idcliente'])) {
                $cliente_id = intval($_POST['idcliente']);
                
                // Verificar que el cliente existe
                $sql = "SELECT * FROM cliente WHERE idcliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $cliente_id);
                $stmt->execute();
                $result = $stmt->get_result();
            
                if ($result->num_rows > 0) {
                    // El cliente existe, proceder a crear la solicitud
                    $sql = "INSERT INTO solicitud (cliente_idcliente, estado) VALUES (?, 'pendiente')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $cliente_id);
                    $stmt->execute();
                    $solicitud_id = $stmt->insert_id; // Obtener el ID de la nueva solicitud
                } else {
                    throw new Exception("El cliente no existe.");
                }
            } else {
                throw new Exception("Debe seleccionar un cliente.");
            }

            // Insertar los productos asociados a la solicitud
            foreach ($_SESSION['productos_seleccionados'] as $idproducto => $producto) {
                // Insertar en producto_solicitud
                $sql = "INSERT INTO producto_solicitud (producto_idproducto, solicitud_idsolicitud) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $idproducto, $solicitud_id);
                $stmt->execute();

                // Actualizar el estado del producto a 'espera' en la tabla almacen
                $sql = "UPDATE almacen SET estado = 'espera' WHERE producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();
            }             

            // Crear un pedido en la tabla pedido vinculado a la solicitud y al usuario responsable
                $sql = "INSERT INTO pedido (solicitud_idsolicitud, usuario_idusuario) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $solicitud_id, $usuario_idusuario); // $usuario_idusuario viene de la sesión
                $stmt->execute();
                $pedido_id = $stmt->insert_id; // Obtener el ID del nuevo pedido

            // Vacía los productos seleccionados de la sesión
            unset($_SESSION['idcliente']);
            unset($_SESSION['nombre_cliente']);
            unset($_SESSION['productos_seleccionados']);

            unset($total);
            // Confirmar transacción
            $conn->commit();
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
                      title: 'Pedido guardado'
                    });
                  </script>";
                  echo "<script>window.location.reload();</script>";

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            echo "Error al guardar el pedido: " . $e->getMessage();
        }
        $conn->close();
    }
}
//print_r($_SESSION);

?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    PRODUCTOS SELECCIONADOS
</div>
<br>
<div class="container text-center" style="position: relative;">
    <!-- Campo de búsqueda de clientes -->
    <input type="text" id="buscar" placeholder="Buscar cliente..." class="form-control"
        value="<?php echo isset($_SESSION['idcliente']) ? obtenerNombreCliente($conn, $_SESSION['idcliente']) : ''; unset($_SESSION['idcliente']);
unset($_SESSION['nombre_cliente']);
?>">

    <div id="resultados" class="mt-2" style="display: none;"></div> 
</div>

<?php
function obtenerNombreCliente($conn, $idcliente) {
    if (!$conn) {
        return 'Error en la conexión a la base de datos.';
    }

    $sql = "SELECT nombre_cliente, apellido_cliente, apellido2_cliente 
            FROM cliente WHERE idcliente = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return 'Error en la preparación de la consulta.';
    }

    $stmt->bind_param("i", $idcliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['nombre_cliente'] . ' ' . $row['apellido_cliente'] . ' ' . $row['apellido2_cliente'];
    } else {
        return 'Cliente no encontrado.';
    }
}

?>

<div class="container">
    <form method="POST" action="pagos.php">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <button id="realizar-venta" name="realizar_venta" class="btn btn-primary btn-realizar-venta">
            <i class="fi fi-rr-dollar"></i>
            REALIZAR VENTA
        </button>
        <input type="hidden" name="idcliente" id="idcliente">
        <button id="guardar-pedido" name="guardar_pedido" class="btn btn-secondary btn-guardar-pedido">
            <i class="fi fi-rr-save"></i>
            GUARDAR PEDIDO
        </button>
        
        <!-- Botón para cancelar todo -->
        <button type="submit" name="cancelar_todo" class="btn btn-danger">
            <i class="fi fi-rr-trash"></i>
            CANCELAR TODO
        </button>
    </form>
    
    <div class="total-cost">
        <h5>Total: <?php echo number_format(@$total, 2); ?> Bs</h5>
    </div>

    <div class="productos-seleccionados">
        <?php if (!empty($_SESSION['productos_seleccionados'])): ?>
            <div class="productos">
                <?php
                // 1. Agrupar productos por categoría, nombre, precio, talla, descuento
                $productos_por_categoria = [];

                foreach ($_SESSION['productos_seleccionados'] as $producto) {
                    $categoria = $producto['categoria_nombre'];
                    $clave = $producto['nombre'] . '|' . $producto['precio'] . '|' . $producto['talla'] . '|' . $producto['descuento'];

                    if (!isset($productos_por_categoria[$categoria])) {
                        $productos_por_categoria[$categoria] = [];
                    }

                    if (!isset($productos_por_categoria[$categoria][$clave])) {
                        $productos_por_categoria[$categoria][$clave] = [
                            'producto' => $producto,
                            'cantidad' => 0
                        ];
                    }

                    $productos_por_categoria[$categoria][$clave]['cantidad']++;
                }

                // 2. Mostrar los productos agrupados por categoría
                foreach ($productos_por_categoria as $categoria => $productos_agrupados): ?>
                    <div class="categoria">
                        <h6 class="categoria-titulo">Categoría: <?php echo htmlspecialchars($categoria); ?></h6>

                        <?php foreach ($productos_agrupados as $clave => $item):
                            $producto = $item['producto'];
                            $cantidad = $item['cantidad'];
                        ?>
                            <div class="product-card">
                                <div class="product-info">
                                    <small><?php echo htmlspecialchars($producto['nombre']); ?></small>
                                    <small class="separator">|</small>
                                    <small>Talla: <?php echo htmlspecialchars($producto['talla']); ?></small>
                                </div>
                                <div class="product-info">
                                    <small>Cantidad: <?php echo $cantidad; ?></small>
                                </div>
                                <div class="product-price <?php echo $producto['descuento'] > 0 ? 'discount' : ''; ?>">
                                    <?php if ($producto['descuento'] > 0): ?>
                                        <span class="original-price"><?php echo htmlspecialchars($producto['precio']); ?>-Bs</span>
                                        Des: <?php echo htmlspecialchars($producto['descuento']); ?>%
                                        | Ahora: <?php echo number_format($producto['precio'] - ($producto['precio'] * ($producto['descuento'] / 100)), 2); ?>-Bs
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($producto['precio']); ?>-Bs
                                    <?php endif; ?>
                                </div>
                                <div class="btn-container">
                                    <button onclick="cancelarProducto(<?php echo htmlspecialchars($producto['idproducto']); ?>, <?php echo $cantidad; ?>)" class="btn btn-danger">
                                        Cancelar
                                    </button>
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