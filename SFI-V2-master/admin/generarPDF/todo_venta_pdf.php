<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php');

// Parámetros para la paginación
$offset = 0;  // Ajustar según sea necesario
$limit = 10;  // Cantidad de resultados por página

// Consulta para ventas directas
$sqlDirectas = "
    SELECT v.idventa, v.fecha_venta, p.nombre, p.apellido1, 
           GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos, 
           SUM(pr.precio) AS precio_total
    FROM venta v
    JOIN usuario u ON v.usuario_idusuario = u.idusuario
    JOIN persona p ON u.persona_idpersona = p.idpersona
    JOIN venta_producto vp ON v.idventa = vp.venta_idventa
    JOIN producto pr ON vp.producto_idproducto = pr.idproducto
    GROUP BY v.idventa
    ORDER BY v.fecha_venta DESC
    LIMIT ?, ?
";

$stmtDirectas = $conn->prepare($sqlDirectas);
$stmtDirectas->bind_param('ii', $offset, $limit);
$stmtDirectas->execute();
$resultDirectas = $stmtDirectas->get_result();
$ventasDirectas = $resultDirectas->fetch_all(MYSQLI_ASSOC);

// Consulta para ventas asociadas a pedidos
$sqlPedidos = "
    SELECT v.idventa, v.fecha_venta, p.nombre, p.apellido1, c.nombre_cliente, 
           GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos, 
           SUM(pr.precio) AS precio_total
    FROM venta v
    JOIN usuario u ON v.usuario_idusuario = u.idusuario
    JOIN persona p ON u.persona_idpersona = p.idpersona
    JOIN pedido_venta pv ON v.pedido_venta_idpedido_venta = pv.idpedido_venta
    JOIN pedido ped ON pv.pedido_idpedido = ped.idpedido
    JOIN solicitud s ON ped.solicitud_idsolicitud = s.idsolicitud
    JOIN cliente c ON s.cliente_idcliente = c.idcliente
    JOIN producto_solicitud ps ON s.idsolicitud = ps.solicitud_idsolicitud
    JOIN producto pr ON ps.producto_idproducto = pr.idproducto
    GROUP BY v.idventa
    ORDER BY v.fecha_venta DESC
    LIMIT ?, ?
";

$stmtPedidos = $conn->prepare($sqlPedidos);
$stmtPedidos->bind_param('ii', $offset, $limit);
$stmtPedidos->execute();
$resultPedidos = $stmtPedidos->get_result();
$ventasPedidos = $resultPedidos->fetch_all(MYSQLI_ASSOC);

// Generar el PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Informe de Ventas');
$pdf->SetSubject('Ventas');
$pdf->SetKeywords('TCPDF, PDF, ventas, informe');

// Configurar el PDF
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Añadir una página
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Agregar el logo en la parte superior izquierda
$image_file = dirname(__FILE__).'/logo/logo.jpg'; // Cambia la ruta de tu logo si es necesario
$pdf->Image($image_file, 10, 10, 60, 25, 'JPG', '', 'T', false, 30, 'L', false, false, 0, false, false, false);
$pdf->Ln(30);
// Agregar contenido de ventas directas
if (!empty($ventasDirectas)) {
    $pdf->Cell(0, 10, 'Ventas Directas', 0, 1, 'C');
    foreach ($ventasDirectas as $ventaDirecta) {
        // Mostrar los detalles generales de la venta
        $htmlVentaDirecta = '
            <h3>Detalles de la Venta Directa</h3>
            <table cellpadding="5">
                <tr>
                    <th>Fecha Venta:</th>
                    <td>' . htmlspecialchars($ventaDirecta['fecha_venta']) . '</td>
                </tr>
                <tr>
                    <th>Responsable:</th>
                    <td>' . htmlspecialchars($ventaDirecta['nombre']) . ' ' . htmlspecialchars($ventaDirecta['apellido1']) . '</td>
                </tr>
            </table>
            <br>
            <h4>Productos de la Venta</h4>
            <table border="1" cellpadding="5" class="tabla-ventas">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Descuento (%)</th>
                        <th>Precio con Descuento</th>
                        <th>Total Producto</th>
                    </tr>
                </thead>
                <tbody>
        ';

        // Consulta para obtener los productos relacionados con la venta directa.
        $sqlProductosDirectos = "
            SELECT pr.nombre AS producto, pr.precio AS precio_producto, 
                   IFNULL(pr.descuento, 0) AS descuento, 
                   CASE 
                       WHEN pr.descuento > 0 THEN pr.precio - (pr.precio * pr.descuento / 100) 
                       ELSE pr.precio 
                   END AS precio_con_descuento 
            FROM venta_producto vp
            JOIN producto pr ON vp.producto_idproducto = pr.idproducto
            WHERE vp.venta_idventa = ?
        ";

        $stmtProductosDirectos = $conn->prepare($sqlProductosDirectos);
        $stmtProductosDirectos->bind_param('i', $ventaDirecta['idventa']);
        $stmtProductosDirectos->execute();
        $resultProductosDirectos = $stmtProductosDirectos->get_result();

        $totalVentaDirecta = 0;  // Variable para el total de la venta

        while ($producto = $resultProductosDirectos->fetch_assoc()) {
            // Calcular el total del producto
            $totalProducto = $producto['precio_con_descuento'];
            $totalVentaDirecta += $totalProducto;  // Sumar al total de la venta

            // Añadir filas a la tabla de productos
            $htmlVentaDirecta .= '
                <tr>
                    <td>' . htmlspecialchars($producto['producto']) . '</td>
                    <td>' . number_format($producto['precio_producto'], 2) . ' BOB</td>
                    <td>' . number_format($producto['descuento'], 2) . ' %</td>
                    <td>' . number_format($producto['precio_con_descuento'], 2) . ' BOB</td>
                    <td>' . number_format($totalProducto, 2) . ' BOB</td>
                </tr>
            ';
        }

        $htmlVentaDirecta .= '
                </tbody>
            </table>
            <br>
            <h4>Total de la Venta: ' . number_format($totalVentaDirecta, 2) . ' BOB</h4>
        ';

        $pdf->writeHTML($htmlVentaDirecta, true, false, true, false, '');
    }
}

// Agregar contenido de ventas asociadas a pedidos
if (!empty($ventasPedidos)) {
    $pdf->Cell(0, 10, 'Ventas Asociadas a Pedidos', 0, 1, 'C');
    foreach ($ventasPedidos as $ventaPedido) {
        // Mostrar los detalles generales de la venta
        $htmlVentaPedido = '
            <h3>Detalles de la Venta Asociada a Pedido</h3>
            <table cellpadding="5">
                <tr>
                    <th>Fecha Venta:</th>
                    <td>' . htmlspecialchars($ventaPedido['fecha_venta']) . '</td>
                </tr>
                <tr>
                    <th>Responsable:</th>
                    <td>' . htmlspecialchars($ventaPedido['nombre']) . ' ' . htmlspecialchars($ventaPedido['apellido1']) . '</td>
                </tr>
                <tr>
                    <th>Cliente:</th>
                    <td>' . htmlspecialchars($ventaPedido['nombre_cliente']) . '</td>
                </tr>
            </table>
            <br>
            <h4>Productos de la Venta</h4>
            <table border="1" cellpadding="5" class="tabla-ventas">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Descuento (%)</th>
                        <th>Precio con Descuento</th>
                        <th>Total Producto</th>
                    </tr>
                </thead>
                <tbody>
        ';

        // Consulta para obtener los productos relacionados con la venta asociada al pedido.
        $sqlProductosPedido = "
            SELECT pr.nombre AS producto, pr.precio AS precio_producto, 
                   IFNULL(pr.descuento, 0) AS descuento, 
                   CASE 
                       WHEN pr.descuento > 0 THEN pr.precio - (pr.precio * pr.descuento / 100) 
                       ELSE pr.precio 
                   END AS precio_con_descuento 
            FROM venta_producto vp
            JOIN producto pr ON vp.producto_idproducto = pr.idproducto
            WHERE vp.venta_idventa = ?
        ";

        $stmtProductosPedido = $conn->prepare($sqlProductosPedido);
        $stmtProductosPedido->bind_param('i', $ventaPedido['idventa']);
        $stmtProductosPedido->execute();
        $resultProductosPedido = $stmtProductosPedido->get_result();

        $totalVentaPedido = 0;  // Variable para el total de la venta

        while ($producto = $resultProductosPedido->fetch_assoc()) {
            // Calcular el total del producto
            $totalProducto = $producto['precio_con_descuento'];
            $totalVentaPedido += $totalProducto;  // Sumar al total de la venta

            // Añadir filas a la tabla de productos
            $htmlVentaPedido .= '
                <tr>
                    <td>' . htmlspecialchars($producto['producto']) . '</td>
                    <td>' . number_format($producto['precio_producto'], 2) . ' BOB</td>
                    <td>' . number_format($producto['descuento'], 2) . ' %</td>
                    <td>' . number_format($producto['precio_con_descuento'], 2) . ' BOB</td>
                    <td>' . number_format($totalProducto, 2) . ' BOB</td>
                </tr>
            ';
        }

        $htmlVentaPedido .= '
                </tbody>
            </table>
            <br>
            <h4>Total de la Venta: ' . number_format($totalVentaPedido, 2) . ' BOB</h4>
        ';

        $pdf->writeHTML($htmlVentaPedido, true, false, true, false, '');
    }
}

// Output del PDF
$pdf->Output('informe_ventas.pdf', 'I');
?>
