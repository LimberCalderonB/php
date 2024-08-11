<?php
require "conexion/conexionBase.php";

class Rol {
    private $idrol;
    private $nombre;
    private $con;

    public function __construct() {
        $this->idrol = null;
        $this->nombre = "";
        $this->con = new ConexionBase();
        $this->con->CreateConnection();
    }

    public function asignar($campo, $valor) {
        $this->$campo = $valor;
    }

    public function existeRol() {
        $sql = "SELECT * FROM rol WHERE nombre = ?";
        $stmt = $this->con->GetConnection()->prepare($sql);
        $stmt->bind_param("s", $this->nombre);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function agregarRol() {
        if (empty($this->nombre)) {
            return false;
        }

        $sqlInsert = "INSERT INTO rol (nombre) VALUES (?)";
        $stmt = $this->con->GetConnection()->prepare($sqlInsert);
        $stmt->bind_param("s", $this->nombre);
        return $stmt->execute();
    }

    public function actualizarRol() {
        if (empty($this->idrol) || empty($this->nombre)) {
            return false;
        }

        $sql = "UPDATE rol SET nombre = ? WHERE idrol = ?";
        $stmt = $this->con->GetConnection()->prepare($sql);
        $stmt->bind_param("si", $this->nombre, $this->idrol);
        return $stmt->execute();
    }
}
?>
