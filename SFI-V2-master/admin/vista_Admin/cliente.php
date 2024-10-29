
<?php
include_once "cabecera.php";
include_once "../../conexion.php";
?>
	
<br>
<script>
    <?php if (isset($_SESSION['registro_exitoso']) && $_SESSION['registro_exitoso']): ?>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "<?php echo $_SESSION['mensaje_cliente']; ?>",
            showConfirmButton: false,
            timer: 1300
        });
        <?php unset($_SESSION['registro_exitoso']); // Limpiar la sesión después de mostrar la alerta ?>
    <?php endif; ?>
</script>
<?php
// Obtener errores y datos de sesión
$errores = $_SESSION['errores_cliente'] ?? [];
$datos = $_SESSION['datos_cliente'] ?? [];

// Limpiar errores y datos de sesión
unset($_SESSION['errores_cliente']);
unset($_SESSION['datos_cliente']);

// Detecta si se ha pasado el parámetro para activar la pestaña de "AGREGAR NUEVO CLIENTE"
$activeTab = 'tabNewCliente'; // Valor por defecto

if (isset($_GET['tab']) && $_GET['tab'] === 'list-cliente') {
    $activeTab = 'tabListcliente';
}

?>

<!--------------------------------------------------------->

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__tab-bar">
    <a href="#tabNewCliente" class="mdl-tabs__tab <?php echo $activeTab === 'tabNewCliente' ? 'is-active' : ''; ?>">AGREGAR NUEVO CLIENTE</a>
        <a href="#tabListcliente" class="mdl-tabs__tab <?php echo $activeTab === 'tabListcliente' ? 'is-active' : ''; ?>">LISTA DE CLIENTES</a>
        
    </div>
    <div class="mdl-tabs__panel <?php echo $activeTab === 'tabNewCliente' ? 'is-active' : ''; ?>" id="tabNewCliente">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <div class="full-width panel mdl-shadow--2dp">
                    <div class="full-width panel-tittle bg-primary text-center tittles">
                        N U E V O - C L I E N T E
                    </div>
                    <div class="full-width panel-content">
                        <form action="../controlador_admin/ct_cliente.php" method="post" class="row g-3 needs-validation" novalidate>
                            <div class="mdl-grid">
                                <div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACIÓN</legend><br>
                                </div>
                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['nombre_cliente']) ? 'is-invalid' : ''; ?>">
                                        <input class="mdl-textfield__input" type="text" id="nombre_cliente" name="nombre_cliente" value="<?php echo htmlspecialchars($datos['nombre_cliente'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="nombre_cliente">Nombre</label>
                                        <?php if (isset($errores['nombre_cliente'])): ?>
                                            <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['nombre_cliente']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['apellido_cliente']) ? 'is-invalid' : ''; ?>">
                                        <input class="mdl-textfield__input" type="text" id="apellido_cliente" name="apellido_cliente" value="<?php echo htmlspecialchars($datos['apellido_cliente'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="apellido_cliente">Primer Apellido</label>
                                        <?php if (isset($errores['apellido_cliente'])): ?>
                                            <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['apellido_cliente']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['apellido2_cliente']) ? 'is-invalid' : ''; ?>">
                                        <input class="mdl-textfield__input" type="text" id="apellido2_cliente" name="apellido2_cliente" value="<?php echo htmlspecialchars($datos['apellido2_cliente'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="apellido2_cliente">Segundo Apellido</label>
                                        <?php if (isset($errores['apellido2_cliente'])): ?>
                                            <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['apellido2_cliente']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--5-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['celular_cliente']) ? 'is-invalid' : ''; ?>">
                                        <input class="mdl-textfield__input" type="number" id="celular_cliente" name="celular_cliente" 
                                            value="<?php echo htmlspecialchars($datos['celular_cliente'] ?? ''); ?>"
                                            minlength="8" maxlength="8"
                                            oninput="if(this.value.length > 8) this.value = this.value.slice(0, 8);">
                                        <label class="mdl-textfield__label" for="celular_cliente">Celular</label>
                                        <?php if (isset($errores['celular_cliente'])): ?>
                                            <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['celular_cliente']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['ci_cliente']) ? 'is-invalid' : ''; ?>">
                                        <input class="mdl-textfield__input" type="text" id="ci_cliente" name="ci_cliente" maxlength="12" value="<?php echo htmlspecialchars($datos['ci_cliente'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="ci_cliente">CI</label>
                                        <?php if (isset($errores['ci_cliente'])): ?>
                                            <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['ci_cliente']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!--<div class="mdl-cell mdl-cell--3-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errores['departamento_cliente']) ? 'is-invalid' : ''; ?>">
                                        <select class="mdl-textfield__input" id="departamento_cliente" name="departamento_cliente">
                                            <option value="" disabled selected></option>
                                            <option value="Chuquisaca">Chuquisaca</option>
                                            <option value="La Paz">La Paz</option>
                                            <option value="Cochabamba">Cochabamba</option>
                                            <option value="Oruro">Oruro</option>
                                            <option value="Potosí">Potosí</option>
                                            <option value="Tarija">Tarija</option>
                                            <option value="Santa Cruz">Santa Cruz</option>
                                            <option value="Beni">Beni</option>
                                            <option value="Pando">Pando</option>
                                        </select>
                                        <label class="mdl-textfield__label" for="departamento_cliente">Seleccione el departamento</label>
                                        <?php if (isset($errores['departamento_cliente'])): ?>
                                            <span class="mdl-textfield__error" style="color:red;"><?php echo htmlspecialchars($errores['departamento_cliente']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>-->
                            </div>
                            <p class="text-center">
                                <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="agregar" type="submit">
                                    <i class="zmdi zmdi-plus"></i>
                                </button>
                                <div class="mdl-tooltip" for="agregar">Agregar Cliente</div>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-------------------------------------------------------------------------------------------------------->
    <!--VISTA DE CLIENTES REGISTRADOS-->
    <!-------------------------------------------------------------------------------------------------------->
    <div class="mdl-tabs__panel <?php echo $activeTab === 'tabListcliente' ? 'is-active' : ''; ?>" id="tabListcliente">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--8-col-desktop mdl-cell--2-offset-desktop">
                <div class="full-width panel mdl-shadow--2dp">
                <div class="full-width panel-tittle bg-success text-center tittles">
                L I S T A - DE - C L I E N T E S
                </div>
                <div class="full-width panel-content">
                    <form action="#">
                        <!--<div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                            <label class="mdl-button mdl-js-button mdl-button--icon" for="searchAdmin">
                                <i class="zmdi zmdi-search"></i>
                            </label>
                            <div class="mdl-textfield__expandable-holder">
                                <input class="mdl-textfield__input" type="text" id="searchAdmin" name="searchAdmin" placeholder="Buscar...">
                                <label class="mdl-textfield__label"></label>
                            </div>
                        </div>-->
                    </form>
                    <?php
                        // Consultar la lista de clientes desde la tabla cliente
                        $query_clientes = "SELECT * FROM cliente";
                        $result_clientes = mysqli_query($conn, $query_clientes);
                        
                        if (!$result_clientes) {
                            echo "Error en la consulta: " . mysqli_error($conn);
                        } elseif (mysqli_num_rows($result_clientes) > 0) {
                            while ($row = mysqli_fetch_assoc($result_clientes)) {
                    ?>
                <div class="mdl-list__item mdl-list__item--two-line">
                    <span class="mdl-list__item-primary-content">

                        <span><?php echo htmlspecialchars($row['nombre_cliente']) . ' ' . htmlspecialchars($row['apellido_cliente']) . ' ' . htmlspecialchars($row['apellido2_cliente']); ?></span>
                        <span class="mdl-list__item-sub-title"><?php echo htmlspecialchars($row['celular_cliente']) . '| CI : ' . htmlspecialchars($row['ci_cliente']) ?></span>
                    </span>
                    <div class="btn-right">
                        <button class='mdl-button mdl-js-button mdl-button--icon' 
                                onclick='window.location.href="editar_cliente.php?idcliente=<?php echo $row["idcliente"]; ?>"'>
                            <i class="zmdi zmdi-edit"></i>
                        </button>
                        <button class="btn danger mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect btn-delete" 
                                onclick='confirmDeletion(<?php echo $row["idcliente"]; ?>)'>
                            <i class="zmdi zmdi-delete"></i>
                        </button>
                    </div>
                </div>
            <hr class="mdl-list__item-divider">
        <?php
            }
            } else {
                echo "<p>No se encontraron resultados</p>";
            }
        ?>
    </div>
<?php
include_once "pie.php";
?>
<script>
function confirmDeletion(idcliente) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminarlo!"
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar solicitud AJAX para eliminar
            $.ajax({
                url: '../crud/cliente/eliminar_cliente.php',
                type: 'POST',
                data: {
                    idcliente: idcliente // Usamos idcliente en lugar de idusuario_cliente
                },
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        Swal.fire({
                            title: "¡Eliminado!",
                            text: "El registro ha sido eliminado.",
                            icon: "success"
                        }).then(() => {
                            window.location.reload(); // Recargar la página para reflejar cambios
                        });
                    } else {
                        Swal.fire({
                            title: '¡Error!',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: '¡Error!',
                        text: 'Ocurrió un error al procesar tu solicitud.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}
</script>

