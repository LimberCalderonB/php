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

    public function agregarProducto($nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1, $img2, $img3, $cantidad) {
        $idproductos = [];
    
        for ($i = 0; $i < $cantidad; $i++) {
            $sql = "INSERT INTO producto (nombre, precio, descuento, precioConDescuento, descripcion, talla, img1, img2, img3) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->GetConnection()->prepare($sql);
            if ($stmt === false) {
                return ['success' => false, 'error' => 'Error al preparar la consulta: ' . $this->GetConnection()->error];
            }
        
            // Vinculación de parámetros
            $stmt->bind_param("sddssssss", $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $img1, $img2, $img3);
            
            if ($stmt->execute()) {
                $idproducto = $stmt->insert_id;
                $idproductos[] = $idproducto;
            } else {
                return ['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $stmt->error];
            }
            $stmt->close();
        }
    
        // Insertar en la tabla `almacen`
        if ($categoria_idcategoria !== null) {
            foreach ($idproductos as $idproducto) {
                $sql_almacen = "INSERT INTO almacen (producto_idproducto, categoria_idcategoria, cantidad) VALUES (?, ?, ?)";
                $stmt_almacen = $this->GetConnection()->prepare($sql_almacen);
                if ($stmt_almacen === false) {
                    return ['success' => false, 'error' => 'Error al preparar la consulta: ' . $this->GetConnection()->error];
                }
                $stmt_almacen->bind_param("iii", $idproducto, $categoria_idcategoria, $cantidad);
                if ($stmt_almacen->execute()) {
                    $stmt_almacen->close();
                } else {
                    return ['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $stmt_almacen->error];
                }
            }
        }
    
        return ['success' => true, 'idproductos' => $idproductos];
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
    public function cambiarEstadoAgotadoAVendido($nombre, $precio, $talla, $descuento, $categoria_idcategoria) {
        $sql = "UPDATE almacen a
                JOIN producto p ON a.producto_idproducto = p.idproducto
                SET a.estado = 'vendido'
                WHERE p.nombre = ? AND p.precio = ? AND p.talla = ? AND p.descuento = ? 
                  AND a.estado = 'agotado' AND a.categoria_idcategoria = ?";
        
        $stmt = $this->GetConnection()->prepare($sql);
        if ($stmt === false) {
            return ['success' => false, 'error' => 'Error al preparar la consulta: ' . $this->GetConnection()->error];
        }
        
        $stmt->bind_param("sdsii", $nombre, $precio, $talla, $descuento, $categoria_idcategoria);
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $stmt->error];
        }
    }

}
?>