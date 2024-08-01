<?php
// Verifica si el usuario está autenticado
if (!isset($_SESSION['idusuario'])) {
    // Redirige al usuario a la página de inicio de sesión
    @header("Location: index.php");
    exit();
    $requiredRole = 1; // Por ejemplo, rol de administrador
if ($_SESSION['role'] != $requiredRole) {
    header("Location: index.php?error=no_permission");
    exit();
}

}
?>
