<?php
include_once "cabecera.php";
include_once '../modelo_admin/mod_producto.php';

$modelo = new ModeloProducto();
$productosConDescuento = $modelo->obtenerProductosConDescuento();
$productosSinDescuento = $modelo->obtenerProductosSinDescuento();
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    ALMACEN DE PRODUCTOS 
</div>

<div class="btn-container">
    <button class="btn-descargar">
            DESCARGAR FACTURAS   
        <i class="fi fi-rs-down-to-line"></i>
    </button>
    <button class="btn-filter">
        FILTRO
    <i class="fi fi-rr-settings-sliders"></i>
    </button>
</div>

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__tab-bar">
        <a href="#tabNewAdmin" class="mdl-tabs__tab is-active">SIN DESCUENTO</a>
        <a href="#tabListAdmin" class="mdl-tabs__tab">CON DESCUENTO</a>
    </div>
    <div class="mdl-tabs__panel is-active" id="tabNewAdmin">
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
                            <?php foreach ($productosSinDescuento as $producto): ?>
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
    </div>
    <div class="mdl-tabs__panel" id="tabListAdmin">
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
                                <th>DESCUENTO</th>
                                <th>P.DESCUENTO</th>
                                <th>CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosConDescuento as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['fecha_actualizacion']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['talla']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['descuento']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['precioConDescuento']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
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

/* Estilos para el contenedor del botón */
.btn-container {
    text-align: right;
    margin-top: 20px;
}

/* Estilos para el botón de descarga */
.btn-descargar {
    background-color: #007bff; /* Color de fondo */
    color: #fff; /* Color del texto */
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px; /* Bordes redondeados */
    transition: background-color 0.3s, transform 0.3s; /* Transición suave para color y tamaño */
    display: inline-flex;
    align-items: center;
}

.btn-descargar i {
    margin-left: 10px; /* Espacio entre el texto y el ícono */
    font-size: 18px; /* Tamaño del ícono */
}

/* Estilos cuando se pasa el ratón sobre el botón */
.btn-descargar:hover {
    background-color: #0056b3; /* Color de fondo cuando se pasa el ratón */
    transform: scale(1.05); /* Aumenta el tamaño ligeramente */
}

.btn-descargar:focus {
    outline: none; /* Quitar el borde de enfoque */
}
</style>

<script>
// Función para exportar la tabla a CSV
function exportTableToCSV(filename, tableSelector) {
    var csv = [];
    var rows = document.querySelectorAll(tableSelector + " tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");

        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }

        csv.push(row.join(","));        
    }

    // Crear un enlace para descargar el archivo
    var csvFile;
    var downloadLink;

    csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);

    downloadLink.click();
}

// Asignar la función al botón para exportar la tabla activa
document.querySelector(".btn-descargar").addEventListener("click", function() {
    var activeTab = document.querySelector(".mdl-tabs__panel.is-active");
    exportTableToCSV("productos.csv", activeTab.querySelector("table").outerHTML);
});
</script>
