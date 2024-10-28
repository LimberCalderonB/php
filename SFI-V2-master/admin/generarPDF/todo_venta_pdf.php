<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php');

// Parámetros para la paginación
$offset = 0;  // Ajustar según sea necesario
$limit = 10;  // Cantidad de resultados por página

// Consulta para ventas directas (excluyendo las que pertenecen a pedidos)
$sqlDirectas = "
SELECT v.idventa, v.fecha_venta, p.nombre AS nombre_usuario, p.apellido1 AS apellido_usuario, 
           c.apellido_cliente, c.apellido2_cliente, c.nombre_cliente, c.ci_cliente,
           GROUP_CONCAT(DISTINCT pr.nombre ORDER BY pr.nombre SEPARATOR ', ') AS productos, 
           SUM(pr.precio) AS precio_total
    FROM venta v
    JOIN usuario u ON v.usuario_idusuario = u.idusuario
    JOIN persona p ON u.persona_idpersona = p.idpersona
    LEFT JOIN cliente c ON v.cliente_idcliente = c.idcliente
    JOIN venta_producto vp ON v.idventa = vp.venta_idventa
    JOIN producto pr ON vp.producto_idproducto = pr.idproducto
    WHERE v.pedido_venta_idpedido_venta IS NULL
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
    SELECT v.idventa, v.fecha_venta, p.nombre AS nombre_usuario, p.apellido1 AS apellido_usuario, 
           c.apellido_cliente, c.apellido2_cliente, c.nombre_cliente, c.ci_cliente,
           GROUP_CONCAT(DISTINCT pr.nombre ORDER BY pr.nombre SEPARATOR ', ') AS productos, 
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
$image_file = dirname(__FILE__).'/logo/logo.jpg';
$pdf->Image($image_file, 10, 10, 60, 25, 'JPG', '', 'T', false, 30, 'L', false, false, false);
$pdf->Ln(30);

// Agregar contenido de ventas directas
if (!empty($ventasDirectas)) {
    $pdf->Cell(0, 10, 'Ventas Directas', 0, 1, 'C');
    foreach ($ventasDirectas as $ventaDirecta) {
        $htmlVentaDirecta = '
            <h4>Detalles de la Venta Directa</h4>
            <table cellpadding="5">
                <tr>
                    <th>Fecha Venta:</th>
                    <td>' . htmlspecialchars($ventaDirecta['fecha_venta']) . '</td>
                </tr>
                <tr>
                    <th>Cliente:</th>
                    <td>' . htmlspecialchars($ventaDirecta['nombre_cliente']) . ' ' . htmlspecialchars($ventaDirecta['apellido_cliente']) . '</td>
                </tr>
                <tr>
                    <th>CI Cliente:</th>
                    <td>' . htmlspecialchars($ventaDirecta['ci_cliente']) . '</td>
                </tr>
            </table>
            <br>
            <h4>Productos de la Venta</h4>
            <table border="1" cellpadding="5" class="tabla-ventas">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Descuento (%)</th>
                        <th>Precio con Descuento</th>
                        <th>Total Producto</th>
                    </tr>
                </thead>
                <tbody>
        ';

        // Consulta para obtener los productos relacionados con la venta directa
        $sqlProductosDirectos = "
            SELECT pr.nombre AS producto, COUNT(vp.producto_idproducto) AS cantidad, 
                   pr.precio AS precio_producto, IFNULL(pr.descuento, 0) AS descuento, 
                   CASE WHEN pr.descuento > 0 THEN pr.precio - (pr.precio * pr.descuento / 100) 
                        ELSE pr.precio END AS precio_con_descuento
            FROM venta_producto vp
            JOIN producto pr ON vp.producto_idproducto = pr.idproducto
            WHERE vp.venta_idventa = ?
            GROUP BY pr.nombre, pr.talla, pr.descuento 
        ";

        $stmtProductosDirectos = $conn->prepare($sqlProductosDirectos);
        $stmtProductosDirectos->bind_param('i', $ventaDirecta['idventa']);
        $stmtProductosDirectos->execute();
        $resultProductosDirectos = $stmtProductosDirectos->get_result();

        $totalVentaDirecta = 0;

        while ($producto = $resultProductosDirectos->fetch_assoc()) {
            $totalProducto = $producto['precio_con_descuento'] * $producto['cantidad'];
            $totalVentaDirecta += $totalProducto;

            $htmlVentaDirecta .= '
                <tr>
                    <td>' . htmlspecialchars($producto['producto']) . '</td>
                    <td>' . htmlspecialchars($producto['cantidad']) . '</td>
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
        $htmlVentaPedido = '
            <h4>Detalles de la Venta Asociada a Pedido</h4>
            <table cellpadding="5">
                <tr>
                    <th>Fecha Venta:</th>
                    <td>' . htmlspecialchars($ventaPedido['fecha_venta']) . '</td>
                </tr>
                <tr>
                    <th>Cliente:</th>
                    <td>' . htmlspecialchars($ventaPedido['nombre_cliente']) . ' ' . htmlspecialchars($ventaPedido['apellido_cliente']) . ' ' . htmlspecialchars($ventaPedido['apellido2_cliente']) . '</td>
                </tr>
                <tr>
                    <th>CI Cliente:</th>
                    <td>' . htmlspecialchars($ventaPedido['ci_cliente']) . '</td>
                </tr>
            </table>
            <br>
            <h4>Productos de la Venta</h4>
            <table border="1" cellpadding="5" class="tabla-ventas">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Descuento (%)</th>
                        <th>Precio con Descuento</th>
                        <th>Total Producto</th>
                    </tr>
                </thead>
                <tbody>
        ';

        // Consulta para obtener los productos relacionados con la venta asociada a pedido
        $sqlProductosPedidos = "
            SELECT pr.nombre AS producto, COUNT(ps.producto_idproducto) AS cantidad, 
           pr.precio AS precio_producto, IFNULL(pr.descuento, 0) AS descuento, 
           CASE WHEN pr.descuento > 0 THEN pr.precio - (pr.precio * pr.descuento / 100) 
                ELSE pr.precio END AS precio_con_descuento
    FROM venta_producto vp
    JOIN producto pr ON vp.producto_idproducto = pr.idproducto
    JOIN pedido_venta pv ON vp.venta_idventa = pv.pedido_idpedido
    JOIN pedido p ON pv.pedido_idpedido = p.idpedido
    JOIN producto_solicitud ps ON p.idpedido = ps.solicitud_idsolicitud
    WHERE vp.venta_idventa = ?
    GROUP BY pr.nombre, pr.talla, pr.descuento 
        ";

        $stmtProductosPedidos = $conn->prepare($sqlProductosPedidos);
        $stmtProductosPedidos->bind_param('i', $ventaPedido['idventa']);
        $stmtProductosPedidos->execute();
        $resultProductosPedidos = $stmtProductosPedidos->get_result();

        $totalVentaPedido = 0;

        while ($producto = $resultProductosPedidos->fetch_assoc()) {
            $totalProducto = $producto['precio_con_descuento'] * $producto['cantidad'];
            $totalVentaPedido += $totalProducto;

            $htmlVentaPedido .= '
                <tr>
                    <td>' . htmlspecialchars($producto['producto']) . '</td>
                    <td>' . htmlspecialchars($producto['cantidad']) . '</td>
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

// Cerrar la conexión
$conn->close();

// Cerrar y generar el PDF
$pdf->Output('informe_ventas.pdf', 'I');
?>
