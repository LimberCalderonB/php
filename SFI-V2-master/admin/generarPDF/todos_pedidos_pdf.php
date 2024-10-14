<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php'); // Conexión a la base de datos

// Consultar la base de datos para obtener todos los pedidos
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
        GROUP BY p.idpedido, s.fecha, cliente, s.estado, responsable, v.fecha_venta
        ORDER BY pe.nombre, s.estado, p.idpedido"; // Ordenar por responsable y estado

$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die('No se encontraron pedidos.');
}

// Crear una nueva instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar el documento PDF
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Listado de Todos los Pedidos');
$pdf->SetSubject('Pedidos');
$pdf->SetKeywords('Pedidos, PDF');

// Establecer los márgenes
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Título del documento
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 15, 'Listado de Todos los Pedidos', 0, 1, 'C');

// Espacio
$pdf->Ln(10);

// Establecer la fuente para el contenido
$pdf->SetFont('helvetica', '', 8);

// Inicializar la variable $html
$html = '';

// Variables para controlar el grupo de pedidos
$current_responsable = '';
$current_estado = '';

// Recorrer todos los pedidos y agruparlos por responsable y estado
while ($row = $result->fetch_assoc()) {
    // Si el responsable cambia, añadir un nuevo encabezado
    if ($current_responsable !== $row['responsable']) {
        if ($current_responsable !== '') {
            $html .= "</tbody></table><br>"; // Cierra la tabla anterior
        }
        $current_responsable = $row['responsable'];
        $html .= "<h3>Responsable: {$current_responsable}</h3>";
    }

    // Si el estado cambia, añadir un nuevo encabezado para el estado
    if ($current_estado !== $row['estado']) {
        if ($current_estado !== '') {
            $html .= "</tbody></table><br>"; // Cierra la tabla anterior
        }
        $current_estado = $row['estado'];
        $html .= "<h4>Estado: {$current_estado}</h4>";
        $html .= "
        <table border='1' cellpadding='4'>
            <thead>
                <tr>
                    <th><strong>Fecha del Pedido</strong></th>
                    <th><strong>Cliente</strong></th>
                    <th><strong>Total a Pagar</strong></th>
                    <th><strong>Fecha de Venta</strong></th>
                </tr>
            </thead>
            <tbody>";
    }

    // Agregar los datos del pedido a la tabla
    $html .= "
        <tr>
            <td>{$row['fecha_pedido']}</td>
            <td>{$row['cliente']}</td>
            <td>{$row['precio_total']} Bs</td>
            <td>{$row['fecha_venta']}</td>
        </tr>";
}

// Cerrar la última tabla
$html .= "</tbody></table>";

// Escribir la tabla en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y emitir el PDF
$pdf->Output('todos_pedidos.pdf', 'I'); // 'I' muestra el PDF en el navegador

?>
