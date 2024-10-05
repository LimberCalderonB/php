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

    public function obtenerProductos() {
        $sql = "SELECT 
                    p.nombre, 
                    p.precio, 
                    p.precioConDescuento,
                    p.talla,
                    IFNULL(p.descuento, 0) AS descuento, -- Cambia aquí para manejar productos sin descuento
                    c.nombre AS categoria_nombre, 
                    COUNT(a.producto_idproducto) AS cantidad, 
                    MAX(p.fecha_actualizacion) AS fecha_actualizacion 
                FROM producto p
                JOIN almacen a ON p.idproducto = a.producto_idproducto
                JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
                GROUP BY p.nombre, p.precio, p.precioConDescuento, p.talla, c.nombre
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
}

?>