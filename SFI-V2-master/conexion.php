<?php
$conn = new mysqli("localhost", "root", "", "proyecto");

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>