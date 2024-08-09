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

    public function actualizarPersona($idusuario, $ci, $nombre, $apellido1, $apellido2, $celular, $idRol, $nombreUsuario, $pass, $foto) {
        // Obtener el idpersona asociado al idusuario
        $query_get_persona_id = "SELECT persona_idpersona FROM usuario WHERE idusuario = ?";
        $stmt = $this->db->GetConnection()->prepare($query_get_persona_id);
        $stmt->bind_param('i', $idusuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $persona_id = $result->fetch_assoc()['persona_idpersona'];
    
            // Actualizar 'persona'
            $query_persona = "UPDATE persona SET ci = ?, nombre = ?, apellido1 = ?, apellido2 = ?, celular = ? WHERE idpersona = ?";
            $stmt = $this->db->GetConnection()->prepare($query_persona);
            $stmt->bind_param('sssssi', $ci, $nombre, $apellido1, $apellido2, $celular, $persona_id);
            $stmt->execute();
    
            // Actualizar 'usuario'
            $query_usuario = "UPDATE usuario SET nombreUsuario = ?, pass = ? WHERE idusuario = ?";
            $stmt = $this->db->GetConnection()->prepare($query_usuario);
            $stmt->bind_param('ssi', $nombreUsuario, $pass, $idusuario);
            $stmt->execute();
    
            // Actualizar 'privilegio'
            $query_privilegio = "UPDATE privilegio SET rol_idrol = ? WHERE usuario_idusuario = ?";
            $stmt = $this->db->GetConnection()->prepare($query_privilegio);
            $stmt->bind_param('ii', $idRol, $idusuario);
            $stmt->execute();
    
            // Guardar la foto en el servidor si se proporciona
            if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../assets/perfil/';
                $foto_path = $upload_dir . basename($foto['name']);
                move_uploaded_file($foto['tmp_name'], $foto_path);
    
                // Actualizar la tabla 'persona' con la ruta de la foto
                $query_update_foto = "UPDATE persona SET foto = ? WHERE idpersona = ?";
                $stmt = $this->db->GetConnection()->prepare($query_update_foto);
                $stmt->bind_param('si', $foto_path, $persona_id);
                $stmt->execute();
            }
        } else {
            throw new Exception("No se encontró la persona asociada al usuario.");
        }
    }
}    
?>
