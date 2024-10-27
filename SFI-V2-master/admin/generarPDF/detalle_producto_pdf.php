<?php 
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once "../../conexion.php";

// Verificar si se ha pasado el ID del producto
if (!isset($_GET['idproducto'])) {
    die('ID de producto no especificado');
}

$idproducto = $_GET['idproducto'];

// Consultar los detalles del producto
$sql = "SELECT 
            p.nombre, 
            p.descripcion, 
            p.precio, 
            p.precioConDescuento, 
            p.talla, 
            IFNULL(p.descuento, 0) AS descuento,
            c.nombre AS categoria_nombre, 
            SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
            IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado,
            MAX(p.fecha_actualizacion) AS fecha_actualizacion
        FROM producto p
        JOIN almacen a ON p.idproducto = a.producto_idproducto
        JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
        WHERE p.nombre = (SELECT nombre FROM producto WHERE idproducto = ?)
            AND p.precio = (SELECT precio FROM producto WHERE idproducto = ?)
            AND p.precioConDescuento = (SELECT precioConDescuento FROM producto WHERE idproducto = ?)
            AND p.talla = (SELECT talla FROM producto WHERE idproducto = ?)
            AND p.descuento = (SELECT descuento FROM producto WHERE idproducto = ?)
        GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre";


            

$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiii', $idproducto, $idproducto, $idproducto, $idproducto, $idproducto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();


if (!$producto) {
    die('Producto no encontrado');
}

// Crear PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Detalle del Producto');

// Configurar márgenes y agregar una página
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// Añadir logo
$image_file = dirname(__FILE__) . '/logo/logo.jpg';
$pdf->Image($image_file, 10, 10, 60, 25, 'JPG', '', 'T', false, 300, 'L', false, false, 0, false, false, false);

// Título centrado
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(100, 30, 'Detalle del Producto', 0, 1, 'C'); // Alineación centrada
$pdf->Ln(10);

// Mostrar categoría y fecha de registro
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'Categoría: ' . $producto['categoria_nombre'], 0, 1);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 6, 'Fecha de registro: ' . $producto['fecha_actualizacion'], 0, 1);
$pdf->Ln(5);

// Nombre del producto
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'Nombre: ' . $producto['nombre'], 0, 1);

// Descripción del producto
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(0, 6, 'Descripción: ' . $producto['descripcion'], 0, 'L', 0, 1);
$pdf->Ln(5);

// Detalles en tabla (Ajustar el tamaño de las celdas)
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(30, 8, 'Precio', 1);
$pdf->Cell(30, 8, 'Precio c/Descuento', 1);
$pdf->Cell(20, 8, 'Talla', 1);
$pdf->Cell(30, 8, 'Descuento', 1);
$pdf->Cell(35, 8, 'Cant. Disponible', 1);
$pdf->Cell(30, 8, 'Estado', 1);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(30, 8, number_format($producto['precio'], 2), 1);
$pdf->Cell(30, 8, number_format($producto['precioConDescuento'], 2), 1);
$pdf->Cell(20, 8, $producto['talla'], 1);
$pdf->Cell(30, 8, $producto['descuento'] . '%', 1);
$pdf->Cell(35, 8, $producto['cantidad_disponible'], 1);

$estado_color = ($producto['estado'] === 'disponible') ? [0, 128, 0] : [255, 0, 0];
$pdf->SetTextColor($estado_color[0], $estado_color[1], $estado_color[2]);
$pdf->Cell(30, 8, ucfirst($producto['estado']), 1, 1);
$pdf->SetTextColor(0, 0, 0); // Reset color

// Salida del PDF
$pdf->Output('Detalle_Producto_' . $producto['nombre'] . '.pdf', 'I');
?>
