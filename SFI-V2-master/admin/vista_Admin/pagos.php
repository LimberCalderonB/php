<?php 
include_once "cabecera.php"; 
include_once "../../conexion.php";
?>

<?php
// Generar un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['idcliente']) && isset($_SESSION['productos'])) {
    $idcliente = $_SESSION['idcliente'];
    $productos = $_SESSION['productos'];

    // Obtener el nombre del cliente usando la función obtenerNombreCliente
    $nombreCliente = obtenerNombreCliente($conn, $idcliente);
} else {
    $idcliente = null;
    $productos = null;
    $nombreCliente = '';
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

if (isset($_GET['cancelar_id']) && isset($_GET['cantidad'])) {
    $cancelar_id = $_GET['cancelar_id'];
    $cantidad_a_cancelar = (int)$_GET['cantidad'];

    if ($cantidad_a_cancelar > 0) {
        // Obtener los detalles del producto a cancelar

        @$producto_a_cancelar = $_SESSION['productos_seleccionados'][$cancelar_id];

        // Filtrar los productos que coinciden con los criterios: nombre, precio, talla, descuento, categoría
        @$productos_similares = array_filter($_SESSION['productos_seleccionados'], function($producto) use ($producto_a_cancelar) {
            return $producto['nombre'] == $producto_a_cancelar['nombre'] &&
                   $producto['precio'] == $producto_a_cancelar['precio'] &&
                   $producto['talla'] == $producto_a_cancelar['talla'] &&
                   $producto['descuento'] == $producto_a_cancelar['descuento'] &&
                   $producto['categoria_nombre'] == $producto_a_cancelar['categoria_nombre'];
        });

        // Cancelar solo la cantidad indicada
        $contador = 0;
        include_once "../../conexion.php"; // Conectar solo una vez
        foreach ($productos_similares as $id => $producto) {
            if ($contador < $cantidad_a_cancelar) {
                unset($_SESSION['productos_seleccionados'][$id]); // Eliminar el producto de la sesión
                $contador++;

                // Revertir el estado del producto a 'disponible'
                $sql = "UPDATE almacen SET estado = 'disponible' WHERE producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    echo "Error en la preparación de la consulta: " . $conn->error;
                    break;
                }
                $stmt->bind_param("i", $producto['idproducto']);
                if (!$stmt->execute()) {
                    echo "Error en la actualización: " . $stmt->error;
                    break;
                }
                $stmt->close(); // Cerrar el statement después de usarlo
            } else {
                break;
            }
        }
        $conn->close(); // Cerrar la conexión después de procesar todos los productos
    }
}

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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancelar_todo'])) {
        // Limpiar los productos seleccionados
        unset($_SESSION['productos_seleccionados']);
        // También puedes limpiar otros datos si es necesario
        unset($_SESSION['idcliente']);

        @header("Location: pagos.php");

    }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['realizar_venta']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    if (!empty($_SESSION['productos_seleccionados'])) {
        include_once "../../conexion.php";
        $conn->begin_transaction();

        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("ID de usuario no encontrado en la sesión.");
            }

            $usuario_idusuario = $_SESSION['user_id'];
            $pedido_venta_idpedido_venta = null;

            // Verificar si hay un cliente seleccionado
            if (!empty($_POST['idcliente'])) {
                $usuario_cliente_id = intval($_POST['idcliente']);
                
                // Verifica que el cliente existe
                $sql = "SELECT * FROM cliente WHERE idcliente = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $usuario_cliente_id);
                $stmt->execute();
                $result = $stmt->get_result();
            
                if ($result->num_rows > 0) {
                    // El cliente existe, proceder a crear el pedido
                    $sql = "INSERT INTO solicitud (cliente_idcliente) VALUES (?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $usuario_cliente_id);
                    $stmt->execute();
                    $pedido_venta_idpedido_venta = $stmt->insert_id; // Obtener el ID del nuevo pedido
                } else {
                    throw new Exception("El cliente no existe.");
                }
            }

            date_default_timezone_set('America/La_Paz');
            $fecha_venta = date("Y-m-d H:i:s");

            // Insertar la venta
            $sql = "INSERT INTO venta (usuario_idusuario, pago, fecha_venta, pedido_venta_idpedido_venta) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idsi", $usuario_idusuario, $total, $fecha_venta, $pedido_venta_idpedido_venta);
            $stmt->execute();
            $venta_id = $stmt->insert_id;

            foreach ($_SESSION['productos_seleccionados'] as $idproducto => $producto) {
                // Insertar los productos vendidos en la tabla venta_producto
                $sql = "INSERT INTO venta_producto (venta_idventa, producto_idproducto) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $venta_id, $idproducto);
                $stmt->execute();
            
                // Reducir la cantidad en el almacén
                $sql = "UPDATE almacen SET cantidad = cantidad - 1 WHERE producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();
            
                // Actualizar el estado del producto a 'agotado'
                $sql = "UPDATE almacen SET estado = 'agotado' WHERE producto_idproducto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idproducto);
                $stmt->execute();
            }
            
            $conn->commit();
            $_SESSION['productos_seleccionados'] = [];
            $total = 0;

            // Mostrar mensaje de éxito
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
    }
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
 // Crear un pedido en la tabla pedido vinculado a la solicitud
            // Crear un pedido en la tabla pedido vinculado a la solicitud y al usuario responsable
                $sql = "INSERT INTO pedido (solicitud_idsolicitud, usuario_idusuario) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $solicitud_id, $usuario_idusuario); // $usuario_idusuario viene de la sesión
                $stmt->execute();
                $pedido_id = $stmt->insert_id; // Obtener el ID del nuevo pedido


            // Insertar en pedido_venta el pedido creado
            $sql = "INSERT INTO pedido_venta (pedido_idpedido) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
// Vacía los productos seleccionados de la sesión
            unset($_SESSION['productos_seleccionados']);
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
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            echo "Error al guardar el pedido: " . $e->getMessage();
        }
        $conn->close();
    }
}


    // Aquí iría el resto de tu lógica para 'realizar_venta' y 'guardar_pedido'
}
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    PRODUCTOS SELECCIONADOS
</div>
<br>
<div class="container text-center" style="position: relative;">
    <!-- Campo de búsqueda de clientes -->
    <input type="text" id="buscar" placeholder="Buscar cliente..." class="form-control"
        value="<?php echo isset($_SESSION['idcliente']) ? obtenerNombreCliente($conn, $_SESSION['idcliente']) : ''; ?>">

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
        <h5>Total: <?php echo number_format($total, 2); ?> Bs</h5>
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
