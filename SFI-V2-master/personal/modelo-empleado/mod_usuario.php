<?php
require "conexion/conexionBase.php";
class Usuario {
    public $id;
    public $nombre;
    public $contrasena;
    public $id_persona;

    public function __construct($id, $nombre, $contrasena, $id_persona) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->contrasena = $contrasena;
        $this->id_persona = $id_persona;
    }

    public function guardar() {
        $conexion = new mysqli("localhost", "usuario", "contrasena", "basedatos");
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }
        
        $sql = "INSERT INTO usuario (nombre, contrasena, id_persona) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssi", $this->nombre, $this->contrasena, $this->id_persona);
        $result = $stmt->execute();
        $stmt->close();
        $conexion->close();
        return $result;
    }

    public function actualizar() {
        $conexion = new mysqli("localhost", "usuario", "contrasena", "basedatos");
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }
        
        $sql = "UPDATE usuario SET nombre=?, contrasena=?, id_persona=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssii", $this->nombre, $this->contrasena, $this->id_persona, $this->id);
        $result = $stmt->execute();
        $stmt->close();
        $conexion->close();
        return $result;
    }

    public static function buscarPorId($id) {
        $conexion = new mysqli("localhost", "usuario", "contrasena", "basedatos");
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }
        
        $sql = "SELECT * FROM usuario WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = null;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $usuario = new Usuario($row["id"], $row["nombre"], $row["contrasena"], $row["id_persona"]);
        }
        $stmt->close();
        $conexion->close();
        return $usuario;
    }

    public static function eliminarPorId($id) {
        $conexion = new mysqli("localhost", "usuario", "contrasena", "basedatos");
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }
        
        $sql = "DELETE FROM usuario WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        $conexion->close();
        return $result;
    }
}
?>
