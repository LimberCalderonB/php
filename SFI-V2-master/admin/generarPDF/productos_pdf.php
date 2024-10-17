<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once "../../conexion.php";

// Crear un nuevo documento PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Inventario de Productos');

// Configurar márgenes
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);

// Agregar una página
$pdf->AddPage();

// Título del documento
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Reporte de Inventario de Productos', 0, 1, 'C');
$pdf->Ln(5); // Salto de línea

// Columnas de la tabla
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(30, 10, 'Nombre', 1);
$pdf->Cell(25, 10, 'Categoría', 1);
$pdf->Cell(20, 10, 'Talla', 1);
$pdf->Cell(20, 10, 'Precio', 1);
$pdf->Cell(20, 10, 'Descuento', 1);
$pdf->Cell(25, 10, 'Estado', 1);
$pdf->Cell(25, 10, 'Cantidad', 1);
$pdf->Ln(); // Salto de línea para la siguiente fila

// Consultar los productos de la base de datos
$sql = "SELECT 
            p.nombre, 
            p.precio, 
            p.precioConDescuento, 
            p.talla, 
            IFNULL(p.descuento, 0) AS descuento, 
            c.nombre AS categoria_nombre, 
            SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
            IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
        FROM producto p
        JOIN almacen a ON p.idproducto = a.producto_idproducto
        JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
        GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
        ORDER BY p.nombre ASC";

$result = $conn->query($sql);

// Mostrar los datos en la tabla del PDF
$pdf->SetFont('helvetica', '', 10);
while ($producto = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $producto['nombre'], 1);
    $pdf->Cell(25, 10, $producto['categoria_nombre'], 1);
    $pdf->Cell(20, 10, $producto['talla'], 1);
    $pdf->Cell(20, 10, number_format($producto['precio'], 2), 1);
    $pdf->Cell(20, 10, $producto['descuento'] . '%', 1);
    $pdf->Cell(25, 10, ucfirst($producto['estado']), 1);
    $pdf->Cell(25, 10, $producto['cantidad_disponible'], 1);
    $pdf->Ln();
}

// Output del PDF
$pdf->Output('Inventario_Productos.pdf', 'I');
?>
