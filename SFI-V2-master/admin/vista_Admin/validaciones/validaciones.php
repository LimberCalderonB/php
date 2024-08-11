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

<!--ALERTA DE ELIMINACION-->

<script>
    function confirmDeletion(idusuario) {
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
                // Enviar solicitud AJAX para eliminar
                $.ajax({
                    url: '../crud/personal/eliminar.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        idusuario: idusuario
                    },
                    success: function(response) {
                        let data = JSON.parse(response);
                        if (data.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "The record has been deleted.",
                                icon: "success"
                            }).then(() => {
                                window.location.reload(); // Recargar la página para reflejar cambios
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while processing your request.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>

<!--VALIDACIONES DE CAMPO DE PERSONAL
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
</script>-->

<style>
    .is-invalid .mdl-textfield__input {
        border-color: red;
    }
    .mdl-textfield__error {
        color: red;
    }
</style>

<!--FOTO DE PERFIL-->
<script>
    document.getElementById('foto').addEventListener('change', function(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var preview = document.getElementById('preview');
        preview.src = reader.result;
        preview.style.display = 'block'; // Mostrar la imagen
        preview.style.opacity = 1; // Hacer la imagen completamente visible
    };
    
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
});


</script>

<!--ESTILOS DE ANIMACION DE BOTON-->
<style>

#file-upload-label {
    display: inline-block;
    padding: 5px 20px;
    font-size: 11px;
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