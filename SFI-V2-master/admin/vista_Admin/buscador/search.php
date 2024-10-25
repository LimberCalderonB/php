<?php
include_once "../../../conexion.php";

if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Consulta SQL modificada
    $sql = "SELECT 
        p.idproducto,
        p.nombre, 
        p.precio, 
        p.precioConDescuento,
        p.talla,
        IFNULL(p.descuento, 0) AS descuento,
        c.nombre AS categoria_nombre, 
        IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) = 0, 0, SUM(a.cantidad)) AS cantidad_disponible,
        MAX(p.fecha_actualizacion) AS fecha_actualizacion,
        IF(SUM(CASE WHEN a.estado = 'disponible' THEN a.cantidad ELSE 0 END) = 0, 'agotado', 'disponible') AS estado
    FROM producto p
    JOIN almacen a ON p.idproducto = a.producto_idproducto
    JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
    WHERE 
        (p.nombre LIKE ? OR 
        p.precio LIKE ? OR 
        p.talla LIKE ? OR 
        c.nombre LIKE ? OR
        p.fecha_actualizacion LIKE ?)
    GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
    HAVING estado = 'agotado' OR estado = 'disponible'";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
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
