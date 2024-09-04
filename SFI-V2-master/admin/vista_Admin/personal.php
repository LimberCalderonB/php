<?php
include_once "cabecera.php";
include_once "../../conexion.php";

$query_roles = "SELECT idrol, nombre FROM rol";
$result_roles = mysqli_query($conn, $query_roles);

// Mostrar errores si existen
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['errors']);
unset($_SESSION['form_data']);
?>

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__tab-bar">
        <a href="#tabNewAdmin" class="mdl-tabs__tab is-active">NUEVO PERSONAL</a>
        <a href="#tabListAdmin" class="mdl-tabs__tab">LISTA DE PERSONAL</a>
    </div>
    <div class="mdl-tabs__panel is-active" id="tabNewAdmin">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <div class="full-width panel mdl-shadow--2dp">
                    <div class="full-width panel-tittle bg-primary text-center tittles">
                        Nuevo Personal
                    </div>
                    <div class="full-width panel-content">
                    <form action="../controlador_admin/ct_PU.php" method="POST" id="formulario" enctype="multipart/form-data">
                            <div class="mdl-grid">
                                <!--<div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACIÓN</legend><br>
                                </div>-->
                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php echo isset($errors['nombre']) ? ' is-invalid' : ''; ?>" id="nombre-field">
                                        <input class="mdl-textfield__input" type="text" id="nombre" name="nombre"  value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="nombre">Nombre</label>
                                        <span class="mdl-textfield__error"><?php echo htmlspecialchars($errors['nombre'] ?? ''); ?></span>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php echo isset($errors['apellido1']) ? ' is-invalid' : ''; ?>" id="apellido1-field">
                                        <input class="mdl-textfield__input" type="text" id="apellido1" name="apellido1" value="<?php echo htmlspecialchars($form_data['apellido1'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="apellido1">Apellido Paterno</label>
                                        <span class="mdl-textfield__error"><?php echo htmlspecialchars($errors['apellido1'] ?? ''); ?></span>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php echo isset($errors['apellido2']) ? ' is-invalid' : ''; ?>" id="apellido2-field">
                                        <input class="mdl-textfield__input" type="text" id="apellido2" name="apellido2"  value="<?php echo htmlspecialchars($form_data['apellido2'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="apellido2">Apellido Materno</label>
                                        <span class="mdl-textfield__error"><?php echo htmlspecialchars($errors['apellido2'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--4-col">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php echo isset($errors['ci']) ? ' is-invalid' : ''; ?>" id="ci-field">
                                        <input class="mdl-textfield__input" type="text" id="ci" name="ci"  maxlength="7" value="<?php echo htmlspecialchars($form_data['ci'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="ci">DNI</label>
                                        <span class="mdl-textfield__error"><?php echo htmlspecialchars($errors['ci'] ?? ''); ?></span>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php echo isset($errors['celular']) ? ' is-invalid' : ''; ?>" id="celular-field">
                                        <input class="mdl-textfield__input" type="text" id="celular" name="celular" maxlength="8" value="<?php echo htmlspecialchars($form_data['celular'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="celular">Celular</label>
                                        <span class="mdl-textfield__error"><?php echo htmlspecialchars($errors['celular'] ?? ''); ?></span>
                                    </div>
                                </div>

                                <div class="mdl-cell mdl-cell--4-col">
                                    <div class="mdl-textfield mdl-js-textfield" id="idRol-field">
                                        <select class="mdl-textfield__input" name="idRol" id="idRol" onchange="redirectIfNewRole(this)">
                                            <option value="" disabled selected>Selecciona Rol</option>
                                            <?php
                                            while ($fila = mysqli_fetch_assoc($result_roles)) {
                                                $selected = ($form_data['idRol'] ?? '') == $fila['idrol'] ? 'selected' : '';
                                                echo "<option value=\"" . htmlspecialchars($fila['idrol']) . "\" $selected>" . htmlspecialchars($fila['nombre']) . "</option>";
                                            }
                                            ?>
                                            <!-- Opción para añadir nuevo rol -->
                                            <option value="new-role" class="new-role-option">+ Añadir Nuevo Rol</option>
                                        </select>
                                        <span class="mdl-textfield__error" id="idRol-error">
                                            <?php echo $errores['idRol'] ?? ''; ?>
                                        </span>
                                    </div>
                                </div>

                                <script>
                                    function redirectIfNewRole(selectElement) {
                                        // Redirige a rol.php con el parámetro que activa la pestaña "AGREGAR NUEVO ROL"
                                        if (selectElement.value === 'new-role') {
                                            window.location.href = 'rol.php?tab=new-role';
                                        }
                                    }
                                </script>

                                <div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; Detalles de Cuenta</legend><br>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php echo isset($errors['nombreUsuario']) ? ' is-invalid' : ''; ?>" id="nombreUsuario-field">
                                        <input class="mdl-textfield__input" type="text" id="nombreUsuario" name="nombreUsuario" value="<?php echo htmlspecialchars($form_data['nombreUsuario'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="nombreUsuario">Nombre de Usuario</label>
                                        <span class="mdl-textfield__error"><?php echo htmlspecialchars($errors['nombreUsuario'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php echo isset($errors['pass']) ? ' is-invalid' : ''; ?>" id="pass-field">
                                        <input class="mdl-textfield__input" type="text" id="pass" name="pass" value="<?php echo htmlspecialchars($form_data['pass'] ?? ''); ?>">
                                        <label class="mdl-textfield__label" for="pass">Contraseña</label>
                                        <span class="mdl-textfield__error"><?php echo htmlspecialchars($errors['pass'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; Elegir Foto de Usuario</legend><br>
                                </div>

                                <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                                    <label id="file-upload-label" class="file-upload mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
                                        <span>Seleccionar Foto de Perfil</span>
                                        <input type="file" id="foto" name="foto" class="input-file">
                                    </label>
                                    <span class="mdl-textfield__error" id="avatar-error">Debe seleccionar una foto de perfil</span>
                                </div>

                                <!-- Contenedor para la previsualización de la imagen -->
                                <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                                    <div id="preview-container" style="width: 190px; height: 180px; text-align: center; border: 2px dashed #ccc; padding: 10px;">
                                        <img id="preview" src="#" style="width: 100%; height: 100%; opacity: 0.3; display: none;">
                                    </div>
                                </div>

                            </div>
                            <p class="text-center">
                                <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="agregar">
                                    <i class="zmdi zmdi-plus"></i>
                                </button>
                                <div class="mdl-tooltip" for="agregar">AGREGAR</div>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
                                        <!--  LISTA DE PERSONAL   -->
    <div class="mdl-tabs__panel" id="tabListAdmin">
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--8-col-desktop mdl-cell--2-offset-desktop">
            <div class="full-width panel mdl-shadow--2dp">
                <div class="full-width panel-tittle bg-success text-center tittles">
                    LISTA DE PERSONAL
                </div>
                <div class="full-width panel-content">
                    <form action="#">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                            <label class="mdl-button mdl-js-button mdl-button--icon" for="searchAdmin">
                                <i class="zmdi zmdi-search"></i>
                            </label>
                            <div class="mdl-textfield__expandable-holder">
                                <input class="mdl-textfield__input" type="text" id="searchAdmin" name="searchAdmin">
                                <label class="mdl-textfield__label"></label>
                            </div>
                        </div>
                    </form>
                    <?php
                        $query_personal = "SELECT p.foto, p.nombre, p.apellido1, p.apellido2, p.ci, u.idusuario, r.nombre AS rol 
                                            FROM persona AS p 
                                            INNER JOIN usuario AS u ON p.idpersona = u.persona_idpersona 
                                            INNER JOIN privilegio AS pv ON u.idusuario = pv.usuario_idusuario 
                                            INNER JOIN rol AS r ON pv.rol_idrol = r.idrol";
                        $result_personal = mysqli_query($conn, $query_personal);
                        if (!$result_personal) {
                            echo "Error en la consulta: " . mysqli_error($conn);
                        } elseif (mysqli_num_rows($result_personal) > 0) {
                            while ($row = mysqli_fetch_assoc($result_personal)) {
                        ?>
                                <div class="mdl-list__item mdl-list__item--two-line">
                                    <span class="mdl-list__item-primary-content">
                                        <img src="<?php echo htmlspecialchars($row['foto']); ?>" class="mdl-list__item-avatar" alt="Foto">
                                        <span><?php echo htmlspecialchars($row['rol']) . ' | ' . htmlspecialchars($row['nombre']) . ' ' . htmlspecialchars($row['apellido1']) . ' ' . htmlspecialchars($row['apellido2']); ?></span>
                                        <span class="mdl-list__item-sub-title"><?php echo htmlspecialchars($row['ci']); ?></span>
                                    </span>
                                    <div class="btn-right">
                                        <button class='mdl-button mdl-js-button mdl-button--icon' 
                                                onclick='window.location.href="editar_PU.php?idusuario=<?php echo $row["idusuario"]; ?>"'>
                                            <i class="zmdi zmdi-edit"></i>
                                        </button>
                                        <button class="btn danger mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect btn-delete" 
                                                onclick='confirmDeletion(<?php echo $row["idusuario"]; ?>)'>
                                            <i class="zmdi zmdi-delete"></i>
                                        </button>

                                    </div>
                                </div>
                                <hr class="mdl-list__item-divider"> <!-- Línea separadora -->
                        <?php
                            }
                        } else {
                            echo "<p>No se encontraron resultados</p>";
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once "validaciones/validaciones.php";
include_once "pie.php";

mysqli_close($conn);
?>
