<?php
include_once "../../../conexion.php"; // Asegúrate de incluir tu conexión a la base de datos

session_start();

// Verificamos si existe la sesión de productos seleccionados, si no, la creamos
if (!isset($_SESSION['productos_seleccionados'])) {
    $_SESSION['productos_seleccionados'] = []; // Inicializa como un arreglo vacío si no existe
}

if (isset($_POST['atender_pedido'])) {
    $idpedido = $_POST['idpedido'];

    // Consulta para obtener los productos y el cliente asociados al pedido
    $sql = "SELECT p.idpedido, s.idsolicitud, c.idcliente, c.nombre_cliente, c.apellido_cliente, c.apellido2_cliente, 
                   pr.idproducto, pr.nombre, pr.precio, pr.talla, pr.descuento, ct.nombre AS categoria_nombre
            FROM pedido p
            JOIN solicitud s ON p.solicitud_idsolicitud = s.idsolicitud
            JOIN cliente c ON s.cliente_idcliente = c.idcliente
            JOIN producto_solicitud ps ON s.idsolicitud = ps.solicitud_idsolicitud
            JOIN producto pr ON ps.producto_idproducto = pr.idproducto
            JOIN almacen a ON pr.idproducto = a.producto_idproducto
            JOIN categoria ct ON a.categoria_idcategoria = ct.idcategoria
            WHERE p.idpedido = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idpedido);
    $stmt->execute();
    $result = $stmt->get_result();

    // Asignar los datos del cliente antes de procesar los productos
    $cliente_asignado = false; // Variable para comprobar si el cliente se asignó correctamente

    // Procesar los productos obtenidos y el cliente en la primera fila
    while ($producto = $result->fetch_assoc()) {
        if (!$cliente_asignado) {
            // Asignar los datos del cliente solo una vez
            $_SESSION['idcliente'] = $producto['idcliente'];
            $_SESSION['nombre_cliente'] = $producto['nombre_cliente'] . ' ' . $producto['apellido_cliente'] . ' ' . $producto['apellido2_cliente'];
            $_SESSION['idpedido'] = $producto['idpedido'];
            $_SESSION['idsolicitud'] = $producto['idsolicitud'];
            $cliente_asignado = true;
        }

        $producto_id = $producto['idproducto'];

        // Verificamos si el producto ya está en la sesión
        if (!array_key_exists($producto_id, $_SESSION['productos_seleccionados'])) {
            // Añadir el producto a la sesión de productos seleccionados
            $_SESSION['productos_seleccionados'][$producto_id] = [
                'idproducto' => $producto_id,
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'talla' => $producto['talla'],
                'descuento' => $producto['descuento'],
                'categoria_nombre' => $producto['categoria_nombre'] // Nombre de la categoría correcto
            ];
        }
    }

    // Verificar si se asignó un cliente
    if ($cliente_asignado) {
        // Redirigir a la vista de pagos
        header("Location: ../pagos.php");
        exit();
    } else {
        // Manejar el caso en que no se encuentra el pedido
        echo "Pedido no encontrado.";
    }
} else {
    // Manejar el caso en que no se envía el formulario
    echo "No se ha enviado el formulario.";
}
?>
