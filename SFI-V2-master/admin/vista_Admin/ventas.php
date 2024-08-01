<?php
include_once "cabecera.php";
include_once '../modelo_admin/mod_producto.php';
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    ALMACÉN DE PRODUCTOS
</div>

<div class="btn-container">
    <a href="../generarPDF/inventario_pdf.php" target="_blank">
        <button class="btn-descargar">
            DESCARGAR DATOS   
            <i class="fi fi-rs-down-to-line"></i>
        </button>
    </a>
</div>

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__panel is-active" id="tabNewAdmin">
        <div class="full-width divider-menu-h"></div>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
                <div class="table-responsive">
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>RESPONSABLE</th>
                                <th>CLIENTE</th>
                                <th>PRODUCTO(S)</th>
                                <th>PRECIO</th>
                                <th>FACTURA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>fecha_venta</td>
                                <td>nombre y apellido1</td>
                                <td>nombre</td>
                                <td>producto(s)</td>
                                <td>precio total</td>
                                <td>
                                    <div class="btn-container">
                                        <a href="../generarPDF/inventario_pdf.php" target="_blank">
                                            <button class="btn-descargar">
                                                <i class="fi fi-rs-down-to-line"></i>
                                            </button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once "pie.php";
include_once "validaciones/val_ventas.php";
?>

