<?php
include_once "cabecera.php";
include_once '../modelo_admin/mod_ventas.php';

$ventas = new ModeloVentas();

// Configuración de paginación para ventas directas
$limit = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$ventasDirectas = $ventas->getVentasDirectas($offset, $limit);
$totalVentasDirectas = $ventas->getTotalVentasDirectas();
$totalPagesDirectas = ceil($totalVentasDirectas / $limit);

// Configuración de paginación para ventas de pedidos
$ventasPedidos = $ventas->getVentasPedidos($offset, $limit);
$totalVentasPedidos = $ventas->countVentasPedidos();
$totalPagesPedidos = ceil($totalVentasPedidos / $limit);
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    P R O D U C T O S - V E N D I D O S
</div>

<div class="btn-container">
    <form method="post" action="../generarPDF/todo_venta_pdf.php" target="_blank">
        <input type="hidden" name="ventasDirectas" value="<?php echo htmlspecialchars(json_encode($ventasDirectas)); ?>">
        <input type="hidden" name="ventasPedidos" value="<?php echo htmlspecialchars(json_encode($ventasPedidos)); ?>">
        <button type="submit" class="btn-descargar">
            DESCARGAR DATOS   
            <i class="fi fi-rs-down-to-line"></i>
        </button>
    </form>
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
                                <th>CLIENTE</th>
                                <th>PRECIO TOTAL</th>
                                <th>FACTURA</th>
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
                                    <td>nombre de cliente</td>
                                    <td><?php echo htmlspecialchars($venta['precio_total']); ?></td>
                                    <td>
                                        <div class="btn-container">
                                            <?php if (isset($venta['idventa'])): ?>
                                                <a href="../generarPDF/venta_pdf.php?id=<?php echo urlencode($venta['idventa']); ?>" target="_blank">
                                                    <button class="btn-descargar">
                                                        <i class="fi fi-rs-down-to-line"></i>
                                                    </button>
                                                </a>
                                            <?php else: ?>
                                                <span>Sin ID</span>
                                            <?php endif; ?>
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
        </div>
    </div>
<?php
include_once "pie.php";
include_once 'validaciones/val_ventas.php';
?>
