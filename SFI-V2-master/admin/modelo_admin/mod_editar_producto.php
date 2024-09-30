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

    // OBTENER PRODUCTOS POR ID
    public function obtenerProductosSimilares($nombre, $precio, $descuento, $talla, $categoria_idcategoria) {
        $query = "SELECT p.idproducto FROM producto p
                  JOIN almacen a ON p.idproducto = a.producto_idproducto
                  WHERE p.nombre = ? 
                  AND p.precio = ? 
                  AND p.descuento = ? 
                  AND p.talla = ? 
                  AND a.categoria_idcategoria = ?";
        $stmt = $this->GetConnection()->prepare($query);
        $stmt->bind_param('sddsi', $nombre, $precio, $descuento, $talla, $categoria_idcategoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $productosSimilares = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $productosSimilares;
    }

    // MODIFICAR PRODUCTOS
    public function actualizarProducto($idproducto, $nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla, $categoria_idcategoria, $img1 = null, $img2 = null, $img3 = null) {
        // Iniciar transacción
        $this->GetConnection()->begin_transaction();

        try {
            // Construcción de la consulta de actualización del producto
            $queryProducto = "UPDATE producto SET 
                                nombre = ?, 
                                precio = ?, 
                                descuento = ?, 
                                precioConDescuento = ?, 
                                descripcion = ?, 
                                talla = ?";

            // Añadir imágenes solo si no son nulas
            if ($img1 !== null) {
                $queryProducto .= ", img1 = ?";
            } else {
                $queryProducto .= ", img1 = NULL";
            }

            if ($img2 !== null) {
                $queryProducto .= ", img2 = ?";
            } else {
                $queryProducto .= ", img2 = NULL";
            }

            if ($img3 !== null) {
                $queryProducto .= ", img3 = ?";
            } else {
                $queryProducto .= ", img3 = NULL";
            }

            $queryProducto .= " WHERE idproducto = ?";

            // Preparar la consulta
            $stmtProducto = $this->GetConnection()->prepare($queryProducto);

            if ($stmtProducto === false) {
                throw new Exception('Error al preparar la consulta de producto: ' . $this->GetConnection()->error);
            }

            // Vincular los parámetros
            $params = [$nombre, $precio, $descuento, $precioConDescuento, $descripcion, $talla];
            $types = 'ssddss';

            if ($img1 !== null) {
                $params[] = $img1;
                $types .= 's';
            }
            if ($img2 !== null) {
                $params[] = $img2;
                $types .= 's';
            }
            if ($img3 !== null) {
                $params[] = $img3;
                $types .= 's';
            }

            $params[] = $idproducto;
            $types .= 'i';

            $stmtProducto->bind_param($types, ...$params);

            // Ejecutar la consulta
            if (!$stmtProducto->execute()) {
                throw new Exception('Error al ejecutar la consulta de producto: ' . $stmtProducto->error);
            }

            // Actualizar la categoría en la tabla "almacen"
            $queryAlmacen = "UPDATE almacen SET categoria_idcategoria = ? WHERE producto_idproducto = ?";
            $stmtAlmacen = $this->GetConnection()->prepare($queryAlmacen);

            if ($stmtAlmacen === false) {
                throw new Exception('Error al preparar la consulta de almacen: ' . $this->GetConnection()->error);
            }

            $stmtAlmacen->bind_param('ii', $categoria_idcategoria, $idproducto);

            if (!$stmtAlmacen->execute()) {
                throw new Exception('Error al ejecutar la consulta de almacen: ' . $stmtAlmacen->error);
            }

            // Confirmar transacción
            $this->GetConnection()->commit();
            return true;

        } catch (Exception $e) {
            // Revertir la transacción si hay algún error
            $this->GetConnection()->rollback();
            return false;
        }
    }
    public function obtenerCategoriaPorId($idproducto) {
        global $conn; // Asumiendo que $conn es tu conexión a la base de datos
        
        $sql = "SELECT a.categoria_idcategoria 
                FROM almacen a 
                JOIN producto p ON a.producto_idproducto = p.idproducto 
                WHERE p.idproducto = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idproducto);
        $stmt->execute();
        $stmt->bind_result($categoria_id);
        $stmt->fetch();
        $stmt->close();
        
        return $categoria_id;
    }
    
}
?>
