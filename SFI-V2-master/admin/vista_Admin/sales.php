<?php
include_once "cabecera.php";
include_once "../../conexion.php";

$sql = "SELECT v.fecha_venta, p.nombre AS nombre_responsable, p.apellido1 AS apellido_responsable, 
        c.nombre AS nombre_cliente, pr.nombre AS nombre_producto, v.pago 
        FROM venta v
        JOIN detalle_pedido dp ON v.producto_idproducto = dp.producto_idproducto
        JOIN pedido pe ON dp.pedido_idpedido = pe.idpedido
        JOIN cliente c ON pe.cliente_idcliente = c.idcliente
        JOIN persona p ON v.persona_idpersona = p.idpersona
        JOIN producto pr ON v.producto_idproducto = pr.idproducto
        ORDER BY v.fecha_venta DESC";

$result = $conn->query($sql);
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    VENTAS 
</div>

<div class="full-width divider-menu-h"></div>
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
        <div class="table-responsive">
            <table class="table table-hover text-center">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Responsable</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['fecha_actualizacion']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_responsable'] . " " . $row['apellido_responsable']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                            <td><?php echo number_format($row['pago'], 2); ?> Bs</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
<?php include_once "pie.php"; ?>



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
