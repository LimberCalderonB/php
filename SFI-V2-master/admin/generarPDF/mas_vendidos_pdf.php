<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php');

// Decodifica los datos recibidos en JSON
$productosMasVendidos = json_decode($_POST['productosMasVendidos'], true);

// Crea una clase personalizada que extiende TCPDF
class MYPDF extends TCPDF {
    public function Header() {
        // Ruta del logo
        $image_file = dirname(__FILE__) . '/logo/logo.jpg'; // Cambia la ruta de tu logo si es necesario
        $this->Image($image_file, 10, 10, 40, 20, 'JPG', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
        
        // Espacio después del logo
        $this->SetY(35); // Posiciona el título debajo del logo

        // Título del documento
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'Reporte de Productos Más Vendidos', 0, 1, 'C');
        $this->Ln(5); // Añade espacio después del título
    }
    
    public function Footer() {
        // Posición en la parte inferior
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Crear nuevo PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Reporte de Productos Más Vendidos');
$pdf->SetMargins(15, 45, 10); // Ajuste de margen superior para evitar encimar con el encabezado
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->AddPage();

// Configura la fuente y el color
$pdf->SetFont('helvetica', '', 10);
$pdf->SetFillColor(224, 235, 255);  // Color de fondo para filas

// Encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Categoría', 1, 0, 'C', 1);
$pdf->Cell(60, 10, 'Nombre', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Talla', 1, 0, 'C', 1);
$pdf->Cell(20, 10, 'Precio', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Cantidad Vendida', 1, 1, 'C', 1);

// Filas de datos
$pdf->SetFont('helvetica', '', 10);
$fill = false;

foreach ($productosMasVendidos as $producto) {
    $pdf->Cell(40, 10, $producto['categoria_nombre'], 1, 0, 'C', $fill);
    $pdf->Cell(60, 10, $producto['producto_nombre'], 1, 0, 'C', $fill);
    $pdf->Cell(20, 10, $producto['producto_talla'], 1, 0, 'C', $fill);
    $pdf->Cell(20, 10, number_format($producto['producto_precio'], 2), 1, 0, 'C', $fill);
    $pdf->Cell(40, 10, $producto['cantidad_vendida'], 1, 1, 'C', $fill);
    $fill = !$fill;  // Alterna color de fondo
}

// Salida del PDF
$pdf->Output('productos_mas_vendidos.pdf', 'I');
?>
