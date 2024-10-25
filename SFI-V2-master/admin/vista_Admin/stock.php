<?php
include_once "cabecera.php";
include_once "../../conexion.php";

// Verificar si el parámetro 'filtro' está presente en la URL
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;

// Modificar la consulta según el filtro aplicado
if ($filtro === 'mayor_cantidad') {
    // Consulta para obtener los productos con mayor cantidad disponible
    $sql = "SELECT 
    p.idproducto,
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
            LIMIT 100";
} elseif ($filtro === 'menor_cantidad') {
    // Consulta para obtener los productos con menor cantidad disponible
    $sql = "SELECT 
    p.idproducto,
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
            LIMIT 100";
} elseif ($filtro === 'mayor_precio') {
    // Consulta para obtener los productos con mayor precio
    $sql = "SELECT 
    p.idproducto,
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
            LIMIT 100"; // Ordenar por mayor precio
} elseif ($filtro === 'menor_precio') {
    // Consulta para obtener los productos con menor precio
    $sql = "SELECT 
    p.idproducto,
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
            LIMIT 100"; // Ordenar por menor precio
} elseif ($filtro === 'disponibles') {
    // Consulta para obtener los productos con estado "disponible"
    $sql = "SELECT 
    p.idproducto,
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
                p.idproducto,
                p.nombre, 
                p.precio, 
                p.precioConDescuento,
                p.talla,
                IFNULL(p.descuento, 0) AS descuento,
                c.nombre AS categoria_nombre, 
                -- Si todos los productos están agotados, cantidad será 0
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) = 0, 0, SUM(a.cantidad)) AS cantidad_disponible,
                MAX(p.fecha_actualizacion) AS fecha_actualizacion,
                -- Verificamos si todos los productos del grupo tienen estado agotado
                IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) = 0, 'agotado', 'disponible') AS estado
            FROM producto p
            JOIN almacen a ON p.idproducto = a.producto_idproducto
            JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
            HAVING estado = 'agotado'";
} else {
    // Consulta general para mostrar todos los productos
    $sql = "SELECT 
    p.idproducto,
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
                <h3>Todos los Productos</h3>
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
            <i class="fi fi-sr-badge-check"></i>
        </div>
        <div class="card card-agotados" onclick="location.href='stock.php?filtro=agotados'">
            <h3>Productos Agotados</h3>
            <i class="fi fi-sr-cross-circle"></i>
        </div>
    </div>
    <div class="search-container text-center">
        <form method="GET" action="">
            <input type="text" name="busqueda" class="search-input" placeholder="Buscador..." onkeyup="realTimeSearch(this.value)" />
        </form>
    </div>


    <div class="btn-container">
    <a href="../generarPDF/stock_pdf.php?filtro=<?php echo urlencode($filtro); ?>" target="_blank">
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
                                
                                <a href="editar_producto.php?idproducto=<?php echo $producto['idproducto']; ?>" class="btn-accion btn-editar">Editar</a>
                                <button class="btn-accion btn-eliminar" data-id="<?php echo $producto['idproducto']; ?>">Eliminar</button>
                                <a href="../generarPDF/detalle_producto_pdf.php?idproducto=<?php echo $producto['idproducto']; ?>" target="_blank" class="btn-accion btn-detalles">Detalles</a>
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
