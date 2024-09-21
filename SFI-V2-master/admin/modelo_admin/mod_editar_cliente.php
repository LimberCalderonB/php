<?php
class ModeloCliente {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "proyecto"); // Ajusta según tu configuración
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function actualizarCliente($idusuario_cliente, $nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $usuario_cliente, $pass_cliente) {
        // Actualizar datos del cliente
        $query_cliente = "UPDATE cliente SET nombre_cliente = ?, apellido_cliente = ?, apellido2_cliente = ?, celular_cliente = ? WHERE idcliente = (SELECT cliente_idcliente FROM usuario_cliente WHERE idusuario_cliente = ?)";
        $stmt_cliente = $this->conn->prepare($query_cliente);
        $stmt_cliente->bind_param("ssssi", $nombre_cliente, $apellido_cliente, $apellido2_cliente, $celular_cliente, $idusuario_cliente); // Aquí hay un error en la cantidad de parámetros.
    
        $resultado_cliente = $stmt_cliente->execute();
        
        // Actualizar datos del usuario
        $query_usuario = "UPDATE usuario_cliente SET usuario_cliente = ?, pass_cliente = ? WHERE idusuario_cliente = ?";
        $stmt_usuario = $this->conn->prepare($query_usuario);
        $stmt_usuario->bind_param("ssi", $usuario_cliente, $pass_cliente, $idusuario_cliente);
        $resultado_usuario = $stmt_usuario->execute();
    
        $stmt_cliente->close();
        $stmt_usuario->close();
        $this->conn->close();
    
        return $resultado_cliente && $resultado_usuario;
    }
    public function verificarUsuarioExistente($usuario_cliente, $idusuario_cliente) {
        global $conn; // Asegúrate de usar la conexión correcta

        $query = "SELECT COUNT(*) FROM usuario_cliente WHERE usuario_cliente = ? AND idusuario_cliente != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $usuario_cliente, $idusuario_cliente);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0; // Devuelve true si existe otro usuario con ese nombre
    }
    
}
