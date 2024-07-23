<?php 
require_once "../modelo/mod_rol.php";

// Obtener datos del formulario y sanitizarlos
$nombre = htmlspecialchars($_POST["nombre"]);
$descripcion = htmlspecialchars($_POST["descripcion"]);

// Crear instancia del modelo Rol
$rol = new Rol();

// Asignar datos del formulario al modelo
$rol->asignar("nombre", $nombre);
$rol->asignar("descripcion", $descripcion);

// Validar y registrar al rol
$rol->validar();

// Redirigir a la página de destino
header("Location: ../vista/rol.php");
exit();
?>