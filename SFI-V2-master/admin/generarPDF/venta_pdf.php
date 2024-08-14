<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once '../modelo_admin/mod_ventas.php';

$ventaId = $_GET['id'];

$modelo = new ModeloVentas();
$venta = $modelo->obtenerVentaPorId($ventaId);

if ($venta === null) {
    die('No se encontró la venta.');
}

$pdf = new TCPDF();

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Factura de Venta');
$pdf->SetSubject('Factura');
$pdf->SetKeywords('TCPDF, PDF, factura, venta');

// Configuración de los márgenes y encabezados
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'FACTURA DE VENTA', 'Generado por TCPDF');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

$productosHTML = '';
$totalGeneral = 0;

foreach ($venta as $item) {
    $nombreProducto = htmlspecialchars(trim($item['nombre_producto']));
    $precio = number_format($item['precio_producto'], 2);
    $descuento = number_format($item['descuento'], 2);
    $precioDescuento = number_format($item['precio_descuento'], 2);
    $total = number_format($item['total'], 2);
    $totalGeneral += $item['total'];

    $productosHTML .= "<tr>
        <td>{$nombreProducto}</td>
        <td>{$precio}</td>
        <td>{$descuento}%</td>
        <td>{$precioDescuento}</td>
        <td>{$total}</td>
    </tr>";
}

$html = <<<EOD
<style>
    body {
        font-family: helvetica, sans-serif;
        font-size: 12px;
        color: #333;
    }
    h1 {
        text-align: center;
        color: #000;
        text-transform: uppercase;
        font-size: 20px;
        margin-bottom: 20px;
    }
    .factura {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .factura th, .factura td {
        border: 1px solid #ccc;
        padding: 8px;
    }
    .factura th {
        background-color: #007BFF;
        color: #fff;
        text-align: left;
    }
    .factura td {
        text-align: left;
    }
    .info {
        margin-top: 20px;
    }
    .info h3 {
        background-color: #6d6d6d;
        color: #fff;
        padding: 10px;
        font-size: 16px;
        margin: 0;
        text-align: center;
        margin-bottom: 10px; /* Añadir espacio debajo del título */
    }
    .productos-table {
        width: 100%;
        border-collapse: collapse;
    }
    .productos-table th, .productos-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .productos-table th {
        background-color: #f8f9fa;
    }
    .productos-table td {
        border-bottom: 1px solid #ddd;
    }
    .productos-table tr:last-child td {
        border-bottom: none;
    }
    .productos-table th:nth-child(1) {
        width: 40%;  
    }
    .productos-table th:nth-child(2),
    .productos-table th:nth-child(3),
    .productos-table th:nth-child(4),
    .productos-table th:nth-child(5) {
        width: 15%; 
    }
    .total {
        font-weight: bold;
        margin-top: 20px;
    }
    .total h2 {
        font-size: 16px;
        margin: 0;
    }
    .total p {
        margin: 0;
    }
</style>

<h1>Factura de Venta</h1>
<table class="factura" cellpadding="4">
    <tr>
        <th>FECHA DE VENTA:</th>
        <td>{$venta[0]['fecha_venta']}</td>
    </tr>
    <tr>
        <th>RESPONSABLE:</th>
        <td>{$venta[0]['nombre']} {$venta[0]['apellido1']}</td>
    </tr>
</table>

<div class="info">
    <h3>PRODUCTOS</h3>
    <table class="productos-table">
        <thead>
            <tr>
                <th>Nombre del Producto</th>
                <th>Precio</th>
                <th>Des (%)</th>
                <th>Precio Descuento</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            {$productosHTML}
        </tbody>
    </table>
</div>

<div class="total">

    <h2> 
    <p>_______________________________________________________________________</p>
    <p> Precio Total: {$totalGeneral} BOB</p></h2>
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('factura_venta_' . $ventaId . '.pdf', 'I');

?>

