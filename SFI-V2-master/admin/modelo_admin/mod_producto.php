<?php
include_once 'conexion/conexionBase.php';

class ModeloProducto extends conexionBase {

    public function __construct() {
        parent::__construct();
        $this->CreateConnection();
    }

    public function __destruct() {
        $this->CloseConnection();
    }

    public function agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $estado, $img1, $img2, $img3) {
        // Primero, inserta el producto en la tabla producto
        $sql = "INSERT INTO producto (nombre, precio, descuento, precioConDescuento, descripcion, talla, estado, img1, img2, img3) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->GetConnection()->prepare($sql);
        if ($stmt === false) {
            return ['success' => false, 'error' => 'Error al preparar la consulta'];
        }

        $stmt->bind_param("ssddssssss", $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $estado, $img1, $img2, $img3);
        
        if ($stmt->execute()) {
            $idproducto = $stmt->insert_id; // Obtiene el ID del producto insertado
            $stmt->close();

            // Luego, inserta en la tabla almacen
            if ($categoria_idcategoria !== null) {
                $sqlAlmacen = "INSERT INTO almacen (producto_idproducto, categoria_idcategoria) VALUES (?, ?)";
                $stmtAlmacen = $this->GetConnection()->prepare($sqlAlmacen);
                
                if ($stmtAlmacen === false) {
                    return ['success' => false, 'error' => 'Error al preparar la consulta de almacen'];
                }

                $stmtAlmacen->bind_param("ii", $idproducto, $categoria_idcategoria);
                
                if ($stmtAlmacen->execute()) {
                    $stmtAlmacen->close();
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'Error al insertar en almacen'];
                }
            } else {
                return ['success' => false, 'error' => 'Categoría no especificada'];
            }
        } else {
            return ['success' => false, 'error' => 'Error al insertar en producto'];
        }
    }

    public function obtenerProductos() {
        // Consulta modificada para agrupar por nombre, precio, talla y categoría, y sumar la cantidad y obtener la última fecha
        $sql = "SELECT 
                    p.nombre, 
                    p.precio, 
                    p.precioConDescuento,
                    p.talla, 
                    c.nombre AS categoria_nombre, 
                    COUNT(a.producto_idproducto) AS cantidad, 
                    MAX(p.fecha_actualizacion) AS fecha_actualizacion 
                FROM producto p
                JOIN almacen a ON p.idproducto = a.producto_idproducto
                JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
                GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre";
        
        $result = $this->GetConnection()->query($sql);
        $productos = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
        return $productos;
    }
}
?>
