<?php
include_once 'conexion/conexionBase.php';


class ModeloVentas extends conexionBase {

    public function __construct() {
        parent::__construct();
        $this->CreateConnection();
    }

    public function __destruct() {
        $this->CloseConnection();
    }

    public function getVentasDirectas() {
        $sql = "
            SELECT v.idventa, v.fecha_venta, p.nombre, p.apellido1, 
                   GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos, 
                   SUM(pr.precio) AS precio_total
            FROM venta v
            JOIN usuario u ON v.usuario_idusuario = u.idusuario
            JOIN persona p ON u.persona_idpersona = p.idpersona
            JOIN venta_producto vp ON v.idventa = vp.venta_idventa
            JOIN producto pr ON vp.producto_idproducto = pr.idproducto
            WHERE v.detalle_pedido_iddetalle_pedido IS NULL
            GROUP BY v.idventa
        ";
        
        $result = $this->GetConnection()->query($sql);
        if ($result === false) {
            return ['success' => false, 'error' => $this->GetConnection()->error];
        }
    
        $ventasDirectas = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ventasDirectas[] = $row;
            }
        }
    
        return $ventasDirectas;
    }
    
    public function getVentasPedidos() {
        $sql = "
            SELECT v.idventa, v.fecha_venta, p.nombre, p.apellido1, c.nombre_cliente, 
                   GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos, 
                   SUM(pr.precio) AS precio_total
            FROM venta v
            JOIN usuario u ON v.usuario_idusuario = u.idusuario
            JOIN persona p ON u.persona_idpersona = p.idpersona
            JOIN detalle_pedido dp ON v.detalle_pedido_iddetalle_pedido = dp.iddetalle_pedido
            JOIN pedido ped ON dp.pedido_idpedido = ped.idpedido
            JOIN cliente c ON ped.cliente_idcliente = c.idcliente
            JOIN pedido_producto pp ON ped.idpedido = pp.pedido_idpedido
            JOIN producto pr ON pp.producto_idproducto = pr.idproducto
            GROUP BY v.idventa
        ";
        
        $result = $this->GetConnection()->query($sql);
        if ($result === false) {
            return ['success' => false, 'error' => $this->GetConnection()->error];
        }
    
        $ventasPedidos = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ventasPedidos[] = $row;
            }
        }
    
        return $ventasPedidos;
    }

    public function obtenerVentaPorId($idventa) {
        $sql = "
        SELECT v.idventa, v.fecha_venta, p.nombre, p.apellido1, 
               pr.nombre AS nombre_producto, 
               pr.precio AS precio_producto, 
               pr.descuento AS descuento, 
               (pr.precio - (pr.precio * pr.descuento / 100)) AS precio_descuento, 
               (pr.precio - (pr.precio * pr.descuento / 100)) AS total
        FROM venta v
        JOIN usuario u ON v.usuario_idusuario = u.idusuario
        JOIN persona p ON u.persona_idpersona = p.idpersona
        JOIN venta_producto vp ON v.idventa = vp.venta_idventa
        JOIN producto pr ON vp.producto_idproducto = pr.idproducto
        WHERE v.idventa = ?
    ";
        
        $stmt = $this->GetConnection()->prepare($sql);
        $stmt->bind_param('i', $idventa);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            return null;
        }
    
        $venta = $result->fetch_all(MYSQLI_ASSOC);
        return $venta;
    }
    
}

?>
