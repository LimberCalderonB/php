<?php
include_once '../modelo_admin/mod_PU.php';
include_once "../../conexion.php";

session_start();

// Inicializar variables de error
$errors = [];
$form_data = $_POST;

// Recuperar datos del formulario
$ci = $_POST['ci'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellido1 = $_POST['apellido1'] ?? '';
$apellido2 = $_POST['apellido2'] ?? '';
$celular = $_POST['celular'] ?? '';
$idRol = $_POST['idRol'] ?? '';
$nombreUsuario = $_POST['nombreUsuario'] ?? '';
$pass = $_POST['pass'] ?? '';
$foto = isset($_FILES['foto']) ? $_FILES['foto'] : null;

// Validar DNI
if (empty($ci) || !preg_match('/^\d{7}$/', $ci)) {
    $errors['ci'] = 'El DNI debe tener 7 dígitos numéricos.';
} else {
    // Verificar si el DNI ya existe
    $query = "SELECT COUNT(*) AS count FROM persona WHERE ci = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $ci);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors['ci'] = 'El DNI ya está registrado.';
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
    $errors['idRol'] = 'Debe seleccionar un rol.';
}

// Validar Celular
if (empty($celular) || !preg_match('/^\d{8}$/', $celular)) {
    $errors['celular'] = 'El celular debe tener 8 dígitos numéricos.';
} else {
    // Verificar si el celular ya existe
    $query = "SELECT COUNT(*) AS count FROM persona WHERE celular = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $celular);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors['celular'] = 'El celular ya está registrado.';
    }
}

// Validar Nombre de Usuario
if (empty($nombreUsuario)) {
    $errors['nombreUsuario'] = 'El nombre de usuario es obligatorio.';
} else if (!filter_var($nombreUsuario, FILTER_VALIDATE_EMAIL)) {
    $errors['nombreUsuario'] = 'El nombre de usuario debe ser un correo electrónico válido.';
} else if (!preg_match('/@gmail\.com$/', $nombreUsuario)) {
    $errors['nombreUsuario'] = 'El nombre de usuario debe ser una dirección de Gmail (@gmail.com).';
} else {
    // Verificar si el nombre de usuario ya existe
    $query = "SELECT COUNT(*) AS count FROM usuario WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $nombreUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors['nombreUsuario'] = 'El nombre de usuario ya está registrado.';
    }
}


// Validar Contraseña
if (empty($pass)) {
    $errors['pass'] = 'La contraseña es obligatoria.';
} elseif (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/', $pass)) {
    $errors['pass'] = 'La contraseña debe tener al menos 8 caracteres, incluyendo letras, números y símbolos.';
} else {
    // Si la contraseña es válida, la ciframos
    $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
}


// Si hay errores, guardarlos en la sesión y redirigir de vuelta al formulario
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $form_data; // Guarda los datos del formulario para reubicación
    header("Location: ../vista_admin/personal.php");
    exit();
}

// Solo manejar la adición de nuevo personal
$modelo = new ModeloPersonaUsuario();

try {
    $modelo->agregarPersona($ci, $nombre, $apellido1, $apellido2, $celular, $idRol, $nombreUsuario, $pass_hashed, $foto);

    // Redirigir o mostrar mensaje de éxito
    $_SESSION['registro'] = 'Datos Registrados';
    header('Location: ../vista_Admin/personal.php');
    exit();
} catch (Exception $e) {
    // Manejo de errores
    echo "Error: " . $e->getMessage();
}
?>
