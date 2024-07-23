<?php
if(isset($_POST['idcategoria'])) {
    $idcategoria = $_POST['idcategoria']; // Asegurarse de usar la variable correcta
    include_once "../../../conexion.php";
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $sql = "DELETE FROM categoria WHERE idcategoria = $idcategoria";
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "Error al eliminar la categoria: " . $conn->error;
    }
    $conn->close();
} else {
    echo "ID de categoria no proporcionado";
}
?>
