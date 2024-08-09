<?php

//ALERTA DE REGISTRO

if(isset($_SESSION['registro']) && $_SESSION['registro'] == true){
    echo "<script>
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Registro Exitoso',
        showConfirmButton: false,
        timer: 1500
    });
    </script>";
    unset($_SESSION['registro']); 
}
?>
<!--VALIDACION DE BOTON - VERDE -->
<script>
    document.getElementById('foto').addEventListener('change', function() {
        const label = document.getElementById('file-upload-label');
        if (this.files && this.files.length > 0) {
            label.classList.add('green-button');
        } else {
            label.classList.remove('green-button');
        }
    });
</script>

<style>
.green-button {
    background-color: green !important;
}
</style>

<!--ALERTA DE ELIMINACION-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(idprivilegio) {
        console.log("confirmDelete called with idprivilegio:", idprivilegio); // Añade esta línea para depuración

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteRecord(idprivilegio);
            }
        });
    }

    function deleteRecord(idprivilegio) {
        fetch('../../crud/personal/eliminar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `idprivilegio=${idprivilegio}&action=delete`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                }).then(() => {
                    location.reload(); // Recargar la página para reflejar los cambios
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message,
                    icon: "error"
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: "Error!",
                text: "Something went wrong!",
                icon: "error"
            });
        });
    }
</script>


<!--VALIDACIONES DE CAMPO DE PERSONAL-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('formulario').addEventListener('submit', function (event) {
            var camposValidos = true;
            var nombre = document.getElementById('nombre').value.trim();
            var apellido1 = document.getElementById('apellido1').value.trim();
            var apellido2 = document.getElementById('apellido2').value.trim();
            var ci = document.getElementById('ci').value.trim();
            var celular = document.getElementById('celular').value.trim();
            var nombreUsuario = document.getElementById('nombreUsuario').value.trim();
            var pass = document.getElementById('pass').value.trim();
            var idRol = document.getElementById('idRol').value;
            //var foto = document.getElementById('foto').value.trim(); 

            var camposValidos = true;

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
            if (apellido2 === '') {
                document.getElementById('apellido2-field').classList.add('is-invalid');
                document.getElementById('apellido2-error').innerText = 'El apellido materno es obligatorio';
                camposValidos = false;
            } else {
                document.getElementById('apellido2-field').classList.remove('is-invalid');
            }

            // Validación de DNI
            if (ci === '') {
                document.getElementById('ci-field').classList.add('is-invalid');
                document.getElementById('ci-error').innerText = 'El DNI es obligatorio';
                camposValidos = false;
            } else if (ci.length !== 7) {
                document.getElementById('ci-field').classList.add('is-invalid');
                document.getElementById('ci-error').innerText = 'El DNI debe tener exactamente 7 dígitos';
                camposValidos = false;
            } else {
                document.getElementById('ci-field').classList.remove('is-invalid');
            }

            // Validación de celular
            if (celular === '') {
                document.getElementById('celular-field').classList.add('is-invalid');
                document.getElementById('celular-error').innerText = 'El número de celular es obligatorio';
                camposValidos = false;
            } else if (celular.length !== 8) {
                document.getElementById('celular-field').classList.add('is-invalid');
                document.getElementById('celular-error').innerText = 'El número de celular debe tener exactamente 8 dígitos';
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

            // Validación de rol
            if (idRol === '') {
                document.getElementById('idRol-field').classList.add('is-invalid');
                document.getElementById('idRol-error').innerText = 'Debe seleccionar un rol';
                camposValidos = false;
            } else {
                document.getElementById('idRol-field').classList.remove('is-invalid');
            }

            // Validación de foto
            /*if (foto === '') {
                document.getElementById('foto').parentNode.classList.add('is-invalid');
                document.getElementById('avatar-error').innerText = 'Debe seleccionar una imagen';
                camposValidos = false;
            } else {
                document.getElementById('foto').parentNode.classList.remove('is-invalid');
            }*/

            if (!camposValidos) {
                event.preventDefault();  // Evitar que el formulario se envíe
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

    /* Estilo para el botón personalizado de selección de archivo */
    .file-upload {
        position: relative;
        overflow: hidden;
        margin: 10px;
        display: inline-block;
        text-align: center;
        background-color: #2196F3;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    .file-upload input[type='file'] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
    }
</style>
