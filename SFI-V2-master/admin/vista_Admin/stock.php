<?php
include_once "cabecera.php";
include_once "../../conexion.php";

// Verificar si el parámetro 'filtro' está presente en la URL
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;

// Modificar la consulta según el filtro aplicado
if ($filtro === 'mayor_cantidad') {
    // Consulta para obtener los productos con mayor cantidad disponible
    $sql = "SELECT 
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
            ORDER BY cantidad_disponible DESC
            LIMIT 1";
} elseif ($filtro === 'menor_cantidad') {
    // Consulta para obtener los productos con menor cantidad disponible
    $sql = "SELECT 
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
            ORDER BY cantidad_disponible ASC
            LIMIT 1";
} elseif ($filtro === 'mayor_precio') {
    // Consulta para obtener los productos con mayor precio
    $sql = "SELECT 
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
            ORDER BY p.precio DESC
            LIMIT 1"; // Ordenar por mayor precio
} elseif ($filtro === 'menor_precio') {
    // Consulta para obtener los productos con menor precio
    $sql = "SELECT 
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
            ORDER BY p.precio ASC
            LIMIT 1"; // Ordenar por menor precio
} elseif ($filtro === 'disponibles') {
    // Consulta para obtener los productos con estado "disponible"
    $sql = "SELECT 
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            WHERE a.estado = 'disponible'
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre";
} elseif ($filtro === 'agotados') {
    // Consulta para obtener los productos con estado "agotado"
    $sql = "SELECT 
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                SUM(CASE WHEN a.estado = 'agotado' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                IF(SUM(CASE WHEN a.estado = 'agotado' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            WHERE a.estado = 'agotado'
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre";
} else {
    // Consulta general para mostrar todos los productos
    $sql = "SELECT 
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) AS cantidad_disponible, 
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) > 0, 'disponible', 'agotado') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
            ORDER BY fecha_actualizacion DESC";
}

$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}

// Almacenar los productos en un array
$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}
?>


<div class="full-width panel-tittle bg-primary text-center tittles">
    P R O D U C T O S 
</div>
    <div class="menu-container">
        <div class="card card-todo" onclick="location.href='stock.php'">
                <h3>Todos los Pedidos</h3>
                <i class="fi fi-sr-globe"></i>
            </div>
        <div class="card card-completados" onclick="location.href='stock.php?filtro=mayor_cantidad'">
            <h3>Mayor Cantidad</h3>
            <i class="fi fi-sr-layer-plus"></i>
        </div>
        <div class="card card-pendientes" onclick="location.href='stock.php?filtro=menor_cantidad'">
            <h3>Menor Cantidad</h3>
            <i class="fi fi-ss-layer-minus"></i>
        </div>
        <div class="card card-mayor-precio" onclick="location.href='stock.php?filtro=mayor_precio'">
            <h3>Mayor Precio</h3>
            <i class="fi fi-ss-coins"></i>
        </div>
        <div class="card card-menor-precio" onclick="location.href='stock.php?filtro=menor_precio'">
            <h3>Menor Precio</h3>
            <i class="fi fi-ss-cheap-stack"></i>
        </div>
        <div class="card card-disponibles" onclick="location.href='stock.php?filtro=disponibles'">
            <h3>Productos Disponibles</h3>
            <i class="fi fi-ss-cheap-stack"></i>
        </div>
        <div class="card card-agotados" onclick="location.href='stock.php?filtro=agotados'">
            <h3>Productos Agotados</h3>
            <i class="fi fi-ss-cheap-stack"></i>
        </div>
    </div>
    <div class="search-container text-center">
    <form method="GET" action="">
        <input type="text" name="busqueda" class="search-input" placeholder="Buscador..." onkeyup="realTimeSearch(this.value)" />
    </form>
</div>


    <div class="btn-container">
        <a href="../generarPDF/stock_pdf.php" target="_blank">
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
                                <td class="<?php echo ($producto['estado'] === 'disponible') ? 'estado-disponible' : 'estado-agotado'; ?>">
                                    <?php echo htmlspecialchars($producto['estado']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($producto['cantidad_disponible']); ?></td>
                                <td>
                                    <a href="#" class="btn-accion btn-editar">Editar</a>
                                    <a href="#" class="btn-accion btn-eliminar">Eliminar</a>
                                    
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

<script>
function realTimeSearch(query) {
    if (query.length === 0) {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'buscador/search.php?query=' + encodeURIComponent(query), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const productos = JSON.parse(xhr.responseText);
            const tbody = document.querySelector('tbody'); // Asegúrate de que este selector sea correcto

            // Limpiar el contenido actual de la tabla
            tbody.innerHTML = '';

            // Añadir los resultados de la búsqueda a la tabla
            productos.forEach(producto => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${producto.fecha_actualizacion}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.categoria_nombre}</td>
                    <td>${producto.talla}</td>
                    <td>${producto.precio}</td>
                    <td>${producto.descuento}</td>
                    <td class="${producto.estado === 'disponible' ? 'estado-disponible' : 'estado-agotado'}">${producto.estado}</td>
                    <td>${producto.cantidad_disponible}</td>
                    <td>
                        <a href="#" class="btn-accion btn-editar">Editar</a>
                        <a href="#" class="btn-accion btn-eliminar">Eliminar</a>
                        <a href="#" class="btn-accion btn-detalles">Detalles</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
    };
    xhr.send();
}
</script>




<style>
.estado-disponible {
    color: #4CAF50;
    font-weight: bold;
}

.estado-agotado {
    color: red;
    font-weight: bold;
}


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
.card-todo {
    background-color: #a5baff; /* Color verde para completados */
    color: white;
}
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
.card-disponibles {
    background-color: #7b5ca5; /* Color rojo para menos vendidos */
    color: white;
}
.card-agotados {
    background-color: #26294d; /* Color rojo para menos vendidos */
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