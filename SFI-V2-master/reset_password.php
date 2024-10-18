<?php
include('conexion.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar si el token es válido
    $query = "SELECT idusuario, reset_token_expiry FROM usuario WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar si el token ha expirado
        if (new DateTime() < new DateTime($user['reset_token_expiry'])) {
            // Mostrar formulario de nueva contraseña
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                // Actualizar la contraseña y borrar el token
                $updateQuery = "UPDATE usuario SET pass = ?, reset_token = NULL, reset_token_expiry = NULL WHERE idusuario = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param('si', $new_password, $user['idusuario']);
                $stmt->execute();

                echo "Contraseña actualizada con éxito.";
            }
        } else {
            echo "El enlace de recuperación ha expirado.";
        }
    } else {
        echo "Token inválido.";
    }
} else {
    echo "No se proporcionó un token.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
</head>
<body>
    <h2>Restablecer contraseña</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="new_password">Nueva contraseña</label>
            <input type="password" name="new_password" id="new_password" required>
        </div>
        <button type="submit">Actualizar contraseña</button>
    </form>
</body>
</html>
