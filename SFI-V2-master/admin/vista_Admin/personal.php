<?php

include_once "cabecera.php";
include_once "../../conexion.php";

$query_roles = "SELECT idrol, nombre FROM rol";
$result_roles = mysqli_query($conn, $query_roles);

?>
<br>
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__tab-bar">
        <a href="#tabNewAdmin" class="mdl-tabs__tab is-active">NUEVO</a>
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
                                <div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACIÓN</legend><br>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="ci-field">
                                        <input class="mdl-textfield__input" type="text" id="ci" name="ci" pattern="[0-9]*" inputmode="numeric" maxlength="7">
                                        <label class="mdl-textfield__label" for="ci">DNI</label>
                                        <span class="mdl-textfield__error" id="ci-error">El DNI debe tener exactamente 7 dígitos</span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="nombre-field">
                                        <input class="mdl-textfield__input" type="text" id="nombre" name="nombre">
                                        <label class="mdl-textfield__label" for="nombre">Nombre</label>
                                        <span class="mdl-textfield__error" id="nombre-error">Nombre inválido</span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="apellido1-field">
                                        <input class="mdl-textfield__input" type="text" id="apellido1" name="apellido1">
                                        <label class="mdl-textfield__label" for="apellido1">Apellido Paterno</label>
                                        <span class="mdl-textfield__error" id="apellido1-error">Apellido inválido</span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="apellido2-field">
                                        <input class="mdl-textfield__input" type="text" id="apellido2" name="apellido2">
                                        <label class="mdl-textfield__label" for="apellido2">Apellido Materno</label>
                                        <span class="mdl-textfield__error" id="apellido2-error">Apellido inválido</span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="celular-field">
                                        <input class="mdl-textfield__input" type="text" id="celular" name="celular" pattern="[0-9]*" inputmode="numeric" maxlength="8">
                                        <label class="mdl-textfield__label" for="celular">Celular</label>
                                        <span class="mdl-textfield__error" id="celular-error">El número de celular debe tener exactamente 8 dígitos</span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; TIPO DE CARGO</legend><br>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    <div class="mdl-textfield mdl-js-textfield" id="idRol-field">
                                        <select class="mdl-textfield__input" name="idRol" id="idRol">
                                            <option value="" disabled="" selected="">Selecciona Rol</option>
                                            <?php
                                            
                                            $query = "SELECT idrol, nombre FROM rol";
                                            $result = mysqli_query($conn, $query);
                                            while ($fila = mysqli_fetch_assoc($result)) {
                                                echo "<option value=\"" . $fila['idrol'] . "\">" . $fila['nombre'] . "</option>";
                                            }
                                            
                                            ?>
                                        </select>
                                        <span class="mdl-textfield__error" id="idRol-error">Debe seleccionar un rol</span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; Detalles de Cuenta</legend><br>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="nombreUsuario-field">
                                        <input class="mdl-textfield__input" type="text" id="nombreUsuario" name="nombreUsuario">
                                        <label class="mdl-textfield__label" for="nombreUsuario">Nombre de Usuario</label>
                                        <span class="mdl-textfield__error" id="nombreUsuario-error">Nombre de usuario inválido</span>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="pass-field">
                                        <input class="mdl-textfield__input" type="text" id="pass" name="pass">
                                        <label class="mdl-textfield__label" for="pass">Contraseña</label>
                                        <span class="mdl-textfield__error" id="pass-error">Contraseña inválida</span>
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
                        
                        $query_personal = "SELECT p.foto, p.nombre, p.apellido1, p.apellido2, p.ci, r.nombre AS rol 
                                            FROM persona AS p 
                                            INNER JOIN usuario AS u ON p.idpersona = u.persona_idpersona 
                                            INNER JOIN privilegio AS pv ON u.idusuario = pv.usuario_idusuario 
                                            INNER JOIN rol AS r ON pv.rol_idrol = r.idrol";

                        // Ejecutar la consulta
                        $result_personal = mysqli_query($conn, $query_personal);

                        if (!$result_personal) {
                            // Manejo de error en caso de fallo en la consulta
                            echo "Error en la consulta: " . mysqli_error($conn);
                        } elseif (mysqli_num_rows($result_personal) > 0) {
                            // Mostrar resultados
                            while ($row = mysqli_fetch_assoc($result_personal)) {
                        ?>
                                <div class="mdl-list__item mdl-list__item--two-line">
                                    <!-- Contenido de cada elemento de la lista -->
                                    <span class="mdl-list__item-primary-content">
                                        <img src="<?php echo htmlspecialchars($row['foto']); ?>" class="mdl-list__item-avatar" alt="Foto">
                                        <span><?php echo htmlspecialchars($row['rol']) . ' | ' . htmlspecialchars($row['nombre']) . ' ' . htmlspecialchars($row['apellido1']) . ' ' . htmlspecialchars($row['apellido2']); ?></span>
                                        <span class="mdl-list__item-sub-title"><?php echo htmlspecialchars($row['ci']); ?></span>
                                    </span>
                                    <!-- Acciones secundarias (eliminar, modificar) -->
                                    <span class="mdl-list__item-secondary-content" style="display: flex; flex-direction: column;">
                                                <button class='mdl-button mdl-js-button mdl-button--icon' onclick='showDetails(<?php echo $row["idprivilegio"]; ?>)'>
                                                    <i class='zmdi zmdi-eye'></i>
                                                </button>
                                                <button id='deleteBtn_<?php echo $row["idprivilegio"]; ?>' class='mdl-button mdl-js-button mdl-button--icon' onclick='confirmDelete(<?php echo $row["idprivilegio"]; ?>)'>
                                                    <i class='zmdi zmdi-delete'></i>
                                                </button>
                                    </span>
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
