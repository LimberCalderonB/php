<?php
// Incluir archivo de conexión a la base de datos
require "conexion/conexionBase.php";

class Rol {
    private $nombre;
    private $con;

    public function __construct() {
        $this->nombre = "";
        $this->con = new ConexionBase();
    }

    public function asignar($nombre, $valor) {
        $this->$nombre = $valor;
    }
    public function existeRol() {
        // Crear la conexión a la base de datos
        $this->con->CreateConnection();
    
        // Preparar la consulta SQL
        $sql = "SELECT * FROM rol WHERE nombre=?";
        $stmt = $this->con->GetConnection()->prepare($sql);
        $stmt->bind_param("s", $this->nombre);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->num_rows > 0;
    }
    public function agregarRol() {
        // Validar si el nombre del rol está vacío
        if(empty($this->nombre)) {
            return false;
        }

        // Verificar si el rol ya existe
        $sql = "SELECT * FROM rol WHERE nombre='$this->nombre'";
        $this->con->CreateConnection();
        $resultado = $this->con->ExecuteQuery($sql);
        $filas = $this->con->GetCountAffectedRows($resultado);
        if($filas > 0) {
            // El rol ya existe, no se puede agregar
            return false;
        } else {
            // El rol no existe, se puede agregar
            $sqlInsert = "INSERT INTO rol (nombre) VALUES ('$this->nombre')";
            $this->con->ExecuteQuery($sqlInsert);
            return true;
        }
    }
}
?>
