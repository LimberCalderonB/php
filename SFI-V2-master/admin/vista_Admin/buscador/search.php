<?php
include_once "../../../conexion.php";

if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Consulta para buscar productos que coincidan con el término de búsqueda en nombre, precio, talla, estado, categoría y fecha
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
            WHERE 
                (p.nombre LIKE ? OR 
                p.precio LIKE ? OR 
                p.talla LIKE ? OR 
                a.estado LIKE ? OR 
                c.nombre LIKE ? OR
                p.fecha_actualizacion LIKE ?)
            GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $productos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }

    // Devolver los resultados como JSON
    echo json_encode($productos);
}
?>
