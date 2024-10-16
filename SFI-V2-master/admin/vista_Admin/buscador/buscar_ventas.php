<?php
include_once "../../../conexion.php"; // Conexión a la base de datos

// Obtener el término de búsqueda desde AJAX
$busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
$busqueda = $conn->real_escape_string($busqueda); // Escapar para evitar SQL Injection

// Separar la búsqueda en palabras clave individuales
$keywords = explode(' ', $busqueda);

// Construir la consulta de búsqueda
$sql = "SELECT p.idpedido, v.fecha_venta, 
        CONCAT(c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.apellido2_cliente) AS cliente, 
        GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos, 
        precio_total, 
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
 LEFT JOIN venta_producto vp ON v.idventa = vp.venta_idventa -- Relación con la tabla venta_producto
 LEFT JOIN producto pr2 ON vp.producto_idproducto = pr2.idproducto -- Relación con producto
 -- Subconsulta para obtener el precio total
 LEFT JOIN (
     SELECT ps.solicitud_idsolicitud, 
            SUM(IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio)) AS precio_total
     FROM producto_solicitud ps
     JOIN producto pr ON ps.producto_idproducto = pr.idproducto
     GROUP BY ps.solicitud_idsolicitud
 ) AS precios ON s.idsolicitud = precios.solicitud_idsolicitud
 WHERE s.estado = 'completado' ";  // Filtro para pedidos completados


// Añadir una condición para cada palabra clave
foreach ($keywords as $word) {
    $sql .= " AND (c.nombre_cliente LIKE '%$word%' OR 
                   c.apellido_cliente LIKE '%$word%' OR 
                   c.apellido2_cliente LIKE '%$word%' OR 
                   pr.nombre LIKE '%$word%' OR 
                   pe.nombre LIKE '%$word%' OR 
                   pe.apellido1 LIKE '%$word%' OR 
                   pe.apellido2 LIKE '%$word%' OR 
                   s.estado LIKE '%$word%' OR 
                   s.fecha LIKE '%$word%' OR 
                   CAST(precio_total AS CHAR) LIKE '%$word%')";
}

$sql .= " GROUP BY p.idpedido, v.fecha_venta, cliente, s.estado, responsable, precio_total
          ORDER BY s.fecha DESC
          LIMIT 7";


$result = $conn->query($sql);

// Verificar si se obtuvieron resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['fecha_venta']) . "</td>";
        echo "<td>" . htmlspecialchars($row['responsable']) . "</td>";
        echo "<td>" . htmlspecialchars(explode(', ', $row['productos'])[0]) . "...</td>"; // Mostrar solo el primer producto
        echo "<td>" . htmlspecialchars($row['precio_total']) . "</td>";
        echo "<td>
                <div class='btn-container'>

                    <a href='editar_venta.php?id=" . urlencode($row['idventa']) . "'>
                        <button class='btn-editar btn-accion'>Editar</button>
                    </a>
                    <form action='eliminar_venta.php' method='post' onsubmit='return confirm(\"¿Estás seguro de que deseas eliminar esta venta?\");'>
                        <input type='hidden' name='idventa' value='" . urlencode($row['idventa']) . "'>
                        <button class='btn-eliminar btn-accion'>Eliminar</button>
                    </form>
                    <a href='../generarPDF/venta.php?id=" . urlencode($row['idventa']) . "' target='_blank'>
                        <button class='btn-reimprimir btn-accion'>PDF</button>
                    </a>
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
