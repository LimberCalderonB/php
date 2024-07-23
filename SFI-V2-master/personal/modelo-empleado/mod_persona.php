<?php
require "conexion/conexionBase.php";

class Persona {
    private $nombre;
    private $apellido;
    private $con;

    public function __construct() {
        $this->nombre = "";
        $this->apellido = "";
        $this->con = new ConexionBase();
    }

    public function asignar($nombre, $valor) {
        $this->$nombre = $valor;
    }

    public function validar() {
        $sql = "SELECT * FROM persona WHERE nombre='$this->nombre'";
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
        $sql = "INSERT INTO persona (nombre, apellido) VALUES ('$this->nombre', '$this->apellido')";
        $this->con->ExecuteQuery($sql);
    }
}
?>