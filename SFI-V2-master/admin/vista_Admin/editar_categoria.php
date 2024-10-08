<?php
include_once "cabecera.php";
include_once "../../conexion.php";

$idcategoria = $_GET['idcategoria'] ?? '';

// Verificar si se proporcionó un idcategoria
if (!$idcategoria) {
    echo "ID de categoría no proporcionado.";
    exit;
}

// Obtener datos de la categoría para la edición
$sql = "SELECT nombre FROM categoria WHERE idcategoria = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idcategoria);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $categoria = $result->fetch_assoc();
    $nombre_categoria = $categoria['nombre'];
} else {
    echo "Categoría no encontrada.";
    exit;
}
$stmt->close();

// Mostrar errores de sesión
$error_categoria = $_SESSION['error_categoria'] ?? false;
$mensaje_categoria = $_SESSION['mensaje_categoria'] ?? '';
$nombre_categoria_sesion = $_SESSION['nombre_categoria'] ?? $nombre_categoria;
unset($_SESSION['error_categoria'], $_SESSION['mensaje_categoria'], $_SESSION['nombre_categoria']);

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
        window.location.href = 'categoria.php';
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
                    MODIFICAR CATEGORÍA
                </div>
                <div class="full-width panel-content">
                    <form action="../controlador_admin/ct_editar_categoria.php" method="post" class="row g-3 needs-validation" novalidate>
                        <input type="hidden" id="idcategoria" name="idcategoria" value="<?php echo htmlspecialchars($idcategoria); ?>">
                        <div class="mdl-grid">
                            <div class="mdl-cell mdl-cell--12-col">
                                <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACION</legend><br>
                            </div>
                            <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo $error_categoria ? 'is-invalid' : ''; ?>">
                                    <input class="mdl-textfield__input" type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre_categoria_sesion); ?>">
                                    <label class="mdl-textfield__label" for="nombre">Categoría</label>
                                    <?php if ($error_categoria): ?>
                                        <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($mensaje_categoria); ?></span>
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
                            <a href="categoria.php" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-danger" onclick="clearSessionData()">
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
unset($_SESSION['error_categoria']); 
unset($_SESSION['mensaje_categoria']);
unset($_SESSION['nombre_categoria']);

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
