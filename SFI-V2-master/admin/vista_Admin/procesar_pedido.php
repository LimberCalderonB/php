<?php
include_once '../../conexion.php'; // Conexión a la base de datos

// Obtener los datos enviados desde el frontend
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['nombre']) && isset($data['cantidad'])) {
    $nombreProducto = $data['nombre'];
    $cantidadSolicitada = (int) $data['cantidad'];
    $categoriaProducto = $data['categoria'];
    $tallaProducto = (int) $data['talla'];  // Asegurar que la talla sea un número entero

    // Consultar los productos agrupados por nombre, categoría, y talla
    $query = "SELECT a.producto_idproducto, a.cantidad, a.estado
              FROM almacen a
              INNER JOIN producto p ON a.producto_idproducto = p.idproducto
              INNER JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
              WHERE p.nombre = ? AND c.nombre = ? AND p.talla = ? AND a.estado IN ('disponible', 'casi_vendido')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $nombreProducto, $categoriaProducto, $tallaProducto);
    $stmt->execute();
    $result = $stmt->get_result();

    $productos = [];
    while ($producto = $result->fetch_assoc()) {
        $productos[] = $producto;
    }

    // Calcular la cantidad total disponible
    $cantidadDisponibleTotal = array_sum(array_column($productos, 'cantidad'));

    // Verificar si hay suficiente cantidad disponible
    if ($cantidadSolicitada <= $cantidadDisponibleTotal) {
        $cantidadRestante = $cantidadSolicitada;

        foreach ($productos as $producto) {
            if ($cantidadRestante > 0) {
                $cantidadProducto = (int) $producto['cantidad'];
                $idProducto = $producto['producto_idproducto'];

                // Restar la cantidad procesada del producto actual
                if ($cantidadRestante <= $cantidadProducto) {
                    $nuevaCantidad = $cantidadProducto - $cantidadRestante;
                    $cantidadRestante = 0; // Se procesó toda la solicitud
                } else {
                    $nuevaCantidad = 0;
                    $cantidadRestante -= $cantidadProducto;
                }

                // Si el producto se está agotando, mantener la cantidad en 1 en lugar de 0
                $nuevaCantidad = max($nuevaCantidad, 1);

                // Actualizar el estado del producto si se agotó (pero mantiene cantidad en 1)
                $nuevoEstado = ($nuevaCantidad == 1) ? 'espera' : 'disponible';

                // Actualizar la cantidad en la base de datos
                $updateQuery = "UPDATE almacen SET cantidad = ?, estado = ? WHERE producto_idproducto = ?";
                $stmtUpdate = $conn->prepare($updateQuery);
                $stmtUpdate->bind_param("isi", $nuevaCantidad, $nuevoEstado, $idProducto);
                $stmtUpdate->execute();
            }
        }

        // Enviar la cantidad que realmente fue procesada de vuelta al frontend
        echo json_encode(['success' => true, 'cantidadEnviada' => $cantidadSolicitada]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No hay suficiente cantidad disponible']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
?>
