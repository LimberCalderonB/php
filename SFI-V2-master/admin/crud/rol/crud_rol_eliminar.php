<?php

if(isset($_POST['idrol'])) {

    $idrol = $_POST['idrol'];
    include_once "../../../conexion.php";
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $sql = "DELETE FROM rol WHERE idrol = $idrol";
    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "Error al eliminar el rol: " . $conn->error;
    }
    $conn->close();
} else {
    echo "ID de rol no proporcionado";
}
?>
