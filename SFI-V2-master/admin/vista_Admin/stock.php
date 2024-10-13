<?php

include_once "cabecera.php";
include_once '../modelo_admin/mod_stock.php';
$modelo = new ModeloProducto($conn);
$productos = $modelo->obtenerProductos();


?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    P R O D U C T O S 
</div>
<div class="menu-container">
    <div class="card card-completados" onclick="location.href='pendientes.php'">
        <h3>Mayor Cantidad</h3>
        <i class="fi fi-sr-layer-plus"></i>
    </div>
    <div class="card card-pendientes" onclick="location.href='mas_vendidos.php'">
        <h3>Menor Cantidad</h3>
        <i class="fi fi-ss-layer-minus"></i>
    </div>
    <div class="card card-mayor-precio" onclick="location.href='menos_vendidos.php'">
        <h3>Mayor Precio</h3>
        <i class="fi fi-ss-coins"></i>
    </div>
    <div class="card card-menor-precio" onclick="location.href='mas_vendidos.php'">
        <h3>Menor Precio</h3>
        <i class="fi fi-ss-cheap-stack"></i>
    </div>
</div>
<div class="search-container text-center">
                    <form method="GET" action="">
                        <input type="text" name="busqueda" class="search-input" placeholder="Buscardor..." />
                    </form>
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

include_once "pie.php";
include_once "validaciones/val_stock.php";

?>
<style>
/* Estilos para el contenedor de tarjetas */
.menu-container {
    display: flex;
    justify-content: 10px; /* Espacio entre las cartas */
    margin: 10px 0; /* Espaciado vertical */
    flex-wrap: wrap; /* Permitir que las cartas se envuelvan en varias líneas */
}

/* Estilos generales para cada carta */
.card {
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    padding: 15px 20px;
    width: 210px; /* Ajusta el ancho */
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
    transition: transform 0.2s; /* Transición suave */
    margin: 7px; /* Espaciado entre cartas */
}

/* Estilo para el texto */
.card h3 {
    margin: 0;
    font-size: 16px; /* Reducir el tamaño de la letra */
    text-align: left;
}

/* Estilo para los iconos */
.card i {
    font-size: 45px; /* Tamaño del ícono */
    margin-left: 5px; /* Espacio entre el texto y el ícono */
}

/* Efecto hover */
.card:hover {
    transform: scale(1.05); /* Aumenta ligeramente el tamaño al pasar el mouse */
}

/* Colores individuales */
.card-completados {
    background-color: #4CAF50; /* Color verde para completados */
    color: white;
}

.card-pendientes {
    background-color: #FF9800; /* Color naranja para pendientes */
    color: white;
}

.card-menor-precio {
    background-color: #F44336; /* Color rojo para menos vendidos */
    color: white;
}
.card-mayor-precio {
    background-color: #17539c; /* Color rojo para menos vendidos */
    color: white;
}

</style>
<style>
    /* Estilos para el buscador */
.search-container {
    margin: 20px 0;
}

.search-input {
    width: 250px;
    padding: 10px;
    margin: 0 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-buscar {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-buscar:hover {
    background-color: #0056b3;
}

/* Estilos para el filtro de fecha */
.date-filter-container {
    margin: 20px 0;
    text-align: left; /* Alinear el contenido a la derecha */
}

.date-input {
    padding: 10px;
    margin: 0 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-filter {
    padding: 10px 15px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-filter:hover {
    background-color: #218838;
}

</style>