

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
<!--ALERTA DE ELIMINACION DE PRODUCTO-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                var productId = this.getAttribute('data-id');
                confirmDelete(productId);
            });
        });
    });

    function confirmDelete(productId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Realizar la solicitud AJAX para eliminar el producto
                $.ajax({
                    type: 'POST',
                    url: '../crud/productos/eliminar.php',
                    data: {idproducto: productId},
                    success: function(response) {
                        // Mensaje de eliminación
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'El producto ha sido eliminado.',
                            icon: 'success'
                        }).then(() => {
                            // Recargar la página después de eliminar el producto
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Error de eliminación
                        Swal.fire({
                            title: 'Error',
                            text: 'Se produjo un error al intentar eliminar el producto.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
<!--Estilo de Boton de la imagen-->
<script>
    document.getElementById('fileUpload').addEventListener('change', function() {
    var fileName = this.files[0].name;
    document.getElementById('fileUploadLabel').textContent = fileName;
});
</script>
<!--Estilo de Boton de la imagen-->
<style>
        .custom-file-upload {
            position: relative;
            display: inline-block;
            width: 67%;
        }

        .custom-file-upload input[type="file"] {
            display: none;
        }

        .custom-file-upload label {
            display: block;
            padding: 7px 10px;
            background-color: #194EF5;
            color: #fff;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }

        .custom-file-upload label:hover {
            background-color: #0288d1;
        }

        .custom-file-upload label:active {
            background-color: #0277bd;
        }

        .custom-file-upload label.selected {
            background-color: #4CAF50;
        }
    </style>
<!--VISUALIZACION DE IMAGEN-->

<script>
    function previewImage(event, index) {
        var reader = new FileReader();
            reader.onload = function() {
        var preview = document.getElementById('preview' + index);
        var previewContainer = document.getElementById('previewContainer' + index);
        var removeButton = document.getElementById('removeButton' + index);

            preview.src = reader.result;
            preview.style.display = 'block';
            preview.style.opacity = 1;
            removeButton.style.display = 'block'; // Mostrar el botón de eliminación
        };

        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function removeImage(index) {
        document.getElementById('fileUpload' + index).value = ''; // Limpiar el input de archivo
        document.getElementById('preview' + index).style.display = 'none'; // Ocultar la imagen de previsualización
        document.getElementById('removeButton' + index).style.display = 'none'; // Ocultar el botón de eliminación
    }

</script>

<!--navegacion de fotos de producto-->
<style>
    .product-images {
    position: relative;
    width: 100%;
    height: 200px; /* Ajusta la altura según sea necesario */
    overflow: hidden;
}

.product-image {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.product-image.active {
    display: block;
}

.prev-button, .next-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 0px;
    cursor: pointer;
    z-index: 0;
}

.prev-button {
    left: 10px;
}

.next-button {
    right: 10px;
}

.prev-button i, .next-button i {
    font-size: 24px;
}

</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const images = card.querySelectorAll('.product-image');
        let currentIndex = 0;

        if (images.length > 0) {
            const showImage = (index) => {
                images.forEach((img, i) => {
                    img.classList.toggle('active', i === index);
                });
            };

            card.querySelector('.prev-button').addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                showImage(currentIndex);
            });

            card.querySelector('.next-button').addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % images.length;
                showImage(currentIndex);
            });

            // Mostrar la primera imagen al cargar
            showImage(currentIndex);
        } else {
            // Ocultar los botones si no hay imágenes
            card.querySelector('.prev-button').style.display = 'none';
            card.querySelector('.next-button').style.display = 'none';
        }
    });
});

</script>
<!--TAMAÑO DE LA LETRA DE CATEGORIA - TALLA - FECHA-->
<style>
.product-info {
    display: flex;
    align-items: center;
    font-size: 0.9rem; /* Ajusta el tamaño de la fuente si es necesario */
}

.product-info small {
    margin-right: 5px;
}

.separator {
    margin: 0 5px;
}

.product-date {
    margin-top: 5px;
    font-size: 0.75rem; /* Ajusta el tamaño de la fuente si es necesario */
}
</style>
<!--VALIDACIONES DE CAMPO-->
<script>
document.getElementById('btn-addProduct').addEventListener('click', function(event) {
    event.preventDefault(); // Evita el envío del formulario por defecto
    
    // Obtener todos los campos que se deben validar
    const fields = [
        { id: 'nombre', message: 'Nombre Invalido' },
        { id: 'precio', message: 'Precio Invalido' },
        { id: 'descuento', message: 'Descuento Invalido' },
        { id: 'descripcion', message: 'Falta la Descripcción' },
        { id: 'talla', message: 'Talla Invalida' },
        { id: 'categoria_idcategoria', message: 'Debe seleccionar una categoría' },
        { id: 'estado', message: 'Seleccione estado' }
    ];

    let isValid = true;

    fields.forEach(field => {
        const element = document.getElementById(field.id);
        if (element) {
            const value = element.value.trim();
            const parentElement = element.closest('.mdl-textfield');
            const errorMessageElement = parentElement.querySelector('.mdl-textfield__error');
            if (value === '') {
                parentElement.classList.add('is-invalid');
                if (errorMessageElement) errorMessageElement.textContent = field.message;
                isValid = false;
            } else {
                parentElement.classList.remove('is-invalid');
                if (errorMessageElement) errorMessageElement.textContent = '';
            }
        }
    });

    // Si todos los campos son válidos, enviar el formulario
    if (isValid) {
        document.getElementById('guardado').submit();
    }
});
</script>
<!--ESTILO DE VALIDACION DE CAMPO-->
<style>
    .is-invalid .mdl-textfield__input {
        border-color: red;
    }
    .mdl-textfield__error {
        color: red;
    }

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

<!--ESTILO DE BOTONES SELECCIONAR - ELIMINAR - EDITAR-->
<style>
    .btn-container {
        display: flex;
        justify-content: space-between; /* Espacio entre el contenedor izquierdo y derecho */
        align-items: center; /* Alinea verticalmente los botones */
        margin: 5px 0; /* Espacio alrededor del contenedor */
    }

    .btn-left {
        flex: 1; /* Toma todo el espacio disponible hacia la izquierda */
        display: flex;
        justify-content: flex-start; /* Alinea el botón a la izquierda */
    }

    .btn-right {
        display: flex;
        gap: 5px; /* Espacio entre los botones de la derecha */
    }

    .btn {
        border: none;
        color: white;
        padding: 3px 9px; /* Tamaño del botón */
        cursor: pointer;
        border-radius: 6px; /* Borde redondeado más sutil */
        display: inline-flex;
        align-items: center;
        font-size: 15px; /* Tamaño de fuente más grande */
        font-weight: bold; /* Fuente en negrita */
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Sombra sutil */
    }

    .btn i {
        margin-right: 8px; /* Espacio entre el icono y el texto */
        font-size: 18px; /* Tamaño del icono */
    }

    .btn span {
        display: inline-block;
        vertical-align: middle; /* Alinea el texto con el icono */
    }

    .primary {
        background-color: #007bff; /* Azul primario */
    }

    .primary:hover {
        background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Sombra más intensa en hover */
    }

    .primary:active {
        background-color: #004085; /* Azul aún más oscuro al hacer clic */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra sutil */
        transform: scale(0.98); /* Efecto de pulsación */
    }

    .danger {
        background-color: #f44336; /* Rojo */
    }

    .danger:hover {
        background-color: #da190b; /* Rojo más oscuro al pasar el ratón */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Sombra más intensa en hover */
    }

    .danger:active {
        background-color: #c62828; /* Rojo aún más oscuro al hacer clic */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra sutil */
        transform: scale(0.98); /* Efecto de pulsación */
    }

    .success {
        background-color: #04AA6D; /* Verde */
    }

    .success:hover {
        background-color: #46a049; /* Verde más oscuro al pasar el ratón */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Sombra más intensa en hover */
    }

    .success:active {
        background-color: #037d56; /* Verde más oscuro al hacer clic */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra sutil */
        transform: scale(0.98); /* Efecto de pulsación */
    }
</style>
<!--ESTILO DE DESCUENTO DE PRECIO-->

<style>
    .product-info small,
    .product-date small,
    .product-price {
        font-size: 0.875em; 
    }

    .product-price.discount {
        color: black;
    }

    .original-price {
        text-decoration: line-through;
        margin-right: 5px;
    }
</style>

<!----------------------------------------------------------------------------------------------------------------->
<!--VALIDACIONES DE EDITAR_PRODUCTO.PHP-->
<!----------------------------------------------------------------------------------------------------------------->
<script>
function removeImage(imageIndex) {
    var fileInput = document.getElementById('fileUpload' + imageIndex);
    var imgPreview = document.getElementById('imgPreview' + imageIndex);
    var removeInput = document.getElementById('remove_img' + imageIndex);
    var originalInput = document.querySelector('input[name="original_img' + imageIndex + '"]');
    
    // Limpiar el archivo seleccionado
    fileInput.value = ''; 
    
    // Vaciar la vista previa de la imagen
    imgPreview.src = ''; 
    
    // Marcar para eliminación
    removeInput.value = '1'; 
    
    // Limpiar el valor del archivo original
    originalInput.value = ''; 
}

function updatePreview(input, imgPreviewId) {
    var imgPreview = document.getElementById(imgPreviewId);
    var removeInput = document.getElementById('remove_' + imgPreviewId.slice(-1));
    var originalInput = document.querySelector('input[name="original_img' + imgPreviewId.slice(-1) + '"]');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            imgPreview.src = e.target.result; // Actualizar la vista previa con la nueva imagen
        };
        reader.readAsDataURL(input.files[0]);
        removeInput.value = '0'; // Desmarcar eliminación si se selecciona una nueva imagen
        originalInput.value = ''; // Limpiar el nombre original si se selecciona una nueva imagen
    } else {
        imgPreview.src = ''; // Vaciar la vista previa si no se selecciona un archivo
    }
}

</script>

<style>
    input[type="file"] {
    display: none;
}

   /* Estilo para el contenedor de carga de imágenes */
.file-upload-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

/* Estilo para las imágenes de vista*/
.img-thumbnail {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 4px;
    border: 2px solid #ddd;
    margin-bottom: 10px;
}

/* Oculta el input de tipo file */
input[type="file"] {
    display: none;
}

/* Estilo para el botón Seleccionar */
.file-upload-button {
    padding: 10px 20px;
    background-color: #4e48b0;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.file-upload-button:hover {
    background-color: #1565C0;
}

.file-upload-button:active {
    background-color: #0D47A1;
}

/* Estilo para el botón Quitar */
.btn-remove {
    padding: 10px 20px;
    background-color: #f44336;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-remove:hover {
    background-color: #e53935;
}

.btn-remove:active {
    background-color: #d32f2f;
}

/* Estilo para los botones dentro del contenedor de carga de imágenes */
.file-upload-controls {
    display: flex;
    justify-content: center;
    gap: 10px; /* Espacio entre botones */
    margin-top: 10px;
}

</style>

<!--ESTILO DE ALERTA-->
<style>
    .mdl-textfield__error {
    display: none; /* Oculta el mensaje de error por defecto */
}
.mdl-textfield.is-invalid .mdl-textfield__error {
    display: block; /* Muestra el mensaje de error si el campo es inválido */
}
</style>

<!--ESTILOS DE CANTIDAD-->
<style>
.form-label {
    font-weight: bold;
    color: #333;
}

.form-control {
    border: 2px solid #009688;
    border-radius: 8px;
    padding: 12px;
    font-size: 16px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.form-control:focus {
    border-color: #004d40;
    box-shadow: 0 0 8px rgba(0, 77, 64, 0.5);
    outline: none;
}

.invalid-feedback {
    display: none;
}

</style>
<script>
    document.getElementById('cantidad').addEventListener('input', function() {
        let value = this.value;
        let feedback = document.getElementById('cantidadFeedback');

        let filteredValue = value.replace(/[^0-9]/g, '');

        let numValue = parseInt(filteredValue, 10);

            if (filteredValue === '' || isNaN(numValue)) {
                this.value = '';
                feedback.style.display = 'none';
            } else if (numValue > 30) {
                this.value = 30;
                feedback.style.display = 'block';
            } else if (numValue < 0) {
                this.value = 0;
                feedback.style.display = 'block';
            } else {
                this.value = numValue;
                feedback.style.display = 'none';
            }
        });
</script>