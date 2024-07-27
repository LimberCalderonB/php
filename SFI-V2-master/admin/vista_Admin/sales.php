<?php
include_once "cabecera.php";
?>

					<div class="full-width panel-tittle bg-primary text-center tittles">
                        VENTAS 
                    </div>

<div class="full-width divider-menu-h"></div>
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
        <div class="table-responsive">
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                <thead>
                    <tr>
                        <th>FECHA Y HORA</th>
                        <th>CLIENTE</th>
                        <th>PRODUCTO</th>
                        <th>PAGO-TOTAL</th>
                        <th>VER</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>fecha_actualizacion</td>
                        <td>nombre</td>
                        <td>lista de productos</td>
                        <td>pago de los productos</td>
                        <td><button class="btn-ver">DESCARGAR</button></td>
                    </tr>
					
                </tbody>
            </table>
            <div class="btn-container">
                <button class="btn-descargar">DESCARGAR FACTURAS</button>
            </div>
        </div>
    </div>
</div>

<?php
include_once "pie.php";
?>

<style>
.centered-table th,
.centered-table td {
    text-align: center;
}

.table-responsive {
    overflow-x: auto;
}

.mdl-data-table {
    width: 100%;
    margin: auto;
}

.mdl-data-table th,
.mdl-data-table td {
    padding: 15px 30px;
}

.mdl-data-table th {
    background-color: #f2f2f2;
}

.mdl-data-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.mdl-data-table tbody tr:hover {
    background-color: #f1f1f1;
}
</style>
<style>
/* styles.css */
body {
    font-family: Arial, sans-serif;
}

.container {
    text-align: center;
    margin-top: 50px;
}

.btn-realizar-venta {
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-realizar-venta i {
    margin-right: 10px;
    font-size: 20px;
}

.btn-realizar-venta:hover {
    background-color: #218838;
}

.btn-container {
    text-align: center;
    margin-top: 20px;
}

.btn-descargar,
.btn-ver {
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 8px 16px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-descargar:hover,
.btn-ver:hover {
    background-color: #0056b3;
}

.centered-table {
    margin: 0 auto;
}
</style>

<script>
// scripts.js
document.addEventListener('DOMContentLoaded', () => {
    const ventaButton = document.getElementById('realizar-venta');
    const descargarButton = document.querySelector('.btn-descargar');
    const verButtons = document.querySelectorAll('.btn-ver');

    ventaButton.addEventListener('click', () => {
        alert('Venta realizada');
    });

    descargarButton.addEventListener('click', () => {
        alert('Facturas descargadas');
    });

    verButtons.forEach(button => {
        button.addEventListener('click', () => {
            alert('Ver detalles');
        });
    });
});
</script>

<?php
include_once "pie.php";
?>
