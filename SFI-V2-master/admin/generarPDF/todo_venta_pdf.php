<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once '../modelo_admin/mod_ventas.php';

// Recibir los datos de las ventas
$ventasDirectas = json_decode($_POST['ventasDirectas'], true);
$ventasPedidos = json_decode($_POST['ventasPedidos'], true);

// Crear nueva instancia de TCPDF
$pdf = new TCPDF();

// Establecer la información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Datos de Ventas');
$pdf->SetSubject('Informe de Ventas');
$pdf->SetKeywords('TCPDF, PDF, ventas, informe');

// Establecer la cabecera del documento
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'INFORME DE VENTAS', 'Generado por TCPDF');

// Establecer las fuentes para la cabecera y el pie de página
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Establecer las fuentes por defecto
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Establecer los márgenes del documento
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Establecer los saltos de página automáticos
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer el factor de escala de la imagen
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Añadir una página
$pdf->AddPage();

// Establecer la fuente
$pdf->SetFont('helvetica', '', 10);

// Contenido HTML para el PDF
$html = '<h1>Informe de Ventas</h1>';

// Ventas Directas
$html .= '<h2>Ventas Directas</h2>';

foreach ($ventasDirectas as $venta) {
    $html .= '<h3>Venta del ' . $venta['fecha_venta'] . '</h3>';
    $html .= '<p><strong>Responsable:</strong> ' . $venta['nombre'] . ' ' . $venta['apellido1'] . '</p>';
    $html .= '<table border="1" cellspacing="3" cellpadding="4">';
    $html .= '<thead>
                <tr>
                    <th>Productos</th>
                    <th>Precio Total</th>
                </tr>
              </thead>';
    $html .= '<tbody>';
    $html .= '<tr>
                <td>' . $venta['productos'] . '</td>
                <td>' . $venta['precio_total'] . '</td>
              </tr>';
    $html .= '</tbody></table><pagebreak />';
}

// Ventas de Pedidos
$html .= '<h2>Ventas de Pedidos</h2>';

foreach ($ventasPedidos as $venta) {
    $html .= '<h3>Venta del ' . $venta['fecha_venta'] . '</h3>';
    $html .= '<p><strong>Responsable:</strong> ' . $venta['nombre'] . ' ' . $venta['apellido1'] . '</p>';
    $html .= '<p><strong>Cliente:</strong> ' . $venta['nombre_cliente'] . '</p>';
    $html .= '<table border="1" cellspacing="3" cellpadding="4">';
    $html .= '<thead>
                <tr>
                    <th>Productos</th>
                    <th>Precio Total</th>
                </tr>
              </thead>';
    $html .= '<tbody>';
    $html .= '<tr>
                <td>' . $venta['productos'] . '</td>
                <td>' . $venta['precio_total'] . '</td>
              </tr>';
    $html .= '</tbody></table><pagebreak />';
}

// Imprimir el contenido HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('datos_ventas.pdf', 'I');
?>
