<?php
// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "proyecto");

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener el ID del grupo y la cantidad de la solicitud POST
$groupId = $_POST['group_id'];
$quantity = $_POST['quantity'];

if ($groupId && $quantity) {
    $conn = new ConexionBase();
    $conn->conectar();
    
    // Eliminar productos basados en el ID del grupo y la cantidad
    // Primero, obtener la cantidad total de productos en el grupo
    $sql = "SELECT COUNT(*) AS total FROM producto WHERE idproducto = ?";
    
    if ($stmt = $conn->getConexion()->prepare($sql)) {
        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();
        
        if ($total >= $quantity) {
            // Eliminar la cantidad especificada de productos
            $sql = "DELETE FROM producto WHERE idproducto = ? LIMIT ?";
            
            if ($stmt = $conn->getConexion()->prepare($sql)) {
                $stmt->bind_param('ii', $groupId, $quantity);
                if ($stmt->execute()) {
                    echo "$quantity productos del grupo con ID $groupId eliminados exitosamente.";
                } else {
                    echo "Error al eliminar los productos.";
                }
                $stmt->close();
            } else {
                echo "Error en la preparación de la consulta.";
            }
        } else {
            echo "No hay suficientes productos para eliminar.";
        }
    } else {
        echo "Error en la preparación de la consulta.";
    }
    
    $conn->cerrar();
} else {
    echo "ID del grupo o cantidad no proporcionados.";
}
?>
