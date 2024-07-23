<?php
session_start();

function getUserByUsernameAndPassword($conn, $username, $password) {

    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    $sql = "SELECT * FROM usuario WHERE nombreUsuario = '$username' AND pass = '$password'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return $user;
    } else {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        include_once "conexion.php";//CONEXION
        
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user = getUserByUsernameAndPassword($conn, $username, $password);

        if ($user) {

            $_SESSION['user_id'] = $user['idusuario'];
            $_SESSION['username'] = $user['nombreUsuario'];
            $sql = "SELECT rol_idrol FROM privilegio WHERE usuario_idusuario = '{$user['idusuario']}'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $rol = $result->fetch_assoc();
                $_SESSION['role'] = $rol['rol_idrol'];
            } else {
                
                $_SESSION['role'] = 'valor_predeterminado';
            }
            
            switch ($_SESSION['role']) {
                case 1: // Rol de administrador
                    header("Location: admin/vista_Admin/home.php");
                    break;
                case 2: // Rol de vendedor
                    header("Location: personal/vista-Empleado/products.php");
                    break;
                default:
                    header("Location: index.php?error=invalid_role");
                    break;
            }
            exit(); 
        } else {
            
            header("Location: index.php?error=invalid_credentials");
            exit();
        }
    } else {
        
        header("Location: index.php");
        exit();
    }
} else {
    
    header("Location: index.php");
    exit();
}
?>
