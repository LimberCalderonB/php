<?php
include_once "cabecera.php";
include_once '../modelo_admin/mod_producto.php';

$modelo = new ModeloProducto();
$productos = $modelo->obtenerProductos();
?>

                    <div class="full-width panel-tittle bg-primary text-center tittles">
                        ALMACEN DE PRODUCTOS 
                    </div>
<div class="full-width divider-menu-h"></div>
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
        <div class="table-responsive">
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                <thead>
                    <tr>
                        <th>FECHA Y HORA</th>
                        <th>NOMBRE</th>
                        <th>CATEGORIA</th>
                        <th>TALLA</th>
                        <th>PRECIO</th>
                        <th>CANTIDAD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['fecha_actualizacion']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['talla']); ?></td>
                            <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                            <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
