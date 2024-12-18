<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php');

// Obtener el id de la venta desde la URL
$idventa = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idventa <= 0) {
    die('ID de venta no válido.');
}

// Consulta para obtener la información general de la venta, incluyendo el CI
$sqlVentaDirecta = "

    SELECT v.idventa, 
           v.fecha_venta, 
           p.nombre AS responsable_nombre, 
           p.apellido1 AS responsable_apellido, 
           COALESCE(cl.nombre_cliente, cl_solicitud.nombre_cliente) AS cliente_nombre, 
           COALESCE(cl.apellido_cliente, cl_solicitud.apellido_cliente) AS cliente_apellido1, 
           COALESCE(cl.apellido2_cliente, cl_solicitud.apellido2_cliente) AS cliente_apellido2, 
           COALESCE(cl.ci_cliente, cl_solicitud.ci_cliente) AS ci_cliente,  -- Asegúrate de incluir el CI aquí
           SUM(pr.precio) AS precio_total,  
           COUNT(vp.venta_idventa) AS cantidad_total  
    FROM venta v
    JOIN usuario u ON v.usuario_idusuario = u.idusuario
    JOIN persona p ON u.persona_idpersona = p.idpersona  
    LEFT JOIN cliente cl ON v.cliente_idcliente = cl.idcliente  
    LEFT JOIN pedido_venta pv ON v.pedido_venta_idpedido_venta = pv.idpedido_venta  
    LEFT JOIN pedido ped ON pv.pedido_idpedido = ped.idpedido  
    LEFT JOIN solicitud sol ON ped.solicitud_idsolicitud = sol.idsolicitud  
    LEFT JOIN cliente cl_solicitud ON sol.cliente_idcliente = cl_solicitud.idcliente  
    JOIN venta_producto vp ON v.idventa = vp.venta_idventa
    JOIN producto pr ON vp.producto_idproducto = pr.idproducto
    WHERE v.idventa = ?
    GROUP BY v.idventa
";




$stmtVentaDirecta = $conn->prepare($sqlVentaDirecta);
$stmtVentaDirecta->bind_param('i', $idventa);
$stmtVentaDirecta->execute();
$resultVentaDirecta = $stmtVentaDirecta->get_result();
$ventaDirecta = $resultVentaDirecta->fetch_assoc();

// Si no se encuentra la venta
if (!$ventaDirecta) {
    die('Venta no encontrada.');
}

// Consulta para obtener los productos de la venta, incluyendo precios y descuentos
$sqlProductos = "
    SELECT pr.nombre AS producto, COUNT(vp.producto_idproducto) AS cantidad, 
           pr.precio AS precio_producto, IFNULL(pr.descuento, 0) AS descuento,
           CASE 
               WHEN pr.descuento > 0 THEN pr.precio - (pr.precio * pr.descuento / 100) 
               ELSE pr.precio 
           END AS precio_con_descuento
    FROM venta_producto vp
    JOIN producto pr ON vp.producto_idproducto = pr.idproducto
    WHERE vp.venta_idventa = ?
    GROUP BY pr.nombre, pr.precio, pr.descuento
";

$stmtProductos = $conn->prepare($sqlProductos);
$stmtProductos->bind_param('i', $idventa);
$stmtProductos->execute();
$resultProductos = $stmtProductos->get_result();

// Si no se encuentran productos
if ($resultProductos->num_rows === 0) {
    die('No se encontraron productos para esta venta.');
}

// Inicializamos la suma total
$sumaTotal = 0;

// Crear el PDF con TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar el documento PDF
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Detalles de la Venta');
$pdf->SetSubject('Detalles de la Venta');
$pdf->SetKeywords('Venta, PDF');

// Establecer los márgenes
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Agregar el logo en el lado derecho superior
$image_file = dirname(__FILE__) . '/logo/logo.jpg'; // Cambia la ruta de tu logo si es necesario
$pdf->Image($image_file, 10, 10, 60, 25, 'JPG', '', 'T', false, 30, 'L', false, false, 0, false, false, false);

// Espacio para separar el logo del contenido
$pdf->Ln(30);

// Título del documento
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 12, 'Detalles de la Venta', 0, 10, 'C');

// Espacio
$pdf->Ln(10);

// Información general de la venta
$pdf->SetFont('helvetica', 'B', 8); // Título en negrita, tamaño 8
$html = "<h2>Información de la Venta</h2>"; // Sin estilo de tamaño

$pdf->SetFont('helvetica', '', 10); // Texto normal, tamaño 10
$html .= "
<table border='1' cellpadding='3'>
    <tr>
        <td><strong>Fecha de Venta:</strong></td>
        <td>{$ventaDirecta['fecha_venta']}</td>
    </tr>
      <tr>
        <td><strong>Cliente:</strong></td>
        <td>" . (!empty($ventaDirecta['cliente_nombre']) ? "{$ventaDirecta['cliente_nombre']} {$ventaDirecta['cliente_apellido1']} {$ventaDirecta['cliente_apellido2']}" : 'Sin Cliente') . "</td>
    </tr>
    <tr>
        <td><strong>CI:</strong></td>
        <td>" . (!empty($ventaDirecta['ci_cliente']) ? $ventaDirecta['ci_cliente'] : 'Sin CI') . "</td>
    </tr>
</table>";

$pdf->writeHTML($html, true, false, true, false, '');

// Espacio antes de la tabla de productos
$pdf->Ln(10);

// Añadir título para la sección de productos
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Detalles de Productos', 0, 1, 'C');
$pdf->Ln(5);

// Configurar el estilo de la tabla de productos
$pdf->SetFont('helvetica', '', 9); // Cambié a tamaño 9 para los textos

// Crear la tabla de productos con las funciones nativas de TCPDF
$pdf->SetFillColor(240, 240, 240); // Color de fondo para el encabezado
$pdf->Cell(40, 8, 'Producto', 1, 0, 'C', 1);
$pdf->Cell(20, 8, 'Cantidad', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Precio Unitario', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Descuento (%)', 1, 0, 'C', 1);
$pdf->Cell(32, 8, 'Precio con Descuento', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Subtotal', 1, 1, 'C', 1);

// Llenar la tabla con los productos
while ($producto = $resultProductos->fetch_assoc()) {
    $subtotal = $producto['precio_con_descuento'] * $producto['cantidad'];
    $sumaTotal += $subtotal;

    $pdf->Cell(40, 8, $producto['producto'], 1);
    $pdf->Cell(20, 8, $producto['cantidad'], 1);
    $pdf->Cell(30, 8, number_format($producto['precio_producto'], 2) . ' BOB', 1);
    $pdf->Cell(30, 8, number_format($producto['descuento'], 2) . '%', 1);
    $pdf->Cell(32, 8, number_format($producto['precio_con_descuento'], 2) . ' BOB', 1);
    $pdf->Cell(30, 8, number_format($subtotal, 2) . ' BOB', 1, 1);
}

// Espacio antes del total
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 10); // Cambié a tamaño 10 para el total
$pdf->Cell(50, 10, 'Total de la Venta: ' . number_format($sumaTotal, 2) . ' BOB', 0, 1, 'R');

// Cerrar y emitir el PDF
$pdf->Output('venta_' . $idventa . '.pdf', 'I');
?>
