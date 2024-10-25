<?php
require_once('../../../tcpdf/tcpdf/tcpdf.php');
include_once('../../conexion.php');

// Recibir los filtros del formulario
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
$cantidad = isset($_GET['cantidad']) ? $_GET['cantidad'] : null;
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;

// Definir consulta base
$sql = "SELECT p.idpedido, s.fecha AS fecha_pedido, 
               CONCAT(c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.apellido2_cliente) AS cliente, 
               SUM(IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio)) AS precio_total,
               s.estado,
               CONCAT(pe.nombre, ' ', pe.apellido1, ' ', pe.apellido2) AS responsable,
               v.fecha_venta
        FROM pedido p
        JOIN solicitud s ON p.solicitud_idsolicitud = s.idsolicitud
        JOIN cliente c ON s.cliente_idcliente = c.idcliente
        JOIN producto_solicitud ps ON s.idsolicitud = ps.solicitud_idsolicitud
        JOIN producto pr ON ps.producto_idproducto = pr.idproducto
        JOIN usuario u ON p.usuario_idusuario = u.idusuario
        JOIN persona pe ON u.persona_idpersona = pe.idpersona
        LEFT JOIN venta v ON v.pedido_venta_idpedido_venta = (
            SELECT idpedido_venta FROM pedido_venta WHERE pedido_idpedido = p.idpedido LIMIT 1
        )
        WHERE 1=1";

if (!empty($estado)) {
    $sql .= " AND s.estado = '$estado'";
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio = $conn->real_escape_string($fecha_inicio);
    $fecha_fin = $conn->real_escape_string($fecha_fin);
    $sql .= " AND s.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$sql .= " GROUP BY p.idpedido, s.fecha, c.idcliente, s.estado, pe.idpersona";

// Aplicar filtros específicos
if ($filtro === 'mayor_cantidad') {
    $sql .= " ORDER BY COUNT(pr.idproducto) DESC"; // Mayor cantidad
} elseif ($filtro === 'menor_cantidad') {
    $sql .= " ORDER BY COUNT(pr.idproducto) ASC"; // Menor cantidad
} elseif ($filtro === 'mayor_precio') {
    $sql .= " ORDER BY precio_total DESC"; // Mayor precio
} elseif ($filtro === 'menor_precio') {
    $sql .= " ORDER BY precio_total ASC"; // Menor precio
} elseif ($filtro === 'disponibles') {
    $sql .= " AND s.estado = 'disponible'"; // Productos disponibles
} elseif ($filtro === 'agotados') {
    $sql .= " AND s.estado = 'agotado'"; // Productos agotados
}

// Aplicar filtro por cantidad si está presente
if (!empty($cantidad)) {
    $sql .= " HAVING COUNT(pr.idproducto) >= $cantidad"; // Filtrar por cantidad mínima de productos
}

// Ejecutar la consulta principal
$result = $conn->query($sql);

// Verificar si se encontraron resultados
if ($result->num_rows == 0) {
    die('No se encontraron pedidos con los filtros aplicados.');
}

// Crear una nueva instancia de TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar el documento PDF
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Empresa');
$pdf->SetTitle('Listado de Todos los Pedidos');
$pdf->SetSubject('Pedidos');
$pdf->SetKeywords('Pedidos, PDF');

// Establecer los márgenes
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Agregar el logo en el lado derecho superior
$image_file = dirname(__FILE__) . '/logo/logo.jpg'; // Ruta relativa de tu logo
$pdf->Image($image_file, 10, 10, 60, 25, 'JPG', '', 'T', false, 30, 'L', false, false, 0, false, false, false);

// Espacio para separar el logo del contenido
$pdf->Ln(30); // Aumenta el espacio si es necesario

// Título del documento
$pdf->SetFont('helvetica', 'B', 15);
$pdf->Cell(0, 15, 'Listado de Todos los Pedidos', 0, 1, 'C');

// Espacio
$pdf->Ln(10);

// Inicializar variables
while ($row = $result->fetch_assoc()) {
    // Estado del pedido en mayúsculas y con color
    $estado = strtoupper($row['estado']);
    $estado_color = ($estado == 'COMPLETADO') ? [124, 179, 66] : [183, 28, 28]; // Verde para completado, rojo para pendiente
    $pdf->SetFont('helvetica', '', 10);

    // Información general del pedido
    $html = "
    <h2>Información del Pedido</h2>
    <table border='1' cellpadding='4'>
        <tr>
            <td><strong>Fecha del Pedido:</strong></td>
            <td>{$row['fecha_pedido']}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Venta:</strong></td>
            <td>{$row['fecha_venta']}</td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong></td>
            <td>{$row['cliente']}</td>
        </tr>
        <tr>
            <td><strong>Responsable:</strong></td>
            <td>{$row['responsable']}</td>
        </tr>
        <tr>
            <td><strong>Estado:</strong></td>
            <td style='color: rgb({$estado_color[0]}, {$estado_color[1]}, {$estado_color[2]});'><strong>$estado</strong></td>
        </tr>
    </table>";
    
    // Escribir la primera parte del contenido en el PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Consultar los detalles de los productos asociados al pedido
    $idpedido = $row['idpedido'];
    $sql_productos = "SELECT pr.nombre, pr.precio, pr.descuento, 
                             IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio) AS precio_con_descuento
                      FROM producto_solicitud ps
                      JOIN producto pr ON ps.producto_idproducto = pr.idproducto
                      WHERE ps.solicitud_idsolicitud = (SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = $idpedido)";
    $result_productos = $conn->query($sql_productos);
    if ($result_productos->num_rows > 0) {
        // Añadir espacio y título para la sección de productos
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Detalles de Productos', 0, 1, 'C');
        $pdf->Ln(5);

        // Configurar el estilo de la tabla de productos
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(240, 240, 240); // Color de fondo para el encabezado
        $pdf->Cell(40, 10, 'Producto', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Precio Unitario', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Descuento (%)', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Precio con Descuento', 1, 0, 'C', 1);
        $pdf->Cell(35, 10, 'Subtotal', 1, 1, 'C', 1);

        // Llenar la tabla con los productos
        while ($producto = $result_productos->fetch_assoc()) {
            $subtotal = $producto['precio_con_descuento'] != '-' ? $producto['precio_con_descuento'] : $producto['precio'];
            $pdf->Cell(40, 10, $producto['nombre'], 1);
            $pdf->Cell(35, 10, number_format($producto['precio'], 2) . ' BOB', 1);
            $pdf->Cell(35, 10, number_format($producto['descuento'], 2) . ' %', 1);
            $pdf->Cell(35, 10, number_format($producto['precio_con_descuento'], 2) . ' BOB', 1);
            $pdf->Cell(35, 10, number_format($subtotal, 2) . ' BOB', 1);
            $pdf->Ln();
        }

        // Espacio después de la tabla de productos
        $pdf->Ln(5);
    }

    // Espacio antes del siguiente pedido
    $pdf->Ln(10);
}

// Salida del PDF
$pdf->Output('pedidos.pdf', 'I');
?>
