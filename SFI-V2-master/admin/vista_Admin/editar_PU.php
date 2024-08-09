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
?>

<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--12-col">
        <div class="full-width panel mdl-shadow--2dp">
            <div class="full-width panel-tittle bg-primary text-center tittles">
                Editar Usuario
            </div>
            <div class="full-width panel-content">
                <form action="../controlador_admin/ct_PU.php" method="POST" id="formulario" enctype="multipart/form-data">
                    <input type="hidden" name="idusuario" value="<?php echo $data['idusuario']; ?>">
                    <input type="hidden" name="idpersona" value="<?php echo $data['idpersona']; ?>">
                    <div class="mdl-grid">
                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACIÓN PERSONAL</legend><br>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="ci-field">
                                <input class="mdl-textfield__input" type="text" id="ci" name="ci" value="<?php echo htmlspecialchars($data['ci']); ?>" pattern="[0-9]*" inputmode="numeric" maxlength="7">
                                <label class="mdl-textfield__label" for="ci">DNI</label>
                                <span class="mdl-textfield__error" id="ci-error">El DNI es obligatorio</span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="celular-field">
                                <input class="mdl-textfield__input" type="text" id="celular" name="celular" value="<?php echo htmlspecialchars($data['celular']); ?>" pattern="[0-9]*" inputmode="numeric" maxlength="8">
                                <label class="mdl-textfield__label" for="celular">Celular</label>
                                <span class="mdl-textfield__error"  id="celular-error">El número de celular debe tener exactamente 8 dígitos</span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="nombre-field">
                                <input class="mdl-textfield__input" type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($data['nombre']); ?>" pattern="[a-zA-Z]*" inputmode="text">
                                <label class="mdl-textfield__label" for="nombre">Nombre</label>
                                <span class="mdl-textfield__error" id="nombre-error">Nombre inválido</span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="apellido1-field">
                                <input class="mdl-textfield__input" type="text" id="apellido1" name="apellido1" value="<?php echo htmlspecialchars($data['apellido1']); ?>" pattern="[a-zA-Z]*" inputmode="text">
                                <label class="mdl-textfield__label" for="apellido1">Apellido Paterno</label>
                                <span class="mdl-textfield__error" id="apellido1-error">Apellido inválido</span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="apellido2-field">
                                <input class="mdl-textfield__input" type="text" id="apellido2" name="apellido2" value="<?php echo htmlspecialchars($data['apellido2']); ?>" pattern="[a-zA-Z]*" inputmode="text">
                                <label class="mdl-textfield__label" for="apellido2">Apellido Materno</label>
                                <span class="mdl-textfield__error"  id="apellido2-error">Apellido inválido</span>
                            </div>
                        </div>

                        

                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; SELECCIONAR ROL</legend><br>
                        </div>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet" id="idRol-field">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <select class="mdl-textfield__input" id="idRol" name="idRol">
                                    <?php while ($role = mysqli_fetch_assoc($result_roles)): ?>
                                        <option value="<?php echo $role['idrol']; ?>" <?php echo $role['idrol'] == $data['idrol'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <label class="mdl-textfield__label" for="idRol">Rol</label>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--12-col">
                            <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; DETALLES DE CUENTA</legend><br>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="nombreUsuario-field">
                                <input class="mdl-textfield__input" type="text" id="nombreUsuario" name="nombreUsuario" value="<?php echo htmlspecialchars($data['nombreUsuario']); ?>">
                                <label class="mdl-textfield__label" for="nombreUsuario">Nombre de Usuario</label>
                                <span class="mdl-textfield__error" id="nombreUsuario-error">Nombre de usuario inválido</span>
                            </div>
                        </div>

                        <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" id="pass-field">
                                <input class="mdl-textfield__input" type="text" id="pass" name="pass" value="<?php echo htmlspecialchars($data['pass']); ?>">
                                <label class="mdl-textfield__label" for="pass">Contraseña</label>
                                <span class="mdl-textfield__error"  id="pass-error">Contraseña inválida</span>
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
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($data['foto']); ?>" alt="Foto de perfil actual" style="width: 150px; height: 150px;">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('formulario').addEventListener('submit', function (event) {
        var camposValidos = true;

        // Obtener los valores de los campos
        var nombre = document.getElementById('nombre').value.trim();
        var apellido1 = document.getElementById('apellido1').value.trim();
        var apellido2 = document.getElementById('apellido2').value.trim();
        var ci = document.getElementById('ci').value.trim();
        var celular = document.getElementById('celular').value.trim();
        var nombreUsuario = document.getElementById('nombreUsuario').value.trim();
        var pass = document.getElementById('pass').value.trim();
        var idRol = document.getElementById('idRol').value;
        var fotoInput = document.getElementById('foto');
        var foto = fotoInput.value.trim(); 

        // Validación de nombre
        if (nombre === '') {
            document.getElementById('nombre-field').classList.add('is-invalid');
            document.getElementById('nombre-error').innerText = 'El nombre es obligatorio';
            camposValidos = false;
        } else {
            document.getElementById('nombre-field').classList.remove('is-invalid');
        }

        // Validación de apellido paterno
        if (apellido1 === '') {
            document.getElementById('apellido1-field').classList.add('is-invalid');
            document.getElementById('apellido1-error').innerText = 'El apellido paterno es obligatorio';
            camposValidos = false;
        } else {
            document.getElementById('apellido1-field').classList.remove('is-invalid');
        }

        // Validación de apellido materno
        /*if (apellido2 === '') {
            document.getElementById('apellido2-field').classList.add('is-invalid');
            document.getElementById('apellido2-error').innerText = 'El apellido materno es obligatorio';
            camposValidos = false;
        } else {
            document.getElementById('apellido2-field').classList.remove('is-invalid');
        }*/

        // Validación de DNI
        if (ci === '') {
            document.getElementById('ci-field').classList.add('is-invalid');
            document.getElementById('ci-error').innerText = 'El DNI es obligatorio';
            camposValidos = false;
        } else if (ci.length !== 7 || !/^\d+$/.test(ci)) {
            document.getElementById('ci-field').classList.add('is-invalid');
            document.getElementById('ci-error').innerText = 'El DNI debe tener exactamente 7 dígitos numéricos';
            camposValidos = false;
        } else {
            document.getElementById('ci-field').classList.remove('is-invalid');
        }

        // Validación de celular
        if (celular === '') {
            document.getElementById('celular-field').classList.add('is-invalid');
            document.getElementById('celular-error').innerText = 'El número de celular es obligatorio';
            camposValidos = false;
        } else if (celular.length !== 8 || !/^\d+$/.test(celular)) {
            document.getElementById('celular-field').classList.add('is-invalid');
            document.getElementById('celular-error').innerText = 'El número de celular debe tener exactamente 8 dígitos numéricos';
            camposValidos = false;
        } else {
            document.getElementById('celular-field').classList.remove('is-invalid');
        }

        // Validación de nombre de usuario
        if (nombreUsuario === '') {
            document.getElementById('nombreUsuario-field').classList.add('is-invalid');
            document.getElementById('nombreUsuario-error').innerText = 'El nombre de usuario es obligatorio';
            camposValidos = false;
        } else {
            document.getElementById('nombreUsuario-field').classList.remove('is-invalid');
        }

        // Validación de contraseña
        if (pass === '') {
                document.getElementById('pass-field').classList.add('is-invalid');
                document.getElementById('pass-error').innerText = 'La contraseña es obligatoria';
                camposValidos = false;
            } else if (pass.length < 8 || !/[0-9]/.test(pass) || !/[a-zA-Z]/.test(pass) || !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pass)) {
                document.getElementById('pass-field').classList.add('is-invalid');
                document.getElementById('pass-error').innerText = 'La contraseña debe tener al menos 8 caracteres, incluyendo letras, números y caracteres especiales';
                camposValidos = false;
            } else {
                document.getElementById('pass-field').classList.remove('is-invalid');
            }

        

        // Si hay algún campo inválido, prevenimos el envío del formulario
        if (!camposValidos) {
            event.preventDefault();
        }
    });
});


</script>

<style>
    .is-invalid .mdl-textfield__input {
        border-color: red;
    }
    .mdl-textfield__error {
        color: red;
    }
</style>
