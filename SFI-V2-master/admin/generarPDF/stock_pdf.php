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

// Agregar el logo
$image_file = dirname(__FILE__).'/logo/logo.jpg'; // Cambia la ruta de tu logo si es necesario
$pdf->Image($image_file, 10, 10, 30, 25, 'JPG', '', 'T', false, 300, 'L', false, false, 0, false, false, false);

// Título del documento
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 30, 'Reporte de Productos', 0, 1, 'C');
$pdf->Ln(5); // Salto de línea

// Consultar los productos de la base de datos
$sql = "SELECT 
            p.nombre, 
            p.descripcion, 
            p.precio, 
            p.precioConDescuento, 
            p.fecha_actualizacion, 
            p.talla, 
            IFNULL(p.descuento, 0) AS descuento, 
            c.nombre AS categoria_nombre, 
            SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
            IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
        FROM producto p
        JOIN almacen a ON p.idproducto = a.producto_idproducto
        JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
        GROUP BY p.nombre, p.descripcion, p.precio, p.precioConDescuento, p.talla, c.nombre
        ORDER BY c.nombre ASC, p.nombre ASC";

$result = $conn->query($sql);

// Mostrar los datos
$pdf->SetFont('helvetica', '', 10);
$current_category = '';

while ($producto = $result->fetch_assoc()) {
    // Verificar si cambia de categoría para mostrarla como título
    if ($producto['categoria_nombre'] != $current_category) {
        $current_category = $producto['categoria_nombre'];
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Categoría: ' . $current_category, 0, 1, 'L');
    }

    // Mostrar nombre del producto
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 10, 'Producto: ' . $producto['nombre'], 0, 1, 'L');

    // Mostrar descripción del producto
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 10, 'Descripción: ' . $producto['descripcion'], 0, 'L', 0, 1);

    // Mostrar fecha de registro
    $pdf->Cell(0, 10, 'Fecha de Registro: ' . date('d/m/Y', strtotime($producto['fecha_actualizacion'])), 0, 1, 'L');

    // Colores para la tabla
    $pdf->SetFillColor(224, 235, 255); // Color de fondo de las celdas
    $pdf->SetTextColor(0); // Color de texto negro

    // Tabla con los detalles del producto
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(30, 10, 'Talla', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Precio', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Descuento', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Precio con Descuento', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Estado', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Cantidad', 1, 1, 'C', 1);

    // Detalles del producto en la tabla
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(30, 10, $producto['talla'], 1);
    $pdf->Cell(30, 10, number_format($producto['precio'], 2), 1);
    $pdf->Cell(20, 10, $producto['descuento'] . '%', 1);
    $pdf->Cell(40, 10, number_format($producto['precioConDescuento'], 2), 1);

    // Colores para el estado del producto
    if ($producto['estado'] == 'disponible') {
        $pdf->SetTextColor(0, 128, 0); // Verde para disponible
    } else {
        $pdf->SetTextColor(255, 0, 0); // Rojo para agotado
    }
    $pdf->Cell(30, 10, ucfirst($producto['estado']), 1, 0, 'C');
    
    // Restablecer color de texto a negro para la siguiente celda
    $pdf->SetTextColor(0);
    $pdf->Cell(30, 10, $producto['cantidad_disponible'], 1, 1);
    $pdf->Ln(5); // Salto de línea entre productos
}

// Output del PDF
$pdf->Output('Inventario_Productos.pdf', 'I');
?>
