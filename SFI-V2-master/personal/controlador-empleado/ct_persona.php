<?php 
require_once "../modelo/mod_persona.php";

// Obtener datos del formulario y sanitizarlos
$nombre = htmlspecialchars($_POST["nombre"]);
$apellido = htmlspecialchars($_POST["apellido"]);

// Crear instancia del modelo Usuario
$persona = new Persona();

// Asignar datos del formulario al modelo
$persona->asignar("nombre", $nombre);
$persona->asignar("apellido", $apellido);

// Validar y registrar al usuario
$persona->validar();

// Redirigir a la página de destino
header("Location: ../vista/persona.php");
exit();
?>