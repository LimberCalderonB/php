
<?php
include_once "cabecera.php";
?>
	<div class="full-width panel mdl-shadow--2dp">
		<div class="full-width panel-tittle bg-primary text-center tittles">
		LISTA DE PEDIDOS
	</div>


	<div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
                <div class="table-responsive">
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                        
                        <tbody>
                                <tr>
                                    <td>fecha_venta</td>
                                    <td>nombre</td>
                                    <td>productos</td>
                                    <td>precio_total</td>
									<td>estado</td>
                                    <td>
                                        <div class="btn-container">
                                                <a href="#" target="_blank">
                                                    <button class="btn-descargar">Realizar Pedido
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
<?php
include_once "pie.php";
include_once 'validaciones/val_ventas.php';
?>