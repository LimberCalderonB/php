<?php
include_once "cabecera.php";
include_once "../../conexion.php";

// Configuración de paginación
$limit = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Obtener ventas directas
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

// Obtener total de ventas directas
$totalVentasDirectas = $conn->query("SELECT COUNT(*) as total FROM venta")->fetch_assoc()['total'];
$totalPagesDirectas = ceil($totalVentasDirectas / $limit);

// Obtener ventas asociadas a pedidos
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

// Contar el total de ventas asociadas a pedidos
$totalVentasPedidos = $conn->query("
    SELECT COUNT(DISTINCT v.idventa) AS total
    FROM venta v
    JOIN pedido_venta pv ON v.pedido_venta_idpedido_venta = pv.idpedido_venta
    JOIN pedido ped ON pv.pedido_idpedido = ped.idpedido
    JOIN solicitud s ON ped.solicitud_idsolicitud = s.idsolicitud
")->fetch_assoc()['total'];

$totalPagesPedidos = ceil($totalVentasPedidos / $limit);
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    P R O D U C T O S - V E N D I D O S
</div>
<br>
<!-- Botones como cartas -->
<div class="menu-container">
    <div class="card card-menos-vendidos" onclick="location.href='ventas.php'">
        <h3>Todos los Pedidos</h3>
        <i class="fi fi-sr-globe"></i>
    </div>
    <div class="card card-completados" onclick="location.href='ventas.php'">
        <h3>Productos Mas Vendidos</h3>
        <i class="fi fi-sr-shopping-cart-add"></i>
    </div>
    <div class="card card-pendientes" onclick="location.href='productos_menos_vendidos.php'">
        <h3>Productos Menos Vendidos</h3>
        <i class="fi fi-ss-cart-minus"></i>
    </div>
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
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="date-input" />
                <label for="fecha_fin">Hasta:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="date-input" />
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

<!-- Tabla de Ventas Directas -->
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__panel is-active" id="tabNewAdmin">
        <div class="full-width divider-menu-h"></div>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
                <div class="table-responsive">
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                        <thead>
                            <tr>
                                <th>FECHA VENTA</th>
                                <th>RESPONSABLE</th>
                                <th>PRODUCTO(S)</th>
                                <th>PRECIO TOTAL</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ventasDirectas as $venta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($venta['fecha_venta']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['nombre'] . ' ' . $venta['apellido1']); ?></td>
                                    <td>
                                        <?php
                                            $productos = explode(', ', $venta['productos']);
                                            echo htmlspecialchars($productos[0]);
                                            if(count($productos) > 1) {
                                                echo '...';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($venta['precio_total']); ?></td>
                                    <td>
                                        <div class="btn-container">
                                            <!-- Editar Venta -->
                                            <a href="editar_venta.php?id=<?php echo urlencode($venta['idventa']); ?>">
                                                <button class="btn-editar btn-accion">Editar</button>
                                            </a>
                                            
                                            <!-- Eliminar Venta -->
                                            <form action="eliminar_venta.php" method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta venta?');">
                                                <input type="hidden" name="idventa" value="<?php echo urlencode($venta['idventa']); ?>">
                                                <button class="btn-eliminar btn-accion">Eliminar</button>
                                            </form>
                                            
                                            <!-- Reimprimir Recibo -->
                                            <a href="../generarPDF/venta_pdf.php?id=<?php echo urlencode($venta['idventa']); ?>" target="_blank">
                                                <button class="btn-reimprimir btn-accion">PDF</button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Controles de Paginación para Ventas Directas -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPagesDirectas; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
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

