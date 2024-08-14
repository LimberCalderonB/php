<?php
require "conexion/conexionBase.php";

class Categoria {
    private $idcategoria;
    private $nombre;
    private $con;

    public function __construct() {
        $this->nombre = "";
        $this->con = new ConexionBase();
        $this->con->CreateConnection();
    }

    public function asignar($nombre, $valor) {
        $this->$nombre = $valor;
    }

    public function existeCategoria() {
        $sql = "SELECT * FROM categoria WHERE nombre=?";
        $stmt = $this->con->GetConnection()->prepare($sql);
        $stmt->bind_param("s", $this->nombre);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function agregarCategoria() {
        if (empty($this->nombre)) {
            return false;
        }

        $sqlInsert = "INSERT INTO categoria (nombre) VALUES (?)";
        $stmt = $this->con->GetConnection()->prepare($sqlInsert);
        $stmt->bind_param("s", $this->nombre);
        return $stmt->execute();
    }

    public function actualizarCategoria() {
        $sql = "UPDATE categoria SET nombre = ? WHERE idcategoria = ?";
        $stmt = $this->con->GetConnection()->prepare($sql);
        $stmt->bind_param("si", $this->nombre, $this->idcategoria);
        return $stmt->execute();
    }

    public function obtenerNombreCategoria($idcategoria) {
        include_once "../../conexion.php";
    
        $sql = "SELECT nombre FROM categoria WHERE idcategoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idcategoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $nombre_categoria = "";
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nombre_categoria = $row['nombre'];
        }
    
        $stmt->close();
        $conn->close();
    
        return $nombre_categoria;
    }
    
}
?>
