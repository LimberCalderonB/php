<?php
include_once "cabecera.php";
include_once "../../conexion.php"; // Archivo de conexión a la base de datos

// Consulta para obtener categorías que tienen productos disponibles
$query = "SELECT categoria.idcategoria, categoria.nombre 
          FROM categoria 
          JOIN almacen ON categoria.idcategoria = almacen.categoria_idcategoria
          JOIN producto ON almacen.producto_idproducto = producto.idproducto
          WHERE almacen.cantidad > 0 
          GROUP BY categoria.idcategoria";

$result = $conn->query($query);
$categorias = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

$query = "SELECT 
            p.nombre, 
            p.precio, 
            p.descuento, 
            p.talla, 
            c.nombre AS categoria, 
            SUM(a.cantidad) AS cantidad,
            p.precioConDescuento
          FROM almacen a
          JOIN producto p ON a.producto_idproducto = p.idproducto
          JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
          WHERE a.estado = 'disponible'
          GROUP BY p.nombre, p.precio, p.descuento, p.talla, c.nombre";

$result = $conn->query($query);
?>


<div class="full-width panel mdl-shadow--2dp">
    <div class="full-width panel-tittle bg-primary text-center tittles">
        CREAR PEDIDO
    </div>

    <nav class="navbar">
        <div class="navbar-container">
            <ul class="navbar-menu">
                <?php if (!empty($categorias)) : ?>
                    <?php foreach ($categorias as $categoria) : ?>
                        <li><a href="#"><?= $categoria['nombre']; ?></a></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>No hay categorías disponibles</li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--4-col" style="text-align: left;">
            <a href="cliente.php" class="mdl-button mdl-js-button mdl-button--raised btn-crear">
                Crear Cliente
            </a>
        </div>

        <div class="mdl-cell mdl-cell--4-col" style="text-align: center;">
            <input type="text" id="buscar" placeholder="Buscar cliente" class="mdl-textfield__input">
            <input type="hidden" id="idcliente" name="idcliente">
            <div id="resultados" style="display:none;"></div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="mdl-grid">
    <div class="mdl-cell mdl-cell--12-col">
            <h4>Productos Disponibles</h4>
            <div class="table-responsive">
    <button id="btn-agregar" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">Agregar Seleccionados</button>
    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table styled-table" id="tabla-productos">
        <thead>
            <tr>
                <th>Seleccionar</th>
                <th>Solicitud</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <!--<th>Precio</th>
                <th>Descuento (%)</th>
                <th>P. Descuento</th>-->
                <th>Talla</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Calcular el precio con descuento si no está en la base de datos
                    $precioConDescuento = isset($row['precioConDescuento']) ? $row['precioConDescuento'] : $row['precio'] - ($row['precio'] * ($row['descuento'] / 100));
            ?>
            <tr>
                <td>
                    <input type="checkbox" class="select-product" 
                        data-product-name="<?php echo $row['nombre']; ?>" 
                        data-product-price="<?php echo $row['precio']; ?>" 
                        data-product-discount="<?php echo $row['descuento']; ?>" 
                        data-product-discounted-price="<?php echo $precioConDescuento; ?>">
                </td>
                <td>
                    <input type="number" class="input-solicitud" 
                        min="0" 
                        value="0" 
                        disabled>
                </td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['categoria']; ?></td>
                <td><?php echo "$" . number_format($row['precio'], 2); ?></td>
                <td><?php echo $row['descuento']; ?>%</td>
                <td><?php echo "$" . number_format($precioConDescuento, 2); ?></td>
                <td><?php echo $row['talla']; ?></td>
                <td><?php echo $row['cantidad']; ?></td>
            </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='9'>No hay productos disponibles</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
    </div>

    <div class="mdl-cell mdl-cell--12-col">
        <h4>Productos Seleccionados</h4>
        <div class="table-responsive">
        <button id="btn-agregar" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">Guardar Pedido</button>
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table styled-table" id="tabla-seleccionados">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Descuento (%)</th>
                        <th>P. Descuento</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<?php
include_once "pie.php";
include_once "validaciones/val_pedido.php";
?>
<script>
document.querySelectorAll('.select-product').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        let solicitudInput = this.closest('tr').querySelector('.input-solicitud');
        if (this.checked) {
            solicitudInput.disabled = false;
        } else {
            solicitudInput.disabled = true;
            solicitudInput.value = 0; // Reiniciar cantidad si se deselecciona
        }
    });
});
</script>
