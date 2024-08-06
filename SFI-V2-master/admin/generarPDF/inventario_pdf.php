<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once '../modelo_admin/mod_producto.php';

$modelo = new ModeloProducto();
$productosConDescuento = $modelo->obtenerProductosConDescuento();
$productosSinDescuento = $modelo->obtenerProductosSinDescuento();

// Crear una nueva instancia de TCPDF
$pdf = new TCPDF();

// Establecer la información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Datos de Productos');
$pdf->SetSubject('Informe de Productos');
$pdf->SetKeywords('TCPDF, PDF, productos, informe');

// Establecer la cabecera del documento
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'INFORME DE PRODUCTOS', 'Generado por TCPDF');

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
$pdf->SetFont('helvetica', '', 7.8);

// Contenido HTML para el PDF
$html = <<<EOD
<style>
    h1 {
        text-align: center;
        color: #000000;
        text-transform: uppercase;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    th {
        background-color: #007BFF;
        color: white;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
    }
    td {
        text-align: center;
    }
    table, th, td {
        border: 1px solid black;
    }
</style>

<h1>Productos Sin Descuento</h1>
<table cellpadding="4">
    <thead>
        <tr>
            <th>FECHA Y HORA</th>
            <th>NOMBRE</th>
            <th>CATEGORIA</th>
            <th>TALLA</th>
            <th>PRECIO</th>
            <th>CANTIDAD</th>
        </tr>
    </thead>
    <tbody>
EOD;

foreach ($productosSinDescuento as $producto) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($producto['fecha_actualizacion']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['categoria_nombre']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['talla']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['precio']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['cantidad']) . '</td>';
    $html .= '</tr>';
}

$html .= <<<EOD
    </tbody>
</table>

<h1>Productos Con Descuento</h1>
<table cellpadding="4">
    <thead>
        <tr>
            <th>FECHA Y HORA</th>
            <th>NOMBRE</th>
            <th>CATEGORIA</th>
            <th>TALLA</th>
            <th>PRECIO</th>
            <th>DESCUENTO</th>
            <th>P.DESCUENTO</th>
            <th>CANTIDAD</th>
        </tr>
    </thead>
    <tbody>
EOD;

foreach ($productosConDescuento as $producto) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($producto['fecha_actualizacion']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['categoria_nombre']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['talla']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['precio']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['descuento']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['precioConDescuento']) . '</td>';
    $html .= '<td>' . htmlspecialchars($producto['cantidad']) . '</td>';
    $html .= '</tr>';
}

$html .= <<<EOD
    </tbody>
</table>
EOD;

// Imprimir el contenido HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('productos.pdf', 'I');
?>
