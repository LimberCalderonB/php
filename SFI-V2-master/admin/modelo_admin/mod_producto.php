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

    public function agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3) {
        $sql = "INSERT INTO producto (nombre, precio, descuento, precioConDescuento, descripcion, talla, img1, img2, img3) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->GetConnection()->prepare($sql);
        if ($stmt === false) {
            return ['success' => false, 'error' => 'Error al preparar la consulta: ' . $this->GetConnection()->error];
        }

        $stmt->bind_param("sddssssss", $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $img1, $img2, $img3);
        
        if ($stmt->execute()) {
            $idproducto = $stmt->insert_id;
            $stmt->close();

            if ($categoria_idcategoria !== null) {
                $sqlAlmacen = "INSERT INTO almacen (producto_idproducto, categoria_idcategoria, cantidad) VALUES (?, ?, ?)";
                $stmtAlmacen = $this->GetConnection()->prepare($sqlAlmacen);
                
                if ($stmtAlmacen === false) {
                    return ['success' => false, 'error' => 'Error al preparar la consulta de almacen: ' . $this->GetConnection()->error];
                }

                $cantidad = 1;
                $stmtAlmacen->bind_param("iii", $idproducto, $categoria_idcategoria, $cantidad);
                
                if ($stmtAlmacen->execute()) {
                    $stmtAlmacen->close();
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'Error al insertar en almacen: ' . $stmtAlmacen->error];
                }
            } else {
                return ['success' => false, 'error' => 'Categoría no especificada'];
            }
        } else {
            return ['success' => false, 'error' => 'Error al insertar en producto: ' . $stmt->error];
        }
    }

    public function eliminarProducto($idproducto) {
        $sqlAlmacen = "DELETE FROM almacen WHERE producto_idproducto = ?";
        $stmtAlmacen = $this->GetConnection()->prepare($sqlAlmacen);
        
        if ($stmtAlmacen === false) {
            return ['success' => false, 'error' => 'Error al preparar la consulta de almacen: ' . $this->GetConnection()->error];
        }

        $stmtAlmacen->bind_param("i", $idproducto);
        
        if (!$stmtAlmacen->execute()) {
            $stmtAlmacen->close();
            return ['success' => false, 'error' => 'Error al eliminar de almacen: ' . $stmtAlmacen->error];
        }

        $stmtAlmacen->close();

        $sqlProducto = "DELETE FROM producto WHERE idproducto = ?";
        $stmtProducto = $this->GetConnection()->prepare($sqlProducto);
        
        if ($stmtProducto === false) {
            return ['success' => false, 'error' => 'Error al preparar la consulta de producto: ' . $this->GetConnection()->error];
        }

        $stmtProducto->bind_param("i", $idproducto);
        
        if ($stmtProducto->execute()) {
            $stmtProducto->close();
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Error al eliminar producto: ' . $stmtProducto->error];
        }
    }

    public function obtenerProductoPorId($idproducto) {
        $query = "SELECT p.*, a.categoria_idcategoria FROM producto p
                  LEFT JOIN almacen a ON p.idproducto = a.producto_idproducto
                  WHERE p.idproducto = ?";
        $stmt = $this->GetConnection()->prepare($query);
        
        if ($stmt === false) {
            return ['success' => false, 'error' => 'Error al preparar la consulta: ' . $this->GetConnection()->error];
        }
        
        $stmt->bind_param("i", $idproducto);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        if ($data) {
            return $data;
        } else {
            return ['success' => false, 'error' => 'Producto no encontrado'];
        }
    }

    public function actualizarProducto($idproducto, $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3) {
        $query = "UPDATE producto SET 
                     nombre = ?, 
                     precio = ?, 
                     descuento = ?, 
                     precioConDescuento = ?, 
                     descripcion = ?, 
                     talla = ?, 
                     img1 = ?, 
                     img2 = ?, 
                     img3 = ? 
                  WHERE idproducto = ?";
        
        $stmt = $this->GetConnection()->prepare($query);
        
        if ($stmt === false) {
            return ['success' => false, 'error' => 'Error al preparar la consulta: ' . $this->GetConnection()->error];
        }
        
        $stmt->bind_param("sddssssssi", $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $img1, $img2, $img3, $idproducto);
        
        if ($stmt->execute()) {
            $stmt->close();

            if ($categoria_idcategoria !== null) {
                $queryAlmacen = "UPDATE almacen SET categoria_idcategoria = ? WHERE producto_idproducto = ?";
                $stmtAlmacen = $this->GetConnection()->prepare($queryAlmacen);
                
                if ($stmtAlmacen === false) {
                    return ['success' => false, 'error' => 'Error al preparar la consulta de almacen: ' . $this->GetConnection()->error];
                }

                $stmtAlmacen->bind_param("ii", $categoria_idcategoria, $idproducto);
                
                if ($stmtAlmacen->execute()) {
                    $stmtAlmacen->close();
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'Error al actualizar almacen: ' . $stmtAlmacen->error];
                }
            } else {
                return ['success' => false, 'error' => 'Categoría no especificada'];
            }
        } else {
            return ['success' => false, 'error' => 'Error al actualizar producto: ' . $stmt->error];
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
