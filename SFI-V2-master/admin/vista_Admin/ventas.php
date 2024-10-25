<?php
include_once "cabecera.php";
include_once "../../conexion.php";

// Capturar fechas de inicio y fin
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Si se establece una fecha de fin, le sumamos un día para incluir todo el día
if ($fechaInicio && $fechaFin) {
    $fechaFin = date('Y-m-d', strtotime($fechaFin . ' +1 day'));
}

// Configuración de paginación
$limit = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Agregar condiciones de filtro de fecha en las consultas SQL
$fechaCondicion = "";
if ($fechaInicio && $fechaFin) {
    $fechaCondicion = "WHERE v.fecha_venta BETWEEN ? AND ?";
}

// Obtener ventas directas con filtro de fecha
$sqlDirectas = "
    SELECT v.idventa, 
           v.fecha_venta, 
           p.nombre AS responsable_nombre, 
           p.apellido1 AS responsable_apellido, 
           COALESCE(cl.nombre_cliente, cl_solicitud.nombre_cliente) AS cliente_nombre, 
           COALESCE(cl.apellido_cliente, cl_solicitud.apellido_cliente) AS cliente_apellido1, 
           COALESCE(cl.apellido2_cliente, cl_solicitud.apellido2_cliente) AS cliente_apellido2, 
           GROUP_CONCAT(DISTINCT pr.nombre SEPARATOR ', ') AS productos, 
           SUM(pr.precio) AS precio_total,  -- Total del precio de los productos
           COUNT(vp.venta_idventa) AS cantidad_total  -- Contar la cantidad de productos en la venta
    FROM venta v
    JOIN usuario u ON v.usuario_idusuario = u.idusuario
    JOIN persona p ON u.persona_idpersona = p.idpersona  -- Responsable de la venta
    LEFT JOIN cliente cl ON v.cliente_idcliente = cl.idcliente  -- Cliente directo de la venta
    LEFT JOIN pedido_venta pv ON v.pedido_venta_idpedido_venta = pv.idpedido_venta  -- Relación con pedido_venta
    LEFT JOIN pedido ped ON pv.pedido_idpedido = ped.idpedido  -- Relación con pedido
    LEFT JOIN solicitud sol ON ped.solicitud_idsolicitud = sol.idsolicitud  -- Relación con solicitud
    LEFT JOIN cliente cl_solicitud ON sol.cliente_idcliente = cl_solicitud.idcliente  -- Cliente de la solicitud (pedido)
    JOIN venta_producto vp ON v.idventa = vp.venta_idventa
    JOIN producto pr ON vp.producto_idproducto = pr.idproducto
    $fechaCondicion
    GROUP BY v.idventa
    ORDER BY v.fecha_venta DESC
    LIMIT ?, ?";

$stmtDirectas = $conn->prepare($sqlDirectas);

if ($fechaInicio && $fechaFin) {
    $stmtDirectas->bind_param('ssii', $fechaInicio, $fechaFin, $offset, $limit);
} else {
    $stmtDirectas->bind_param('ii', $offset, $limit);
}

$stmtDirectas->execute();
$resultDirectas = $stmtDirectas->get_result();
$ventasDirectas = $resultDirectas->fetch_all(MYSQLI_ASSOC);

// Obtener total de ventas directas
$sqlTotalDirectas = "SELECT COUNT(*) as total FROM venta v $fechaCondicion";
$stmtTotalDirectas = $conn->prepare($sqlTotalDirectas);

if ($fechaInicio && $fechaFin) {
    $stmtTotalDirectas->bind_param('ss', $fechaInicio, $fechaFin);
}
$stmtTotalDirectas->execute();
$totalVentasDirectas = $stmtTotalDirectas->get_result()->fetch_assoc()['total'];
$totalPagesDirectas = ceil($totalVentasDirectas / $limit);

// Paginación con fechas
?>

<?php
$masVendidos = isset($_GET['mas_vendidos']) && $_GET['mas_vendidos'] == 'true';

if ($masVendidos) {
    // Consulta para obtener los productos más vendidos agrupados por nombre, talla, precio y categoría
    $sqlMasVendidos = "
        SELECT 
            pr.nombre AS producto_nombre, 
            pr.talla AS producto_talla, 
            pr.precio AS producto_precio, 
            c.nombre AS categoria_nombre, 
            COUNT(vp.producto_idproducto) AS cantidad_vendida
        FROM venta_producto vp
        JOIN producto pr ON vp.producto_idproducto = pr.idproducto
        JOIN almacen a ON a.producto_idproducto = pr.idproducto
        JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
        JOIN venta v ON vp.venta_idventa = v.idventa
        GROUP BY pr.nombre, pr.talla, pr.precio, c.nombre
        ORDER BY cantidad_vendida DESC";

    // Ejecutar la consulta
    $stmtMasVendidos = $conn->prepare($sqlMasVendidos);
    $stmtMasVendidos->execute();
    $productosMasVendidos = $stmtMasVendidos->get_result()->fetch_all(MYSQLI_ASSOC);
}
$sqlClientesLeales = "
    SELECT 
        cl.nombre_cliente AS nombre,
        cl.apellido_cliente AS apellido1,
        cl.apellido2_cliente AS apellido2,
        cl.ci_cliente AS ci,
        COUNT(vp.producto_idproducto) AS total_productos
    FROM cliente cl
    JOIN venta v ON cl.idcliente = v.cliente_idcliente
    JOIN venta_producto vp ON v.idventa = vp.venta_idventa
    GROUP BY cl.idcliente
    ORDER BY total_productos DESC
    LIMIT 10"; // Puedes ajustar el límite según sea necesario

$stmtClientesLeales = $conn->prepare($sqlClientesLeales);
$stmtClientesLeales->execute();
$clientesLeales = $stmtClientesLeales->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    P R O D U C T O S - V E N D I D O S
</div>
<br>
<!-- Botones como cartas -->
<div class="menu-container">
    <div class="card card-menos-vendidos" onclick="location.href='ventas.php'">
        <h3>Todas las Ventas</h3>
        <i class="fi fi-sr-globe"></i>
    </div>
    <div class="card card-completados" onclick="location.href='ventas.php?mas_vendidos=true'">
        <h3>Listar Productos Más Vendidos</h3>
        <i class="fi fi-sr-chart-mixed-up-circle-dollar"></i>
    </div>

    <!--<div class="card card-pendientes" onclick="location.href='ventas.php?leales=true'">
        <h3>Listar Clientes Más Leales</h3>
        <i class="fi fi-ss-cart-minus"></i>
    </div>-->
</div>

<div class="search-container text-center">
    <form method="GET" action="">
        <input type="text" name="busqueda" class="search-input" placeholder="Buscador..." value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>" />
    </form>
</div>

<!-- Filtro de Fechas -->
<div class="container row">
    <div class="filter-container col-12">
        <form method="get" action="ventas.php" class="filter-form">
            <div class="date-filter-container text-left">
                <label for="fecha_inicio">Desde:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="date-input" value="<?php echo htmlspecialchars($fechaInicio); ?>" />
                <label for="fecha_fin">Hasta:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="date-input" value="<?php echo isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : ''; ?>" />
                <button type="submit" class="btn-filter">Filtrar</button>
            </div>
        </form>
    </div>

    <!-- Botón de Exportar -->
    <div class="btn-container col-12">
        <form method="post" action="../generarPDF/todo_venta_pdf.php" target="_blank">
            <input type="hidden" name="ventasDirectas" value="<?php echo htmlspecialchars(json_encode($ventasDirectas)); ?>">
            <input type="hidden" name="ventasPedidos" value="<?php echo htmlspecialchars(json_encode($ventasPedidos)); ?>">
            <button type="submit" class="btn-exportar">Detalles PDF</button>
        </form>
    </div>
</div>

<?php
// Verificar y mostrar tabla de productos más vendidos
if ($masVendidos) {
    echo '<div class="full-width panel-tittle bg-primary text-center tittles">Productos Más Vendidos</div>';
    echo '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">';  // Contenedor con scroll
    echo '<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table">';
    echo '<thead><tr><th>Categoría</th><th>Nombre</th><th>Talla</th><th>Precio</th><th>Cantidad Vendida</th></tr></thead><tbody>';

    foreach ($productosMasVendidos as $producto) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($producto['categoria_nombre']) . '</td>';
        echo '<td>' . htmlspecialchars($producto['producto_nombre']) . '</td>';
        echo '<td>' . htmlspecialchars($producto['producto_talla']) . '</td>';
        echo '<td>' . htmlspecialchars($producto['producto_precio']) . '</td>';
        echo '<td>' . htmlspecialchars($producto['cantidad_vendida']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>'; // Fin de table-responsive
}

// Mostrar tabla de ventas directas
if (!$masVendidos) {
    echo '<div class="full-width panel-tittle bg-primary text-center tittles">Ventas Directas</div>';
    echo '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">';  // Contenedor con scroll
    echo '<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table">';
    echo '<thead><tr><th>Fecha</th><th>Responsable</th><th>Cliente</th><th>Productos</th><th>Precio Total</th><th>Cantidad Total</th><th>Detalles</th></tr></thead><tbody>';

    foreach ($ventasDirectas as $venta) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($venta['fecha_venta']) . '</td>';
        echo '<td>' . htmlspecialchars($venta['responsable_nombre'] . ' ' . $venta['responsable_apellido']) . '</td>';
        echo '<td>' . htmlspecialchars($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido1'] . ' ' . $venta['cliente_apellido2']) . '</td>';
        echo '<td>' . htmlspecialchars($venta['productos']) . '</td>';
        echo '<td>' . htmlspecialchars($venta['precio_total']) . '</td>';
        echo '<td>' . htmlspecialchars($venta['cantidad_total']) . '</td>';
        echo '<td><a href="../generarPDF/venta_pdf.php?id=' . htmlspecialchars($venta['idventa']) . '" class="btn btn-info" style="background-color: #17a2b8; border-color: #17a2b8; padding: 5px 10px; border-radius: 5px; transition: background-color 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2); font-weight: bold; font-size: 14px; color: white; text-decoration: none;"> <i class="fas fa-file-pdf"></i> Detalle</a></td>'; // Botón de detalles
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>'; // Fin de table-responsive
}
?>

<!-- Controles de Paginación para Ventas Directas -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPagesDirectas; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&fecha_inicio=<?php echo urlencode($fechaInicio); ?>&fecha_fin=<?php echo urlencode(date('Y-m-d', strtotime($fechaFin . ' -1 day'))); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </div>
<?php
include_once "pie.php";
include_once "validaciones/val_ventas.php";
?>

<script>
function confirmDelete(idventa) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esta acción!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Si se confirma, enviar el formulario
            document.getElementById('eliminar-venta-' + idventa).submit();
        }
    });
}
</script>