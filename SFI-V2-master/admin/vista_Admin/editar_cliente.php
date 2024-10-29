<?php
include_once "cabecera.php";
include_once "../../conexion.php"; // Asegúrate de que la ruta sea correcta

// Inicializar variables para errores y datos
$errores = $_SESSION['errores_cliente'] ?? [];
$datos = $_SESSION['datos_cliente'] ?? [];

// Limpiar errores y datos de sesión después de mostrarlos
unset($_SESSION['errores_cliente']);
unset($_SESSION['datos_cliente']);

// Verificar si se pasó el ID del usuario
// Verificar si se pasó el ID del cliente
if (isset($_GET['idcliente'])) {
    $idcliente = $_GET['idcliente'];

    // Realizar la consulta a la base de datos para obtener los datos del cliente
    $query = "SELECT * FROM cliente WHERE idcliente = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idcliente);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si se encuentra el cliente, rellenar $datos con la información obtenida
    if ($result->num_rows > 0) {
        $datos = $result->fetch_assoc();
    } else {
        error_log("Cliente no encontrado para idcliente: $idcliente");
        $errores['cliente'] = "Cliente no encontrado.";
    }

    $stmt->close();
}


?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    E D I T A R - C L I E N T E
</div>
<div class="full-width panel-content">
    <form action="../controlador_admin/ct_editar_cliente.php" method="post" class="row g-3 needs-validation" novalidate>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACION</legend><br>
            </div>

            <!-- Campo oculto para ID de usuario cliente -->
            <input type="hidden" name="idcliente" value="<?php echo htmlspecialchars($datos['idcliente'] ?? ''); ?>">

            <!-- Campo Nombre -->
            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['nombre_cliente']) ? 'is-invalid' : ''; ?>">
                    <input class="mdl-textfield__input" type="text" id="nombre_cliente" name="nombre_cliente" value="<?php echo htmlspecialchars($datos['nombre_cliente'] ?? ''); ?>" required>
                    <label class="mdl-textfield__label" for="nombre_cliente">Nombre</label>
                    <?php if (isset($errores['nombre_cliente'])): ?>
                        <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['nombre_cliente']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Primer Apellido -->
            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['apellido_cliente']) ? 'is-invalid' : ''; ?>">
                    <input class="mdl-textfield__input" type="text" id="apellido_cliente" name="apellido_cliente" value="<?php echo htmlspecialchars($datos['apellido_cliente'] ?? ''); ?>" required>
                    <label class="mdl-textfield__label" for="apellido_cliente">Primer Apellido</label>
                    <?php if (isset($errores['apellido_cliente'])): ?>
                        <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['apellido_cliente']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Segundo Apellido -->
            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['apellido2_cliente']) ? 'is-invalid' : ''; ?>">
                    <input class="mdl-textfield__input" type="text" id="apellido2_cliente" name="apellido2_cliente" value="<?php echo htmlspecialchars($datos['apellido2_cliente'] ?? ''); ?>" >
                    <label class="mdl-textfield__label" for="apellido2_cliente">Segundo Apellido</label>
                    <?php if (isset($errores['apellido2_cliente'])): ?>
                        <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['apellido2_cliente']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Campo Celular -->
            <div class="mdl-cell mdl-cell--5-col mdl-cell--8-col-tablet">
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['celular_cliente']) ? 'is-invalid' : ''; ?>">
        <input class="mdl-textfield__input" type="number" id="celular_cliente" name="celular_cliente" 
               value="<?php echo htmlspecialchars($datos['celular_cliente'] ?? ''); ?>" 
               minlength="8" maxlength="8" required 
               oninput="if(this.value.length > 8) this.value = this.value.slice(0, 8);">
        <label class="mdl-textfield__label" for="celular_cliente">Celular</label>
        <?php if (isset($errores['celular_cliente'])): ?>
            <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['celular_cliente']); ?></span>
        <?php endif; ?>
    </div>
</div>


            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['ci_cliente']) ? 'is-invalid' : ''; ?>">
                    <input class="mdl-textfield__input" type="text" id="ci_cliente" name="ci_cliente" value="<?php echo htmlspecialchars($datos['ci_cliente'] ?? ''); ?>"maxlength="12">
                    <label class="mdl-textfield__label" for="ci_cliente">CI</label>
                    <?php if (isset($errores['ci_cliente'])): ?>
                        <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['ci_cliente']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

        <!-- Botones de acción -->
        <div class="mdl-cell mdl-cell--12-col">
            <p class="text-center">
                <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-primary" id="editar">Actualizar</button>
                <a href="cliente.php" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-danger">
                    Cancelar
                </a>
            </p>
        </div>
    </form>
</div>
<?php
include_once "pie.php";
?>