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
    
            // Luego, inserta en la tabla almacen con cantidad inicial de 1
            if ($categoria_idcategoria !== null) {
                $sqlAlmacen = "INSERT INTO almacen (producto_idproducto, categoria_idcategoria, cantidad) VALUES (?, ?, ?)";
                $stmtAlmacen = $this->GetConnection()->prepare($sqlAlmacen);
                
                if ($stmtAlmacen === false) {
                    return ['success' => false, 'error' => 'Error al preparar la consulta de almacen'];
                }
    
                $cantidad = 1; // Valor inicial para la cantidad
                $stmtAlmacen->bind_param("iii", $idproducto, $categoria_idcategoria, $cantidad);
                
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
    
    

    public function eliminarProducto($idproducto) {
    // Primero, elimina el producto de la tabla almacen
    $sqlAlmacen = "DELETE FROM almacen WHERE producto_idproducto = ?";
    $stmtAlmacen = $this->GetConnection()->prepare($sqlAlmacen);
    
    if ($stmtAlmacen === false) {
        return ['success' => false, 'error' => 'Error al preparar la consulta de almacen'];
    }

    $stmtAlmacen->bind_param("i", $idproducto);
    
    if (!$stmtAlmacen->execute()) {
        $stmtAlmacen->close();
        return ['success' => false, 'error' => 'Error al eliminar de almacen'];
    }

    $stmtAlmacen->close();

    // Luego, elimina el producto de la tabla producto
    $sqlProducto = "DELETE FROM producto WHERE idproducto = ?";
    $stmtProducto = $this->GetConnection()->prepare($sqlProducto);
    
    if ($stmtProducto === false) {
        return ['success' => false, 'error' => 'Error al preparar la consulta de producto'];
    }

    $stmtProducto->bind_param("i", $idproducto);
    
    if ($stmtProducto->execute()) {
        $stmtProducto->close();
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Error al eliminar producto'];
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

    public function obtenerProductosConDescuento() {
        $sql = "SELECT 
                    p.nombre, 
                    p.precio, 
                    p.precioConDescuento,
                    p.talla,
                    p.descuento,
                    c.nombre AS categoria_nombre, 
                    COUNT(a.producto_idproducto) AS cantidad, 
                    MAX(p.fecha_actualizacion) AS fecha_actualizacion 
                FROM producto p
                JOIN almacen a ON p.idproducto = a.producto_idproducto
                JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
                WHERE p.descuento > 0
                GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre, p.descuento
                ORDER BY fecha_actualizacion DESC";
        
        $result = $this->GetConnection()->query($sql);
        
        if ($result === false) {
            return ['success' => false, 'error' => $this->GetConnection()->error];
        }
    
        $productos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
    
        return $productos;
    }
    
    
    public function obtenerProductosSinDescuento() {
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
                WHERE p.descuento = 0
                GROUP BY p.nombre, p.precio, p.talla, c.nombre
                ORDER BY fecha_actualizacion DESC";
        
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
