<?php
include_once "cabecera.php";
include_once "../../conexion.php";

if (isset($_GET['idusuario'])) {
    $idusuario = $_GET['idusuario'];

    // Consulta para obtener los datos del usuario y persona
    $query = "SELECT u.idusuario, u.nombreUsuario, u.pass, p.idpersona, p.nombre, p.apellido1, p.apellido2, p.ci, p.celular, p.foto, r.idrol
              FROM usuario u
              INNER JOIN persona p ON u.persona_idpersona = p.idpersona
              INNER JOIN privilegio pv ON u.idusuario = pv.usuario_idusuario
              INNER JOIN rol r ON pv.rol_idrol = r.idrol
              WHERE u.idusuario = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idusuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit();
    }
} else {
    echo "ID de usuario no especificado.";
    exit();
}

// Obtener roles
$query_roles = "SELECT idrol, nombre FROM rol";
$result_roles = mysqli_query($conn, $query_roles);

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

unset($_SESSION['errors']);
unset($_SESSION['form_data']);
?>

<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--12-col">
        <div class="full-width panel mdl-shadow--2dp">
            <div class="full-width panel-tittle bg-primary text-center tittles">
                Editar Usuario
            </div>
            <div class="full-width panel-content">
                <form action="../controlador_admin/ct_editar_PU.php" method="POST" id="formulario" enctype="multipart/form-data">
                    <input type="hidden" name="idusuario" value="<?php echo htmlspecialchars($data['idusuario']); ?>">
                    <input type="hidden" name="idpersona" value="<?php echo htmlspecialchars($data['idpersona']); ?>">
                    <div class="mdl-grid">
                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACIÓN PERSONAL</legend><br>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['ci']) ? 'is-invalid' : ''; ?>" id="ci-field">
                                <input class="mdl-textfield__input" type="text" id="ci" name="ci" value="<?php echo isset($data['ci']) ? htmlspecialchars($data['ci']) : ''; ?>" maxlength="7">
                                <label class="mdl-textfield__label" for="ci">DNI</label>
                                <span class="mdl-textfield__error" id="ci-error"><?php echo isset($errors['ci']) ? htmlspecialchars($errors['ci']) : ''; ?></span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['celular']) ? 'is-invalid' : ''; ?>" id="celular-field">
                                <input class="mdl-textfield__input" type="text" id="celular" name="celular" value="<?php echo isset($data['celular']) ? htmlspecialchars($data['celular']) : ''; ?>" maxlength="8">
                                <label class="mdl-textfield__label" for="celular">Celular</label>
                                <span class="mdl-textfield__error" id="celular-error"><?php echo isset($errors['celular']) ? htmlspecialchars($errors['celular']) : ''; ?></span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['nombre']) ? 'is-invalid' : ''; ?>" id="nombre-field">
                                <input class="mdl-textfield__input" type="text" id="nombre" name="nombre" value="<?php echo isset($data['nombre']) ? htmlspecialchars($data['nombre']) : ''; ?>">
                                <label class="mdl-textfield__label" for="nombre">Nombre</label>
                                <span class="mdl-textfield__error" id="nombre-error"><?php echo isset($errors['nombre']) ? htmlspecialchars($errors['nombre']) : ''; ?></span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['apellido1']) ? 'is-invalid' : ''; ?>" id="apellido1-field">
                                <input class="mdl-textfield__input" type="text" id="apellido1" name="apellido1" value="<?php echo isset($data['apellido1']) ? htmlspecialchars($data['apellido1']) : ''; ?>">
                                <label class="mdl-textfield__label" for="apellido1">Apellido Paterno</label>
                                <span class="mdl-textfield__error" id="apellido1-error"><?php echo isset($errors['apellido1']) ? htmlspecialchars($errors['apellido1']) : ''; ?></span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['apellido2']) ? 'is-invalid' : ''; ?>" id="apellido2-field">
                                <input class="mdl-textfield__input" type="text" id="apellido2" name="apellido2" value="<?php echo isset($data['apellido2']) ? htmlspecialchars($data['apellido2']) : ''; ?>">
                                <label class="mdl-textfield__label" for="apellido2">Apellido Materno</label>
                                <span class="mdl-textfield__error" id="apellido2-error"><?php echo isset($errors['apellido2']) ? htmlspecialchars($errors['apellido2']) : ''; ?></span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; SELECCIONAR ROL</legend><br>
                        </div>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet" id="idRol-field">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['idRol']) ? 'is-invalid' : ''; ?>">
                                <select class="mdl-textfield__input" id="idRol" name="idRol">
                                    <?php while ($role = mysqli_fetch_assoc($result_roles)): ?>
                                        <option value="<?php echo htmlspecialchars($role['idrol']); ?>" 
                                            <?php echo isset($data['idrol']) && $data['idrol'] == $role['idrol'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <label class="mdl-textfield__label" for="idRol">Rol</label>
                                <span class="mdl-textfield__error" id="idRol-error"><?php echo isset($errors['idRol']) ? htmlspecialchars($errors['idRol']) : ''; ?></span>
                            </div>
                        </div>


                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; DETALLES DE CUENTA</legend><br>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['nombreUsuario']) ? 'is-invalid' : ''; ?>" id="nombreUsuario-field">
                                <input class="mdl-textfield__input" type="text" id="nombreUsuario" name="nombreUsuario" value="<?php echo isset($data['nombreUsuario']) ? htmlspecialchars($data['nombreUsuario']) : ''; ?>">
                                <label class="mdl-textfield__label" for="nombreUsuario">Nombre de Usuario</label>
                                <span class="mdl-textfield__error" id="nombreUsuario-error"><?php echo isset($errors['nombreUsuario']) ? htmlspecialchars($errors['nombreUsuario']) : ''; ?></span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($errors['pass']) ? 'is-invalid' : ''; ?>" id="pass-field">
                                <input class="mdl-textfield__input" type="text" id="pass" name="pass" value="<?php echo isset($data['pass']) ? htmlspecialchars($data['pass']) : ''; ?>">
                                <label class="mdl-textfield__label" for="pass">Contraseña</label>
                                <span class="mdl-textfield__error" id="pass-error"><?php echo isset($errors['pass']) ? htmlspecialchars($errors['pass']) : ''; ?></span>
                            </div>
                        </div>


                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; ELEGIR FOTO DE PERFIL</legend><br>
                        </div>

                        <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                            <label id="file-upload-label" class="file-upload mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
                                <span>Seleccionar Foto de Perfil</span>
                                <input type="file" id="foto" name="foto" class="input-file">
                            </label>
                            <span class="mdl-textfield__error" id="avatar-error">Debe seleccionar una foto de perfil</span>
                        </div>

                        <div class="mdl-cell mdl-cell--12-col">
                            <div id="preview-container" style="width: 190px; height: 180px; text-align: center; border: 2px dashed #ccc; padding: 10px;">
                                <?php if (!empty($data['foto']) && file_exists('' . $data['foto'])): ?>
                                    <img id="preview-image" src="<?php echo '' . $data['foto']; ?>" alt="Foto de perfil actual" style="width: 190px; height: 180px;">
                                <?php else: ?>
                                    <p>No hay foto de perfil disponible.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                            <div class="mdl-cell mdl-cell--12-col">
                                <p class="text-center">
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-primary" id="editar">Actualizar</button>
                                    <a href="personal.php" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-danger" onclick="clearSessionData()">
                                        Cancelar
                                    </a>
                                </p>
                            </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once "pie.php";
?>

<!--FOTO DE PERFIL-->
<script>
    document.getElementById('foto').addEventListener('change', function(event) {
        var preview = document.getElementById('preview-image');
        var file = event.target.files[0];
        var reader = new FileReader();

        reader.onloadend = function() {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
        }
    });
</script>
<!--ESTILOS DE ANIMACION DE BOTON-->
<style>

#file-upload-label {
    display: inline-block;
    padding: 5px 13px;
    font-size: 12px;
    font-weight: bold;
    color: #fff;
    background-color: #1976D2;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

#file-upload-label::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 300%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.1);
    transform: translateX(-50%) scaleX(0);
    transition: transform 0.4s ease;
    transform-origin: left;
}

#file-upload-label:hover::before {
    transform: translateX(-50%) scaleX(1);
}

#file-upload-label:hover {
    background-color: #1976D2; 
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

#file-upload-label span {
    position: relative;
    z-index: 1;
}

.input-file {
    display: none;
}

</style>