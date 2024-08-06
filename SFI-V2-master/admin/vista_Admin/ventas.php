<?php
include_once "cabecera.php";
include_once '../modelo_admin/mod_ventas.php';

$ventas = new ModeloVentas();

$ventasDirectas = $ventas->getVentasDirectas();
$ventasPedidos = $ventas->getVentasPedidos();
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    ALMACEN DE PRODUCTOS 
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

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__tab-bar">
        <a href="#tabNewAdmin" class="mdl-tabs__tab is-active">VENTAS</a>
        <a href="#tabListAdmin" class="mdl-tabs__tab">VENTA DE PEDIDOS</a>
    </div>
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
                                <th>FACTURA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ventasDirectas as $venta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($venta['fecha_venta']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['nombre'] . ' ' . $venta['apellido1']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['productos']); ?></td>
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
                </div>
            </div>
        </div>
    </div>
    <div class="mdl-tabs__panel" id="tabListAdmin">
        <div class="full-width divider-menu-h"></div>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
                <div class="table-responsive">
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                        <thead>
                            <tr>
                                <th>FECHA VENTA</th>
                                <th>RESPONSABLE</th>
                                <th>CLIENTE</th>
                                <th>PRODUCTO(S)</th>
                                <th>PRECIO TOTAL</th>
                                <th>FACTURA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ventasPedidos as $venta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($venta['fecha_venta']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['nombre'] . ' ' . $venta['apellido1']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['nombre_cliente']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['productos']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['precio_total']); ?></td>
                                    <td>
                                        <div class="btn-container">
                                            <?php if (isset($venta['idventa'])): ?>
                                                <a href="../generarPDF/venta_pedido_pdf.php?id=<?php echo urlencode($venta['idventa']); ?>" target="_blank">
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once "pie.php";
include_once 'validaciones/val_ventas.php';
?>
