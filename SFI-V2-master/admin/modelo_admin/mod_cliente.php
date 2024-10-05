<?php
require_once 'conexion/conexionBase.php'; // Ajusta la ruta según la ubicación de tu archivo

class ModeloCliente {
    private $conexion;

    public function __construct() {
        $this->conexion = new conexionBase();
        $this->conexion->CreateConnection();
    }

    public function __destruct() {
        $this->conexion->CloseConnection();
    }

    // Función para agregar cliente
    public function agregarCliente($nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $ci_cliente, $departamento_cliente) {
        $nombre = $this->conexion->GetConnection()->real_escape_string($nombre_cliente);
        $apellido1 = $this->conexion->GetConnection()->real_escape_string($apellido_cliente);
        $apellido2 = $this->conexion->GetConnection()->real_escape_string($apellido2_cliente);
        $celular = $this->conexion->GetConnection()->real_escape_string($celular_cliente);
        $ci = $this->conexion->GetConnection()->real_escape_string($ci_cliente);
        $departamento = $this->conexion->GetConnection()->real_escape_string($departamento_cliente);
    
        $sql = "INSERT INTO cliente (nombre_cliente, apellido_cliente, apellido2_cliente, celular_cliente, ci_cliente, departamento_cliente)
                VALUES ('$nombre', '$apellido1', '$apellido2', '$celular', '$ci', '$departamento')";
        
        $this->conexion->ExecuteQuery($sql);
    
        if ($this->conexion->GetCountAffectedRows() > 0) {
            return $this->conexion->GetConnection()->insert_id;
        } else {
            return false;
        }
    }
    
    

    // Función para verificar si el celular ya existe
    public function existeCelular($celular_cliente) {
        $celular_cliente = $this->conexion->GetConnection()->real_escape_string($celular_cliente);
        $sql = "SELECT COUNT(*) as count FROM cliente WHERE celular_cliente = '$celular_cliente'";
        $resultado = $this->conexion->ExecuteQuery($sql);
        $row = $resultado->fetch_assoc();
        return $row['count'] > 0;
    }

    public function existeci($ci_cliente) {
        $ci_cliente = $this->conexion->GetConnection()->real_escape_string($ci_cliente);
        $sql = "SELECT COUNT(*) as count FROM cliente WHERE ci_cliente = '$ci_cliente'";
        $resultado = $this->conexion->ExecuteQuery($sql);
        $row = $resultado->fetch_assoc();
        return $row['count'] > 0;
    }

    // Función para obtener los datos de un cliente por su ID
    public function obtenerClientePorId($idCliente) {
        $idCliente = $this->conexion->GetConnection()->real_escape_string($idCliente);
        $sql = "SELECT * FROM cliente WHERE idcliente = $idCliente";
        $resultado = $this->conexion->ExecuteQuery($sql);
        return $resultado->fetch_assoc();
    }
}
?>
