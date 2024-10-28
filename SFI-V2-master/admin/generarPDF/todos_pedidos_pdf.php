<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php');

// Recibir los filtros del formulario
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;

$cantidad = isset($_GET['cantidad']) ? $_GET['cantidad'] : null;
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;

// Definir consulta base con cálculo de cantidad de productos
$sql = "SELECT p.idpedido, s.fecha AS fecha_pedido, 
               CONCAT(c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.apellido2_cliente) AS cliente, 
               SUM(IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio)) AS precio_total,
               s.estado,
               CONCAT(pe.nombre, ' ', pe.apellido1, ' ', pe.apellido2) AS responsable,
               v.fecha_venta,
               COUNT(pr.idproducto) AS cantidad  -- Contar la cantidad de productos en el pedido
        FROM pedido p
        JOIN solicitud s ON p.solicitud_idsolicitud = s.idsolicitud
        JOIN cliente c ON s.cliente_idcliente = c.idcliente
        JOIN producto_solicitud ps ON s.idsolicitud = ps.solicitud_idsolicitud
        JOIN producto pr ON ps.producto_idproducto = pr.idproducto
        JOIN usuario u ON p.usuario_idusuario = u.idusuario
        JOIN persona pe ON u.persona_idpersona = pe.idpersona
        LEFT JOIN venta v ON v.pedido_venta_idpedido_venta = (
            SELECT idpedido_venta FROM pedido_venta WHERE pedido_idpedido = p.idpedido LIMIT 1
        )
        WHERE 1=1";

// Aplicar filtro de estado si está presente
if (!empty($estado)) {
    $sql .= " AND s.estado = '$estado'";
}

// Aplicar filtro de fechas si están presentes
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio = $conn->real_escape_string($fecha_inicio);
    $fecha_fin = $conn->real_escape_string($fecha_fin);
    $sql .= " AND s.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

// Agrupar por pedido y calcular la cantidad total
$sql .= " GROUP BY p.idpedido, s.fecha, c.idcliente, s.estado, pe.idpersona";

// Aplicar filtros adicionales
if ($filtro === 'mayor_cantidad') {
    $sql .= " ORDER BY cantidad DESC"; // Filtrar por mayor cantidad
} elseif ($filtro === 'menor_cantidad') {
    $sql .= " ORDER BY cantidad ASC"; // Filtrar por menor cantidad
} elseif ($filtro === 'mayor_precio') {
    $sql .= " ORDER BY precio_total DESC"; // Filtrar por mayor precio
} elseif ($filtro === 'menor_precio') {
    $sql .= " ORDER BY precio_total ASC"; // Filtrar por menor precio
} elseif ($filtro === 'disponibles') {
    $sql .= " AND s.estado = 'disponible'"; // Filtrar productos disponibles
} elseif ($filtro === 'agotados') {
    $sql .= " AND s.estado = 'agotado'"; // Filtrar productos agotados
}

// Aplicar filtro por cantidad mínima si está presente
if (!empty($cantidad)) {
    $sql .= " HAVING cantidad >= $cantidad"; // Filtrar por cantidad mínima de productos
}

// Ejecutar la consulta
$result = $conn->query($sql);

// Verificar si se encontraron resultados
if ($result->num_rows == 0) {
    die('No se encontraron pedidos con los filtros aplicados.');
}

// Crear instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar el PDF
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Listado de Todos los Pedidos');
$pdf->SetSubject('Pedidos');
$pdf->SetKeywords('Pedidos, PDF');

// Establecer márgenes
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Agregar logo
$image_file = dirname(__FILE__) . '/logo/logo.jpg';
$pdf->Image($image_file, 10, 10, 60, 25, 'JPG', '', 'T', false, 30, 'L', false, false, 0, false, false, false);
$pdf->Ln(30);

// Título
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 15, 'Listado de Todos los Pedidos', 0, 1, 'C');
$pdf->Ln(10);

// Detalles de cada pedido
while ($row = $result->fetch_assoc()) {
    // Configurar el tamaño de la fuente para cada bloque de información de pedido
    $pdf->SetFont('helvetica', '', 10); // Ajustar a un tamaño de fuente más pequeño y uniforme

    // Información general del pedido
    $html = "
    <h2 style='font-size:14px;'>Información del Pedido</h2> <!-- Ajustar el tamaño del título -->
    <table border='1' cellpadding='4'>
        <tr>
            <td><strong>Fecha del Pedido:</strong></td>
            <td>{$row['fecha_pedido']}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Venta:</strong></td>
            <td>{$row['fecha_venta']}</td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong></td>
            <td>{$row['cliente']}</td>
        </tr>
        <tr>
            <td><strong>Responsable:</strong></td>
            <td>{$row['responsable']}</td>
        </tr>
        <tr>
            <td><strong>Estado:</strong></td>
            <td>{$row['estado']}</td>
        </tr>
    </table>";

    // Escribir el contenido HTML en el PDF
    $pdf->writeHTML($html, true, false, true, false, '');



    // Consultar detalles de productos agrupados
    $idpedido = $row['idpedido'];
    $sql_productos = "SELECT pr.nombre, pr.precio, pr.descuento, 
                             IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio) AS precio_con_descuento,
                             COUNT(pr.idproducto) AS cantidad_producto  -- Agrupar y contar productos individuales
                      FROM producto_solicitud ps
                      JOIN producto pr ON ps.producto_idproducto = pr.idproducto
                      WHERE ps.solicitud_idsolicitud = (SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = $idpedido)
                      GROUP BY pr.nombre, pr.precio, pr.descuento, pr.precioConDescuento"; // Agrupar productos para mostrar solo uno con su cantidad
    $result_productos = $conn->query($sql_productos);

    if ($result_productos->num_rows > 0) {
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 10); // Tamaño de fuente reducido para el título
        $pdf->Cell(0, 8, 'Detalles de Productos', 0, 1, 'C'); // Reducir altura de celda
        $pdf->Ln(5);
    
        $pdf->SetFont('helvetica', '', 8); // Reducir tamaño de fuente para el contenido
        $pdf->SetFillColor(240, 240, 240);
        // Ajuste de ancho de columnas para que sea más compacto
        $pdf->Cell(40, 8, 'Producto', 1, 0, 'C', 1);
        $pdf->Cell(20, 8, 'Cantidad', 1, 0, 'C', 1);  // Columna para cantidad
        $pdf->Cell(30, 8, 'Precio Unitario', 1, 0, 'C', 1);
        $pdf->Cell(30, 8, 'Descuento (%)', 1, 0, 'C', 1);
        $pdf->Cell(30, 8, 'Precio con Desc.', 1, 0, 'C', 1);
        $pdf->Cell(30, 8, 'Subtotal', 1, 1, 'C', 1);
    
        while ($producto = $result_productos->fetch_assoc()) {
            $subtotal = $producto['precio_con_descuento'] * $producto['cantidad_producto'];
            $pdf->Cell(40, 8, $producto['nombre'], 1);
            $pdf->Cell(20, 8, $producto['cantidad_producto'], 1);  // Mostrar cantidad del producto agrupado
            $pdf->Cell(30, 8, number_format($producto['precio'], 2) . ' BOB', 1);
            $pdf->Cell(30, 8, number_format($producto['descuento'], 2) . ' %', 1);
            $pdf->Cell(30, 8, number_format($producto['precio_con_descuento'], 2) . ' BOB', 1);
            $pdf->Cell(30, 8, number_format($subtotal, 2) . ' BOB', 1, 1);
        }
    }
    
}

$pdf->Output('Listado_Todos_Los_Pedidos.pdf', 'I');
?>
