<?php

include '../../conexion.php';

$usuario_id = $_SESSION['idusuario'];


$query = "SELECT p.nombre, p.apellido1 
          FROM usuario u 
          JOIN persona p ON u.persona_idpersona = p.idpersona 
          WHERE u.idusuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario_data = $result->fetch_assoc();

$usuario_nombre = $usuario_data['nombre'];
$usuario_apellido1 = $usuario_data['apellido1'];

// Obtener los productos seleccionados y sus precios
$productos = $_POST['productos']; // Array de IDs de productos
$productos_str = implode(',', array_map('intval', $productos));
$query = "SELECT idproducto, precio FROM producto WHERE idproducto IN ($productos_str)";
$result = $conn->query($query);

$pago_total = 0;
while ($row = $result->fetch_assoc()) {
    $pago_total += $row['precio'];
}

// Paso 2: Insertar en la tabla venta
$query = "INSERT INTO venta (pago, detalle_pedido_iddetalle_pedido, fecha_venta, usuario_idusuario) VALUES (?, 1, NOW(), ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("di", $pago_total, $usuario_id);
$stmt->execute();
$venta_id = $conn->insert_id; // ID de la venta recién insertada

// Paso 3: Insertar en la tabla detalle_venta
foreach ($productos as $producto_id) {
    $query = "INSERT INTO detalle_venta (venta_idventa) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $venta_id);
    $stmt->execute();
}

// Paso 4: Opcional: Insertar información del cliente si es proporcionado
$cliente_nombre = $_POST['cliente_nombre'];
if (!empty($cliente_nombre)) {
    // Aquí puedes añadir la lógica para asociar el cliente con la venta si es necesario.
}

// Confirmar la venta y redirigir o mostrar mensaje
echo "Venta realizada con éxito.";
echo "Nombre del responsable: $usuario_nombre $usuario_apellido1";

// Cerrar la conexión
$conn->close();
?>
