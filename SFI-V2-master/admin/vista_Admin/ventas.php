<?php
include_once "cabecera.php";
include_once '../modelo_admin/mod_ventas.php';

$ventas = new ModeloVentas();

// Configuración de paginación para ventas directas
$limit = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$ventasDirectas = $ventas->getVentasDirectas($offset, $limit);
$totalVentasDirectas = $ventas->getTotalVentasDirectas();
$totalPagesDirectas = ceil($totalVentasDirectas / $limit);

// Configuración de paginación para ventas de pedidos
$ventasPedidos = $ventas->getVentasPedidos($offset, $limit);
$totalVentasPedidos = $ventas->countVentasPedidos();
$totalPagesPedidos = ceil($totalVentasPedidos / $limit);
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    P R O D U C T O S - V E N D I D O S
</div>
<br>
<!-- Botones como cartas -->
                <div class="menu-container">
                    <div class="card card-completados" onclick="location.href='pendientes.php'">
                        <h3>Productos Mas Vendidos</h3>
                        <i class="fi fi-sr-shopping-cart-add"></i>
                    </div>
                    <div class="card card-pendientes" onclick="location.href='mas_vendidos.php'">
                        <h3>Productos Menos Vendidos</h3>
                        <i class="fi fi-ss-cart-minus"></i>
                    </div>
                    <div class="card card-menos-vendidos" onclick="location.href='menos_vendidos.php'">
                        <h3>Menos Vendidos</h3>
                        <i class="fa fa-chart-bar"></i>
                    </div>
                </div>

                <div class="search-container text-center">
                    <form method="GET" action="">
                        <input type="text" name="busqueda" class="search-input" placeholder="Buscardor..." />
                    </form>
                </div>
<!-- Formulario para filtrar ventas por fecha con animación mejorada -->

<div class="container row">
    <!-- Filtro de Fechas -->
    <div class="filter-container col-12">
        <form method="get" action="ventas.php" class="filter-form">

            <div class="date-filter-container text-left">
                    <form method="GET" action="">
                        <label for="fecha_inicio">Desde:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="date-input" />
                        <label for="fecha_fin">Hasta:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="date-input" />
                        <button type="submit" class="btn-filter">Filtrar</button>
                    </form>
                </div>
            
        </form>
    </div>

    <!-- Botón de Exportar -->
    <div class="btn-container col-12">
        <form method="post" action="../generarPDF/todo_venta_pdf.php" target="_blank">
            <input type="hidden" name="ventasDirectas" value="<?php echo htmlspecialchars(json_encode($ventasDirectas)); ?>">
            <input type="hidden" name="ventasPedidos" value="<?php echo htmlspecialchars(json_encode($ventasPedidos)); ?>">
            <button type="submit" class="btn-exportar">Detalles PDF</button>
        </form>
    </div>
</div>



<!-- Tabla de Ventas Directas -->
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__panel is-active" id="tabNewAdmin">
        <div class="full-width divider-menu-h"></div>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
                <div class="table-responsive">
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                        <thead>
                            <tr>
                                <th>FECHA VENTA</th>
                                <th>RESPONSABLE</th>
                                <th>PRODUCTO(S)</th>
                                <th>CLIENTE</th>
                                <th>PRECIO TOTAL</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ventasDirectas as $venta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($venta['fecha_venta']); ?></td>
                                    <td><?php echo htmlspecialchars($venta['nombre'] . ' ' . $venta['apellido1']); ?></td>
                                    <td>
                                        <?php
                                            $productos = explode(', ', $venta['productos']);
                                            echo htmlspecialchars($productos[0]);
                                            if(count($productos) > 1) {
                                                echo '...';
                                            }
                                        ?>
                                    </td>
                                    <td>nombre de cliente</td>
                                    <td><?php echo htmlspecialchars($venta['precio_total']); ?></td>
                                    <td>
                                        <div class="btn-container">
                                            <!-- Ver Detalles -->
                                            <a href="detalle_venta.php?id=<?php echo urlencode($venta['idventa']); ?>" target="_blank">
                                                <button class="btn-ver btn-accion">Ver Detalles</button>
                                            </a>
                                            
                                            <!-- Editar Venta -->
                                            <a href="editar_venta.php?id=<?php echo urlencode($venta['idventa']); ?>">
                                                <button class="btn-editar btn-accion">Editar</button>
                                            </a>
                                            
                                            <!-- Eliminar Venta -->
                                            <form action="eliminar_venta.php" method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta venta?');">
                                                <input type="hidden" name="idventa" value="<?php echo urlencode($venta['idventa']); ?>">
                                                <button class="btn-eliminar btn-accion">Eliminar</button>
                                            </form>
                                            
                                            <!-- Reimprimir Recibo -->
                                            <a href="../generarPDF/venta.php?id=<?php echo urlencode($venta['idventa']); ?>" target="_blank">
                                                <button class="btn-reimprimir btn-accion">PDF</button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Controles de Paginación para Ventas Directas -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPagesDirectas; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once "pie.php";
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

.card-menos-vendidos {
    background-color: #F44336; /* Color rojo para menos vendidos */
    color: white;
}
</style>


<!--Estilos de los botones de accion-->
<style>
.btn-container {
    display: flex; /* Alinea los botones en una sola fila */
    gap: 10px; /* Espacio entre los botones */
}

.btn-accion {
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    color: white; 
}

/* Diferentes colores para cada botón */
.btn-ver {
    background-color: #17a2b8;
}

.btn-ver:hover {
    background-color: #138496;
}

.btn-editar {
    background-color: #ffc107;
}

.btn-editar:hover {
    background-color: #e0a800;
}

.btn-eliminar {
    background-color: #dc3545;
}

.btn-eliminar:hover {
    background-color: #c82333;
}

.btn-reimprimir {
    background-color: #6c757d;
}

.btn-reimprimir:hover {
    background-color: #5a6268;
}
</style>

<style>
/* Contenedor de la paginación */
.pagination {
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 20px 0;
    list-style: none;
    gap: 10px; /* Espacio entre botones */
}

/* Estilo general de los botones de paginación */
.page-item {
    display: inline-block;
}

.page-link {
    color: #007bff; /* Color del texto del botón */
    background-color: white; /* Fondo del botón */
    border: 1px solid #007bff; /* Borde del botón */
    padding: 8px 16px; /* Espaciado dentro del botón */
    border-radius: 5px; /* Bordes redondeados */
    text-decoration: none; /* Quitar subrayado */
    transition: background-color 0.3s, color 0.3s, transform 0.3s; /* Efectos de transición */
}

/* Efecto hover para los botones */
.page-link:hover {
    background-color: #007bff;
    color: white;
    transform: scale(1.05); /* Efecto de agrandamiento */
}

/* Estilos para el botón activo */
.page-item.active .page-link {
    background-color: #0056b3; /* Fondo del botón activo */
    color: white; /* Color del texto del botón activo */
    border-color: #0056b3; /* Borde del botón activo */
    font-weight: bold; /* Negrita en el botón activo */
    transform: scale(1.1); /* Efecto de crecimiento en el botón activo */
}

/* Quitar el focus outline al hacer clic */
.page-link:focus {
    outline: none;
}
</style>

<style>
    /* Estilo para centrar el contenido de las celdas de la tabla */
.mdl-data-table th, .mdl-data-table td {
    text-align: center; /* Centra el texto en los encabezados y celdas */
}

/* Opcional: ajustar la altura de las filas para mejor apariencia */
.mdl-data-table tr {
    height: 50px; /* Altura de la fila */
}

</style>

<style>
/* Estilos generales */
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 20px;
}

.filter-container, .btn-container {
    margin: 7px 0;
}

.filter-form {
    display: flex;
    flex-direction: column;
}

.date-row {
    display: flex;
    justify-content: space-between;
    gap: 7px;
    align-items: center;
}

.date-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    flex-grow: 1;
}

.animated-label {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.input-fecha {
    padding: 5px;
    font-size: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s ease;
    width: 80%;
}

.input-fecha:hover, .input-fecha:focus {
    border-color: #007bff;
}

/* Botones */
.btn-filtrar, .btn-exportar {
    padding: 12px 15px;
    font-size: 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 20px;
}

.btn-filtrar {
    background-color: #28a745;
    color: white;
}

.btn-filtrar:hover {
    background-color: #218838;
}

.btn-exportar {
    background-color: #17a2b8;
    color: white;
}

.btn-exportar:hover {
    background-color: #138496;
}

/* Estilo responsive */
@media (max-width: 668px) {
    .date-row {
        flex-direction: column;
    }

    .container {
        flex-direction: column;
        align-items: center;
    }

    .filter-container, .btn-container {
        width: 100%;
    }
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