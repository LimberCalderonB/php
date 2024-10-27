<?php
include_once "../../../conexion.php"; // Conexión a la base de datos

// Obtener el término de búsqueda desde AJAX
$busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
$busqueda = $conn->real_escape_string($busqueda); // Escapar para evitar SQL Injection

// Separar la búsqueda en palabras clave individuales
$keywords = explode(' ', $busqueda);

// Construir la consulta de búsqueda
$sql = "SELECT p.idpedido, s.fecha AS fecha_pedido, v.fecha_venta, c.ci_cliente,
        CONCAT(c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.apellido2_cliente) AS cliente, 
        GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos, 
        precio_total,
        COUNT(pr.idproducto) AS cantidad_productos,  -- Contar productos
        s.estado,
        CONCAT(pe.nombre, ' ', pe.apellido1, ' ', pe.apellido2) AS responsable
 FROM pedido p
 JOIN solicitud s ON p.solicitud_idsolicitud = s.idsolicitud
 JOIN cliente c ON s.cliente_idcliente = c.idcliente
 JOIN producto_solicitud ps ON s.idsolicitud = ps.solicitud_idsolicitud
 JOIN producto pr ON ps.producto_idproducto = pr.idproducto
 JOIN usuario u ON p.usuario_idusuario = u.idusuario
 JOIN persona pe ON u.persona_idpersona = pe.idpersona
 LEFT JOIN venta v ON v.pedido_venta_idpedido_venta = (
     SELECT idpedido_venta 
     FROM pedido_venta 
     WHERE pedido_idpedido = p.idpedido
     LIMIT 1
 )
 -- Subconsulta para obtener el precio total
 LEFT JOIN (
     SELECT ps.solicitud_idsolicitud, 
            SUM(IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio)) AS precio_total
     FROM producto_solicitud ps
     JOIN producto pr ON ps.producto_idproducto = pr.idproducto
     GROUP BY ps.solicitud_idsolicitud
 ) AS precios ON s.idsolicitud = precios.solicitud_idsolicitud
 WHERE 1=1";

// Añadir una condición para cada palabra clave
foreach ($keywords as $word) {
    $sql .= " AND (c.nombre_cliente LIKE '%$word%' OR 
                   c.apellido_cliente LIKE '%$word%' OR 
                   c.apellido2_cliente LIKE '%$word%' OR 
                   c.ci_cliente LIKE '%$word%' OR 
                   pr.nombre LIKE '%$word%' OR 
                   s.estado LIKE '%$word%' OR 
                   s.fecha LIKE '%$word%' OR 
                   v.fecha_venta LIKE '%$word%' OR 
                   CAST(precio_total AS CHAR) LIKE '%$word%')";
}

$sql .= " GROUP BY p.idpedido, s.fecha, v.fecha_venta, cliente, s.estado, responsable, precio_total
          ORDER BY s.fecha DESC
          LIMIT 7";


$result = $conn->query($sql);

// Verificar si se obtuvieron resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['fecha_pedido'] . "</td>"; // Fecha del pedido
        echo "<td>" . $row['fecha_venta'] . "</td>"; 
        echo "<td>" . $row['cliente'] . "</td>";
        echo "<td>" . $row['ci_cliente'] . "</td>";
        echo "<td>" . obtenerProductos($row['idpedido'], $conn) . "</td>";
        echo "<td>" . $row['cantidad_productos'] . "</td>";
        echo "<td>" . $row['precio_total'] . "</td>";
        echo "<td class='" . ($row['estado'] == 'pendiente' ? 'estado-pendiente' : 'estado-completado') . "'>" . $row['estado'] . "</td>";
        echo "<td>
                <div style='display: flex; gap: 5px;'>
                    <form method='POST' action='pedidos/atender_pedido.php'>
                        <input type='hidden' name='idpedido' value='" . $row['idpedido'] . "'>
                        <button type='submit' name='atender_pedido' class='btn-accion btn-editar'>Atender</button>
                    </form>
                    <form action='pedidos/cancelar_pedido.php' method='POST' style='display:inline;' onsubmit='return confirmCancel(event, this);'>
                        <input type='hidden' name='idpedido' value='" . $row['idpedido'] . "'>
                        <button type='submit' name='cancelar_pedido' class='btn-accion btn-eliminar'>Anular</button>
                    </form>
                    <a href='#' class='btn-accion btn-detalles'>Detalles</a>
                </div>
            </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No se encontraron resultados.</td></tr>";
}

// Función para obtener productos asociados
function obtenerProductos($idpedido, $conn) {
    $sql = "SELECT pr.nombre 
            FROM producto_solicitud ps
            JOIN producto pr ON ps.producto_idproducto = pr.idproducto
            WHERE ps.solicitud_idsolicitud = (
                SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idpedido);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row['nombre'];
    }
    
    if (count($productos) > 1) {
        return $productos[0] . '...';
    } else if (count($productos) === 1) {
        return $productos[0];
    } else {
        return 'Sin productos';
    }
}

?>
