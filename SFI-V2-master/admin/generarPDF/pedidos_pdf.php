<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php'); // Incluir conexión a la base de datos

// Verificar si se recibió el idpedido
if (!isset($_GET['idpedido'])) {
    die('No se ha proporcionado un idpedido.');
}

$idpedido = intval($_GET['idpedido']); // Obtener el ID del pedido desde la URL

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

// Crear una nueva instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar el documento PDF
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Detalles del Pedido');
$pdf->SetSubject('Detalles del Pedido');
$pdf->SetKeywords('Pedido, PDF');

// Establecer los márgenes
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Título del documento
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 15, 'Detalles del Pedido', 0, 1, 'C');

// Espacio
$pdf->Ln(10);

// Información del pedido general
$pdf->SetFont('helvetica', '', 12);
$html = "
<h2>Información del Pedido</h2>
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
        <td><strong>Total a Pagar:</strong></td>
        <td>{$row['precio_total']} Bs</td>
    </tr>
    <tr>
        <td><strong>Estado:</strong></td>
        <td>{$row['estado']}</td>
    </tr>
    <tr>
        <td><strong>Responsable:</strong></td>
        <td>{$row['responsable']}</td>
    </tr>
</table>";

// Escribir la primera parte del contenido en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Consultar los detalles de los productos asociados al pedido
$sql_productos = "SELECT pr.nombre, 
                         pr.precio, 
                         pr.descuento,
                         IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, '-') AS precio_con_descuento
                  FROM producto_solicitud ps
                  JOIN producto pr ON ps.producto_idproducto = pr.idproducto
                  WHERE ps.solicitud_idsolicitud = (SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = $idpedido)";

$result_productos = $conn->query($sql_productos);
if ($result_productos->num_rows > 0) {
    // Añadir espacio y título para la sección de productos
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Detalles de Productos', 0, 1, 'C');
    $pdf->Ln(5);

    // Construir la tabla de productos con descuento
    $pdf->SetFont('helvetica', '', 12);
    $html_productos = "
    <table border='1' cellpadding='4'>
        <thead>
            <tr>
                <th><strong>Producto</strong></th>
                <th><strong>Precio Unitario</strong></th>
                <th><strong>Descuento (%)</strong></th>
                <th><strong>Precio con Descuento</strong></th>
                <th><strong>Subtotal</strong></th>
            </tr>
        </thead>
        <tbody>
    ";

    // Agregar los productos a la tabla
    while ($producto = $result_productos->fetch_assoc()) {
        // Calcular el subtotal basado en el precio con descuento (si existe)
        $subtotal = $producto['precio_con_descuento'] != '-' ? $producto['precio_con_descuento'] : $producto['precio'];
        $html_productos .= "
            <tr>
                <td>{$producto['nombre']}</td>
                <td>{$producto['precio']} Bs</td>
                <td>{$producto['descuento']}%</td>
                <td>{$producto['precio_con_descuento']} Bs</td>
                <td>$subtotal Bs</td>
            </tr>";
    }

    $html_productos .= "</tbody></table>";

    // Escribir la tabla de productos en el PDF
    $pdf->writeHTML($html_productos, true, false, true, false, '');
}

// Cerrar y emitir el PDF
$pdf->Output('pedido_' . $idpedido . '.pdf', 'I'); // 'I' muestra el PDF en el navegador

?>
