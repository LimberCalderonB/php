<?php
require "conexion/conexionBase.php";

class Rol {
    private $nombre;
    private $descripcion;
    private $con;

    public function __construct() {
        $this->nombre = "";
        $this->descripcion = "";
        $this->con = new ConexionBase();
    }

    public function asignar($nombre, $valor) {
        $this->$nombre = $valor;
    }

    public function validar() {
        $sql = "SELECT * FROM rol WHERE nombre='$this->nombre'";
        $this->con->CreateConnection();
        $resp = $this->con->ExecuteQuery($sql);
        $aux = $this->con->GetCountAffectedRows($resp);
        if ($aux > 0) {
            session_start();
            $_SESSION['error'] = 1;
            $_SESSION['mensaje'] = "El nombre ya está en uso.";
        } else {
            $this->registrar();
        }
    }

    public function registrar() {
        $this->con->CreateConnection();
        $sql = "INSERT INTO rol (nombre, descripcion) VALUES ('$this->nombre', '$this->descripcion')";
        $this->con->ExecuteQuery($sql);
    }
}
?>