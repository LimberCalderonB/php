<?php
include_once "cabecera.php";
include_once "../../conexion.php";

  // Asegúrate de que la sesión está iniciada

// Recuperar ID de categoría desde la URL
$idcategoria = $_GET['idcategoria'] ?? '';
$categoria = [];

// Recuperar los datos de la categoría si se proporciona un ID
if ($idcategoria) {
    $sql = "SELECT idcategoria, nombre FROM categoria WHERE idcategoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idcategoria);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $categoria = $result->fetch_assoc();
    } else {
        echo "Categoría no encontrada.";
        exit;
    }
    $stmt->close();
}

// Recuperar mensaje de error de sesión si está definido
$error_message = isset($_SESSION['mensaje_categoria']) ? $_SESSION['mensaje_categoria'] : '';
$error_class = isset($_SESSION['error_categoria']) && $_SESSION['error_categoria'] ? 'input-error' : '';
unset($_SESSION['mensaje_categoria'], $_SESSION['error_categoria']);
?>

<br>
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--12-col">
        <div class="full-width panel mdl-shadow--2dp">
            <div class="full-width panel-tittle bg-primary text-center tittles">
                Editar Categoría
            </div>
            <div class="full-width panel-content">
                <form action="../controlador_admin/ct_categoria.php" method="post" class="row g-3 needs-validation" novalidate>
                    <input type="hidden" id="idcategoria" name="idcategoria" value="<?php echo htmlspecialchars($idcategoria); ?>">
                    <div class="mdl-grid">
                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACIÓN</legend><br>
                        </div>
                        <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input <?php echo $error_class; ?>" type="text" id="nombre" name="nombre" value="<?php echo isset($_SESSION['nombre_categoria']) ? htmlspecialchars($_SESSION['nombre_categoria']) : htmlspecialchars($categoria['nombre'] ?? ''); ?>" required>
                                <label class="mdl-textfield__label" for="nombre">Nombre de Categoría</label>
                                <?php if ($error_message): ?>
                                    <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($error_message); ?></span>
                                <?php else: ?>
                                    <span class="mdl-textfield__error" style="display:none;">Campo obligatorio</span>
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

<?php
include_once "pie.php";
?>

<?php
unset($_SESSION['mensaje_categoria']);
unset($_SESSION['error_categoria']);
unset($_SESSION['nombre_categoria']);
@header("Location: categoria.php");
exit();
?>
