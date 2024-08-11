<?php
include_once "cabecera.php";
include_once "../../conexion.php";

$idrol = $_GET['idrol'] ?? '';

// Verificar si se proporcionó un idrol
if (!$idrol) {
    echo "ID de rol no proporcionado.";
    exit;
}

// Obtener datos del rol para la edición
$sql = "SELECT nombre FROM rol WHERE idrol = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idrol);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rol = $result->fetch_assoc();
    $nombre_rol = $rol['nombre'];
} else {
    echo "Rol no encontrado.";
    exit;
}
$stmt->close();

// Mostrar errores de sesión
$error_rol = $_SESSION['error_rol'] ?? false;
$mensaje_rol = $_SESSION['mensaje_rol'] ?? '';
$nombre_rol_sesion = $_SESSION['nombre_rol'] ?? $nombre_rol;
unset($_SESSION['error_rol'], $_SESSION['mensaje_rol'], $_SESSION['nombre_rol']);

// ALERTA DE ACTUALIZACIÓN EXITOSA
if (isset($_SESSION['registro']) && $_SESSION['registro'] == true) {
    echo "<script>
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Actualización Exitosa',
        showConfirmButton: false,
        timer: 1000
    }).then(() => {
        window.location.href = 'rol.php'; // Redirigir a la lista de roles después de la alerta
    });
    </script>";
    unset($_SESSION['registro']);
}
?>

<br>

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <div class="full-width panel mdl-shadow--2dp">
                <div class="full-width panel-tittle bg-primary text-center tittles">
                    MODIFICAR ROL
                </div>
                <div class="full-width panel-content">
                    <form action="../controlador_admin/ct_editar_rol.php" method="post" class="row g-3 needs-validation" novalidate>
                        <input type="hidden" id="idrol" name="idrol" value="<?php echo htmlspecialchars($idrol); ?>">
                        <div class="mdl-grid">
                            <div class="mdl-cell mdl-cell--12-col">
                                <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACION</legend><br>
                            </div>
                            <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo $error_rol ? 'is-invalid' : ''; ?>">
                                    <input class="mdl-textfield__input" type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre_rol_sesion); ?>">
                                    <label class="mdl-textfield__label" for="nombre">Rol</label>
                                    <?php if ($error_rol): ?>
                                        <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($mensaje_rol); ?></span>
                                    <?php else: ?>
                                        <span class="mdl-textfield__error" style="color:red;">Campo obligatorio</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <p class="text-center">
                            <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-primary" type="submit" name="editar" value="1">
                                Guardar Cambios
                            </button>
                            <a href="rol.php" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-danger" onclick="clearSessionData()">
                                Cancelar
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Limpiar las variables
unset($_SESSION['error_rol']); 
unset($_SESSION['mensaje_rol']);
unset($_SESSION['nombre_rol']);

include_once "pie.php";
?>

<style>
    .is-invalid .mdl-textfield__input {
        border-color: red;
    }
    .mdl-textfield__error {
        color: red;
    }
</style>
