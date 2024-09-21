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
    public function agregarCliente($nombre, $apellido1, $apellido2, $celular) {
        // Escapando las variables para evitar inyecciones SQL
        $nombre = $this->conexion->GetConnection()->real_escape_string($nombre);
        $apellido1 = $this->conexion->GetConnection()->real_escape_string($apellido1);
        $apellido2 = $this->conexion->GetConnection()->real_escape_string($apellido2);
        $celular = $this->conexion->GetConnection()->real_escape_string($celular);

        // Query SQL para insertar un nuevo cliente
        $sql = "INSERT INTO cliente (nombre_cliente, apellido_cliente, apellido2_cliente, celular_cliente)
                VALUES ('$nombre', '$apellido1', '$apellido2', '$celular')";

        // Ejecutando el query
        $this->conexion->ExecuteQuery($sql);

        // Verificando si el cliente fue agregado correctamente
        if ($this->conexion->GetCountAffectedRows() > 0) {
            return $this->conexion->GetConnection()->insert_id; // Devuelve el ID del cliente insertado
        } else {
            return false; // Error al agregar cliente
        }
    }

    // Función para agregar usuario cliente
    public function agregarUsuarioCliente($usuario, $pass, $idCliente) {
        // Escapando las variables
        $usuario = $this->conexion->GetConnection()->real_escape_string($usuario);
        $pass = $this->conexion->GetConnection()->real_escape_string($pass);

        // Encriptar la contraseña
        $pass = password_hash($pass, PASSWORD_BCRYPT);

        // Query SQL para insertar un nuevo usuario cliente
        $sql = "INSERT INTO usuario_cliente (usuario_cliente, pass_cliente, cliente_idcliente)
                VALUES ('$usuario', '$pass', '$idCliente')";

        // Ejecutando el query
        $this->conexion->ExecuteQuery($sql);

        // Verificando si el usuario cliente fue agregado correctamente
        return $this->conexion->GetCountAffectedRows() > 0;
    }

    // Función para verificar si el usuario ya existe
    public function existeUsuario($usuario) {
        $usuario = $this->conexion->GetConnection()->real_escape_string($usuario);
        $sql = "SELECT COUNT(*) as count FROM usuario_cliente WHERE usuario_cliente = '$usuario'";
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

    // Función para obtener los datos de usuario cliente por su ID
    public function obtenerUsuarioClientePorId($idusuario_cliente) {
        $query = "SELECT * FROM usuario_cliente WHERE idusuario_cliente = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $idusuario_cliente);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false; // Si no se encuentra, retornar false
        }
    }
    
    
}
?>
