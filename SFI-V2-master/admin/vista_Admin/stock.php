<?php

include_once "cabecera.php";
include_once '../modelo_admin/mod_stock.php';

// Crear la instancia del modelo con la conexión
$modelo = new ModeloProducto($conn);

// Obtener los productos
$productos = $modelo->obtenerProductos();


?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    R E P O R T E - D E - P R O D U C T O S 
</div>

<div class="btn-container">
    <a href="../generarPDF/inventario_pdf.php" target="_blank">
        <button class="btn-descargar">
            DESCARGAR DATOS   
            <i class="fi fi-rs-down-to-line"></i>
        </button>
    </a>
</div>

<div class="mdl-tabs__panel is-active" id="tabListAdmin">
    <div class="full-width divider-menu-h"></div>
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
            <div class="table-responsive">
                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table">
                    <thead>
                        <tr>
                            <th>FECHA Y HORA</th>
                            <th>NOMBRE</th>
                            <th>CATEGORÍA</th>
                            <th>TALLA</th>
                            <th>PRECIO</th>
                            <th>DESCUENTO</th>
                            <th>ESTADO</th>
                            <th>CANTIDAD</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Mostrar los productos obtenidos -->
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['fecha_actualizacion']); ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($producto['talla']); ?></td>
                                <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                                <td><?php echo htmlspecialchars($producto['descuento']); ?></td>
                                <td></td> <!-- Aquí puedes agregar el estado si es necesario -->
                                <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                <td>
                                    <a href="#" class="btn-accion btn-editar">Editar</a>
                                    <a href="#" class="btn-accion btn-eliminar">Eliminar</a>
                                    <a href="#" class="btn-accion btn-detalles">Detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el pie de página y validaciones
include_once "pie.php";
include_once "validaciones/val_stock.php";

// Cerrar la conexión después de ejecutar todas las consultas

?>
