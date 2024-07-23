<?php
require_once "../modelo/mod_usuario.php";
// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que se hayan recibido todos los campos necesarios
    if (isset($_POST["nombre_usuario"]) && isset($_POST["contrasena"]) && isset($_POST["persona"])) {
        // Obtener los datos del formulario
        $nombre_usuario = $_POST["nombre_usuario"];
        $contrasena = $_POST["contrasena"];
        $id_persona = $_POST["persona"];

        // Realizar la conexión a la base de datos
        $conexion = new mysqli("localhost", "root", "", "login");

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Preparar la consulta SQL para insertar el usuario en la base de datos
        $sql = "INSERT INTO usuario (nombre, pass, persona_idpersona) VALUES (?, ?, ?)";
        
        // Preparar la declaración
        $stmt = $conexion->prepare($sql);

        // Verificar si la preparación fue exitosa
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conexion->error);
        }

        // Enlazar los parámetros
        $stmt->bind_param("ssi", $nombre_usuario, $contrasena, $id_persona);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Usuario registrado correctamente.";
        } else {
            echo "Error al registrar el usuario: " . $stmt->error;
        }

        // Cerrar la conexión y liberar los recursos
        $stmt->close();
        $conexion->close();
    } else {
        echo "Por favor, complete todos los campos del formulario.";
    }
}
?>
