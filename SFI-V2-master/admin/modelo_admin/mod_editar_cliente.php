<?php
class ModeloCliente {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "proyecto"); // Ajusta según tu configuración
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    // Método para verificar si el celular ya existe en la base de datos
    public function verificarCelularExistente($celular_cliente, $idcliente) {
        $query = "SELECT COUNT(*) FROM cliente WHERE celular_cliente = ? AND idcliente != ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $celular_cliente, $idcliente);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0; // Retorna true si ya existe
    }

    // Método para verificar si el CI ya existe en la base de datos
    public function verificarCIExistente($ci_cliente, $idcliente) {
        $query = "SELECT COUNT(*) FROM cliente WHERE ci_cliente = ? AND idcliente != ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $ci_cliente, $idcliente);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0; // Retorna true si ya existe
    }

    public function actualizarCliente($idcliente, $nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $ci_cliente, $departamento_cliente) {
        // Actualizar datos del cliente
        $query_cliente = "UPDATE cliente SET nombre_cliente = ?, apellido_cliente = ?, apellido2_cliente = ?, celular_cliente = ?, ci_cliente = ?, departamento_cliente = ? WHERE idcliente = ?";
        $stmt_cliente = $this->conn->prepare($query_cliente);
        $stmt_cliente->bind_param("ssssssi", $nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $ci_cliente, $departamento_cliente, $idcliente);
    
        $resultado_cliente = $stmt_cliente->execute();
        
        $stmt_cliente->close();
        $this->conn->close();
    
        return $resultado_cliente; // Solo se devuelve el resultado del cliente
    }

    public function verificarUsuarioExistente($usuario_cliente, $idusuario_cliente) {
        // Esta función ya no es necesaria ya que eliminamos los campos de usuario.
        return false; // Retornar falso para evitar su uso
    }
}
?>
