<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php');

// Verificar si se recibió el idpedido
if (!isset($_GET['idpedido'])) {
    die('No se ha proporcionado un idpedido.');
}
$idpedido = intval($_GET['idpedido']);

// Consultar la base de datos para obtener los detalles del pedido
$sql = "SELECT p.idpedido, s.fecha AS fecha_pedido,
                CONCAT(c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.apellido2_cliente) AS cliente,
                SUM(IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio)) AS precio_total,
                s.estado,
                CONCAT(pe.nombre, ' ', pe.apellido1, ' ', pe.apellido2) AS responsable,
                v.fecha_venta
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
        WHERE p.idpedido = $idpedido
        GROUP BY p.idpedido, s.fecha, cliente, s.estado, responsable, v.fecha_venta";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die('No se encontraron detalles del pedido.');
}
$row = $result->fetch_assoc();

// Crear el PDF y configuraciones iniciales
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Detalles del Pedido');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// Agregar el logo
$image_file = dirname(__FILE__).'/logo/logo.jpg';
$pdf->Image($image_file, 10, 10, 60, 25, 'JPG', '', 'T', false, 30, 'L', false, false, 0, false, false, false);
$pdf->Ln(30);

// Título y detalles del pedido
$pdf->SetFont('helvetica', 'B', 10); // Reducir tamaño de letra
$pdf->Cell(0, 15, 'Detalles del Pedido', 0, 1, 'C');
$pdf->Ln(10);

// Información del pedido en formato HTML
$estado = strtoupper($row['estado']);
$estado_color = ($estado == 'COMPLETADO') ? [124, 179, 66] : [183, 28, 28];
$html = "<h4>Información del Pedido</h4> <!-- Cambiado de h2 a h3 para reducir tamaño -->
<table border='1' cellpadding='3'> <!-- Reducir el cellpadding -->
    <tr><td><strong>Fecha del Pedido:</strong></td><td>{$row['fecha_pedido']}</td></tr>
    <tr><td><strong>Fecha de Venta:</strong></td><td>{$row['fecha_venta']}</td></tr>
    <tr><td><strong>Cliente:</strong></td><td>{$row['cliente']}</td></tr>
    <tr><td><strong>Responsable:</strong></td><td>{$row['responsable']}</td></tr>
    <tr><td><strong>Estado:</strong></td><td style='color: rgb({$estado_color[0]}, {$estado_color[1]}, {$estado_color[2]});'><strong>$estado</strong></td></tr>
</table>";
$pdf->writeHTML($html, true, false, true, false, '');

// Cambia el tamaño de la fuente antes de agregar la tabla de información
$pdf->SetFont('helvetica', '', 7);

// Consultar los detalles de los productos agrupados
$sql_productos = "SELECT pr.nombre, pr.precio, pr.descuento,
                         COUNT(pr.idproducto) AS cantidad,
                         IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, '-') AS precio_con_descuento
                  FROM producto_solicitud ps
                  JOIN producto pr ON ps.producto_idproducto = pr.idproducto
                  WHERE ps.solicitud_idsolicitud = (SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = $idpedido)
                  GROUP BY pr.nombre, pr.precio, pr.descuento, pr.precioConDescuento";
$result_productos = $conn->query($sql_productos);
if ($result_productos->num_rows > 0) {
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Detalles de Productos', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 9); // Reducir tamaño de letra
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(40, 10, 'Producto', 1, 0, 'C', 1);
    $pdf->Cell(25, 10, 'Cantidad', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Precio Unitario', 1, 0, 'C', 1);
    $pdf->Cell(25, 10, 'Descuento (%)', 1, 0, 'C', 1);
    $pdf->Cell(35, 10, 'Precio con Desc.', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Subtotal', 1, 1, 'C', 1);

    while ($producto = $result_productos->fetch_assoc()) {
        $subtotal = ($producto['precio_con_descuento'] != '-' ? $producto['precio_con_descuento'] : $producto['precio']) * $producto['cantidad'];
        $pdf->Cell(40, 10, $producto['nombre'], 1);
        $pdf->Cell(25, 10, $producto['cantidad'], 1);
        $pdf->Cell(30, 10, number_format($producto['precio'], 2) . ' BOB', 1);
        $pdf->Cell(25, 10, number_format($producto['descuento'], 2) . '%', 1);
        $pdf->Cell(35, 10, $producto['precio_con_descuento'] . ' BOB', 1);
        $pdf->Cell(30, 10, number_format($subtotal, 2) . ' BOB', 1, 1);
    }

    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(45, 10, 'Total a Pagar: ' . number_format($row['precio_total'], 2) . ' BOB', 0, 1, 'R'); // Mover el total a pagar al extremo derecho
}

$pdf->Output('pedido_' . $idpedido . '.pdf', 'I');
?>
