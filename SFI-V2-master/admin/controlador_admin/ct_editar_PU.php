<?php
include_once '../modelo_admin/mod_PU.php';
include_once "../../conexion.php";

// Inicia la sesión para manejar errores y datos del formulario


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $form_data = $_POST;

    // Recuperar datos del formulario
    $idusuario = $form_data['idusuario'] ?? '';
    $ci = $form_data['ci'] ?? '';
    $nombre = $form_data['nombre'] ?? '';
    $apellido1 = $form_data['apellido1'] ?? '';
    $apellido2 = $form_data['apellido2'] ?? '';
    $celular = $form_data['celular'] ?? '';
    $idRol = $form_data['idRol'] ?? '';
    $nombreUsuario = $form_data['nombreUsuario'] ?? '';
    $pass = $form_data['pass'] ?? '';
    $foto = $_FILES['foto'] ?? null;



    // Validar DNI
    if (empty($ci) || !preg_match('/^[a-zA-Z0-9-]{7,12}$/', $ci)) {
        $errors['ci'] = 'El DNI debe tener entre 7 y 12 caracteres, incluyendo letras, números y el guion "-".';
    } else {
        // Verificar si el DNI ya existe para otro usuario
        $query = "SELECT COUNT(*) AS count FROM persona WHERE ci = ? AND idpersona != (SELECT persona_idpersona FROM usuario WHERE idusuario = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $ci, $idusuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            $errors['ci'] = 'El DNI ya está registrado.';
        }
    }

    // Validar Celular
    if (empty($celular) || !preg_match('/^\d{8}$/', $celular)) {
        $errors['celular'] = 'El celular debe tener 8 dígitos numéricos.';
    } else {
        // Verificar si el celular ya existe para otro usuario
        $query = "SELECT COUNT(*) AS count FROM persona WHERE celular = ? AND idpersona != (SELECT persona_idpersona FROM usuario WHERE idusuario = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $celular, $idusuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            $errors['celular'] = 'El celular ya está registrado.';
        }
    }

    // Validar Nombre
    if (empty($nombre) || !preg_match('/^[a-zA-Z]+$/', $nombre)) {
        $errors['nombre'] = 'El nombre es obligatorio y solo debe contener letras.';
    }

    // Validar Apellido Paterno
    if (empty($apellido1) || !preg_match('/^[a-zA-Z]+$/', $apellido1)) {
        $errors['apellido1'] = 'El apellido paterno es obligatorio y solo debe contener letras.';
    }

    // Validar Apellido Materno
    if (!empty($apellido2) && !preg_match('/^[a-zA-Z]*$/', $apellido2)) {
        $errors['apellido2'] = 'El apellido materno debe contener solo letras.';
    }

    // Validar Rol
    if (empty($idRol)) {
        $errors['idRol'] = 'El campo Rol es obligatorio.';
    }

// Validar Nombre de Usuario
if (empty($nombreUsuario)) {
    $errors['nombreUsuario'] = 'El nombre de usuario es obligatorio.';
} else if (!filter_var($nombreUsuario, FILTER_VALIDATE_EMAIL)) {
    $errors['nombreUsuario'] = 'El nombre de usuario debe ser un correo electrónico válido.';
} else if (!preg_match('/@gmail\.com$/', $nombreUsuario)) {
    $errors['nombreUsuario'] = 'El nombre de usuario debe ser una dirección de Gmail (@gmail.com).';
} else {
    // Verificar si el nombre de usuario ya existe en otro usuario
    $query = "SELECT COUNT(*) AS count FROM usuario WHERE nombreUsuario = ? AND idusuario != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $nombreUsuario, $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors['nombreUsuario'] = 'El nombre de usuario ya está registrado.';
    }
}
// Validar Contraseña solo si no está vacía (en caso de edición)
if (!empty($pass)) {
    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/', $pass)) {
        $errors['pass'] = 'La contraseña debe tener al menos 8 caracteres, incluyendo letras, números y símbolos.';
    } else {
        // Hashear la contraseña nueva
        $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
    }
} else {
    // Si el campo de contraseña está vacío, usamos la contraseña actual de la base de datos
    $query = "SELECT pass FROM usuario WHERE idusuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $idusuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pass_hashed = $row['pass']; // Usamos la contraseña existente
    } else {
        $errors['pass'] = 'No se encontró la contraseña del usuario.';
    }
}


    // Manejo de archivo de foto
    $fotoDestPath = null;
    if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
        $fotoTmpPath = $foto['tmp_name'];
        $fotoName = basename($foto['name']);
        $fotoDestPath = "../vista_Admin/img/fotos_de_perfil/" . $fotoName;
        if (!move_uploaded_file($fotoTmpPath, $fotoDestPath)) {
            $errors['foto'] = 'Error al subir la foto.';
        }
    }

    if (count($errors) > 0) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $form_data;
        header("Location: ../vista_admin/editar_PU.php?idusuario=" . urlencode($idusuario));
        exit();
    } else {
        // Actualizar datos en la base de datos
        $modelo = new ModeloPersonaUsuario();

        try {
            $modelo->actualizarPersona(
                $idusuario,
                $ci,
                $nombre,
                $apellido1,
                $apellido2,
                $celular,
                $idRol,
                $nombreUsuario,
                $pass_hashed,
                $foto
            );

            $_SESSION['success'] = 'Usuario actualizado correctamente.';
            header("Location: ../vista_admin/personal.php");
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al actualizar el usuario: ' . $e->getMessage();
            header("Location: ../vista_admin/editar_PU.php?idusuario=" . urlencode($idusuario));
        }
        exit();
    }
} else {
    // No es una solicitud POST
    header("Location: ../vista_admin/editar_PU.php");
    exit();
}
?>
