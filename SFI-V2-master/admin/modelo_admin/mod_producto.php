
<?php
include_once 'conexion/conexionBase.php';

class ModeloProducto extends ConexionBase {

    public function __construct() {
        parent::__construct();
        $this->CreateConnection();
    }

    public function __destruct() {
        $this->CloseConnection();
    }

    public function agregarProducto($nombre, $precio, $descuento, $descripcion, $talla, $categoria_idcategoria, $estado, $img1,$img2, $img3) {
        $sql = "INSERT INTO producto (nombre, precio, descuento, descripcion, talla, categoria_idcategoria, estado, img1, img2, img3) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->GetConnection()->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("ssdsssssss", $nombre, $precio, $descuento, $descripcion, $talla, $categoria_idcategoria, $estado, $img1,$img2, $img3);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function obtenerProductos() {
        $sql = "SELECT * FROM producto";
        $result = $this->ExecuteQuery($sql);
        $productos = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
            $this->SetFreeResult($result);
        }

        return $productos;
    }
}
?>
