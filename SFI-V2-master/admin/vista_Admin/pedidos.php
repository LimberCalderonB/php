<?php
include_once "cabecera.php";
?>
<div class="full-width panel mdl-shadow--2dp">
    <div class="full-width panel-tittle bg-primary text-center tittles">
        LISTA DE PEDIDOS
    </div>

    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <div class="table-responsive">
                <div class="btn-container">
                    <a href="crear_pedido.php" class="btn-descargar">Crear Pedido</a>
                </div>

                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp centered-table">
                    <thead>
                        <tr>
                            <th>Fecha de Pedido</th>
                            <th>Responsable</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Precio Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2024-10-01</td>
                            <td>Juan Pérez</td>
                            <td>María Gómez</td>
                            <td>Producto A, Producto B</td>
                            <td>$100.00</td>
                            <td>Completado</td>
                            <td>
                                <a href="#" class="btn-accion btn-editar">Editar</a>
                                <a href="#" class="btn-accion btn-eliminar">Cancelar</a>
                                <a href="#" class="btn-accion btn-detalles">Detalles</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include_once "pie.php";

?>

<!-- Estilos CSS -->
<style>
    .btn-container {
        padding: 5px;
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .btn-descargar {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.3s, transform 0.3s;
    }

    .btn-descargar:hover {
        background-color: #218838;
        transform: scale(1.05);
    }

    .btn-accion {
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        margin-right: 5px;
        font-size: 0.75rem;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .btn-editar {
        background-color: #ffc107;
    }

    .btn-eliminar {
        background-color: #dc3545;
    }

    .btn-detalles {
        background-color: #17a2b8;
    }

    .btn-accion:hover {
        opacity: 0.8;
    }

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
        padding: 10px 14px;
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
