<?php
include_once 'conexion/conexionBase.php';

class ModeloPersonaUsuario {
    private $db;

    public function __construct() {
        $this->db = new conexionBase();
        $this->db->CreateConnection();
    }

    public function agregarPersona($ci, $nombre, $apellido1, $apellido2, $celular, $idRol, $nombreUsuario, $pass, $foto) {
        // Prepare and execute query to insert into 'persona'
        $query_persona = "INSERT INTO persona (ci, nombre, apellido1, apellido2, celular) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->GetConnection()->prepare($query_persona);
        $stmt->bind_param('sssss', $ci, $nombre, $apellido1, $apellido2, $celular);
        $stmt->execute();

        $persona_id = $this->db->GetConnection()->insert_id;

        // Prepare and execute query to insert into 'usuario'
        $query_usuario = "INSERT INTO usuario (persona_idpersona, nombreUsuario, pass) VALUES (?, ?, ?)";
        $stmt = $this->db->GetConnection()->prepare($query_usuario);
        $stmt->bind_param('iss', $persona_id, $nombreUsuario, $pass);
        $stmt->execute();

        $usuario_id = $this->db->GetConnection()->insert_id;

        // Prepare and execute query to insert into 'privilegio'
        $query_privilegio = "INSERT INTO privilegio (usuario_idusuario, rol_idrol) VALUES (?, ?)";
        $stmt = $this->db->GetConnection()->prepare($query_privilegio);
        $stmt->bind_param('ii', $usuario_id, $idRol);
        $stmt->execute();

        // Save the photo on the server if provided
        if ($foto) {
            $upload_dir = '../../assets/perfil/';
            $foto_path = $upload_dir . basename($foto['name']);
            move_uploaded_file($foto['tmp_name'], $foto_path);

            // Update 'persona' table with the photo path
            $query_update_foto = "UPDATE persona SET foto = ? WHERE idpersona = ?";
            $stmt = $this->db->GetConnection()->prepare($query_update_foto);
            $stmt->bind_param('si', $foto_path, $persona_id);
            $stmt->execute();
        }
    }

    public function obtenerPersonal() {
        $query = "SELECT p.foto, p.nombre, p.apellido1, p.apellido2, p.ci, r.nombre AS rol 
                  FROM persona AS p 
                  INNER JOIN usuario AS u ON p.idpersona = u.persona_idpersona 
                  INNER JOIN privilegio AS pv ON u.idusuario = pv.usuario_idusuario 
                  INNER JOIN rol AS r ON pv.rol_idrol = r.idrol";
        $result = $this->db->ExecuteQuery($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $this->db->SetFreeResult($result);
        return $data;
    }

    public function __destruct() {
        $this->db->CloseConnection();
    }

    
}
?>