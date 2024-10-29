<!--TAMAÑO DE LAS CARTAS DE LOS PRODUCTOS-->
<style>
.product-card {
    flex: 1 1 30%; /* Cada producto ocupa 30% del ancho disponible */
    min-width: 240px;
    max-width: 170px;
    margin-bottom: 15px; /* Reducido el espacio entre tarjetas */
    display: flex;
    flex-direction: column;
    align-items: center;
}

.product-images {
    position: relative;
    width: 180px; /* Reducido el ancho de las imágenes */
    height: 160px; /* Reducido la altura de las imágenes */
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
    top: 30%;
    transform: translateY(-70%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 0;
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
    font-size: 18px; /* Reducido ligeramente el tamaño del icono */
}
</style>

<!--ESTILO DE CAMBIO DE COLOR DE BOTON DE AÑADIR CUANDO ESTA AGOTADO-->
<style>
.btn.success {
    background-color: #4CAF50; /* Verde para disponible */
    color: white;
    cursor: pointer;
}

.btn.agotado {
    background-color: #FF5252; /* Rojo para agotado */
    color: white;
    cursor: not-allowed;
}
.mdl-card__supporting-text {
    color: rgba(5, 0, 0, .54);
    font-size: 1rem;
    line-height: 2px;
    overflow: hidden;
    padding: 8px;
    width: 90%;
}
</style>

<!--TAMAÑO DE LA LETRA DE CATEGORIA - TALLA - FECHA-->
<style>
.product-info {
    display: flex;
    align-items: center;
    font-size: 0.95rem; /* Reducido el tamaño */
}

.product-info small {
    margin-right: 3px; /* Espaciado más compacto */
}

.separator {
    margin: 0 1px; /* Espaciado más compacto */
}

.product-date {
    margin-top: 1px;
    font-size: 0.65rem; /* Reducido el tamaño de la fuente */
}
</style>
<style>
    .product-infor small{
        font-size: 0.8em; 
    }
</style>
<style>
    .product-info small,
    .product-date small,
    .product-price {
        font-size: 0.75em; 
    }

    .product-price.discount {
        color: black;
    }

    .original-price {
        text-decoration: line-through;
        margin-right: 5px;
    }
</style>
<!--ALERTA DE AÑADIR-->
<script>
function mostrarAlerta(idProducto) {
    // Usar SweetAlert2 para pedir la cantidad de productos
    Swal.fire({
        title: '¿Cuántos productos quieres añadir?',
        input: 'number', // Campo de entrada numérico
        inputAttributes: {
            min: 1 // Asegurarse de que el número sea positivo
        },
        showCancelButton: true, // Mostrar el botón de cancelar
        confirmButtonText: 'Añadir',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || isNaN(value) || value <= 0) {
                return 'Por favor, introduce un número válido.';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let cantidad = result.value; // Obtener el valor ingresado

            // Realizar la solicitud AJAX
            $.ajax({
                url: '../controlador_admin/ct_aniadir.php', // Archivo que gestionará la duplicación
                type: 'POST',
                data: {
                    idproducto: idProducto,
                    cantidad: cantidad
                },
                success: function(response) {
                    // Mostrar mensaje de éxito con SweetAlert2
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Se han añadido ' + cantidad + ' productos correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Redirigir a productos.php después de que el usuario presione "Aceptar"
                        window.location.href = 'productos.php';
                    });
                },
                error: function(xhr, status, error) {
                    // Mostrar error con SweetAlert2
                    Swal.fire('Error', 'Hubo un error al añadir los productos: ' + error, 'error');
                }
            });
        }
    });
}
</script>
<!--ESTILO DE BOTON DE AÑADIR-->
<style>
    /* Contenedor del botón */
.btn-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 10px 0;
    padding: 5px;
}

/* Estilo del botón "Añadir" */
.btn-aniadir {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #00b4d1; /* Color verde */
    color: white;
    font-size: 16px;
    font-weight: bold;
    padding: 3px 7px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    position: relative;
    overflow: hidden;
}

/* Animación de hover del botón */
.btn-aniadir:hover {
    background-color: #007897; /* Cambio a un tono de verde más oscuro */
    transform: translateY(-3px); /* Efecto de desplazamiento */
}

/* Estilo del icono dentro del botón */
.btn-aniadir i {
    margin-left: 8px;
    font-size: 18px;
}

/* Animación de clic en el botón */
.btn-aniadir:active {
    background-color: #004a66; /* Verde más oscuro al hacer clic */
    transform: translateY(0);
}

/* Efecto de onda al hacer clic */
.btn-aniadir::after {
    content: '';
    position: absolute;
    width: 300%;
    height: 300%;
    top: 50%;
    left: 50%;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.5s ease;
}

.btn-aniadir:active::after {
    transform: translate(-50%, -50%) scale(1);
}

/* Ajustes para dispositivos móviles */
@media (max-width: 768px) {
    .btn-aniadir {
        font-size: 14px;
        padding: 8px 16px;
    }
    .btn-aniadir i {
        font-size: 16px;
    }
}

</style>
<!--MANEJO DE CANTIDAD DE LOS PRODUCTOS QUE SE ENVIAN A LA PARTE DE PAGOS-->
<script>
function seleccionarProducto(idProducto) {
    Swal.fire({
        title: 'Seleccionar cantidad',
        input: 'number',
        inputLabel: 'Cantidad',
        inputPlaceholder: 'Ingrese la cantidad',
        inputAttributes: {
            min: 1,
            step: 1
        },
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value <= 0) {
                return 'Por favor ingrese una cantidad válida';
            }
            const cantidadDisponible = parseInt(document.getElementById('cantidadDisponible' + idProducto).textContent);
            if (value > cantidadDisponible) {
                return `No puedes seleccionar más de ${cantidadDisponible} productos.`;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const cantidadSeleccionada = parseInt(result.value);
            const cantidadDisponibleElement = document.getElementById('cantidadDisponible' + idProducto);
            const cantidadDisponibleActual = parseInt(cantidadDisponibleElement.textContent);

            // Actualizar cantidad disponible en la vista
            cantidadDisponibleElement.textContent = cantidadDisponibleActual - cantidadSeleccionada;

            // Asigna la cantidad seleccionada al campo oculto del formulario
            document.getElementById('cantidad' + idProducto).value = cantidadSeleccionada;
            document.getElementById('formSeleccionar' + idProducto).submit();
        }
    });
}
</script>

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
            var idproducto = this.getAttribute('data-id');
            promptDelete(idproducto);
        });
    });
});

function promptDelete(productId) {
    Swal.fire({
        title: '¿Cuántos productos quieres eliminar?',
        input: 'number',
        inputLabel: 'Cantidad',
        inputPlaceholder: 'Ingresa la cantidad',
        inputAttributes: {
            min: 1
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value <= 0) {
                return 'Por favor ingresa una cantidad válida.';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            var cantidad = result.value;
            $.ajax({
                type: 'POST',
                url: '../crud/productos/eliminar.php',
                data: {
                    idproducto: productId,
                    cantidad: cantidad
                },
                success: function(response) {
                    if (response.trim() === "success") {
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'Los productos han sido eliminados.',
                            icon: 'success'
                        }).then(() => {
                            location.reload(); // Recargar la página para reflejar los cambios
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response || 'Hubo un error al intentar eliminar los productos.',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Se produjo un error al intentar eliminar los productos.',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

</script>


<!--CAMPO DE CANTIDAD-->
<style>
.col-md-6 {
        position: relative;
}

.form-control {
        width: 100%; /* Hace que el campo de entrada ocupe el 100% del contenedor */
}

.feedback {
    display: none; /* Inicialmente oculto */
    color: red;
    margin-top: 5px; /* Espacio entre el campo y el mensaje de error */
    font-size: 0.65rem; /* Tamaño de fuente más pequeño para el mensaje de error */
}

.is-invalid {
    border-color: red; /* Cambia el borde del campo a rojo si es inválido */
}

/* Oculta los botones de incremento y decremento en los campos de tipo number */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
}

input[type="number"] {
        -moz-appearance: textfield; /* Oculta los botones en Firefox */
    }
</style>

<!--FUNCIONAMIENTO DE CANTIDAD-->
<script>
function validateQuantity(input) {
    const min = 1;
    const max = 36;

    // Solo procesa si el campo tiene algún valor
    if (input.value.trim() !== '') {
        input.value = input.value.replace(/[^0-9]/g, ''); // Elimina caracteres no numéricos

        let value = parseInt(input.value, 10);

        if (isNaN(value) || value < min) {
            input.value = min;
        } else if (value > max) {
            input.value = max;
        }
    }
}

function resetDefaultValue(input) {
    const defaultValue = 1;

    // Restablece el valor al predeterminado solo si el campo está vacío
    if (input.value.trim() === '') {
        input.value = defaultValue;
    }
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


<!--BOTON DE ANTES Y DESPUES-->
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
<!--VALIDACION DE NUERMEROS Y SIMBOLOS DE PRECIO-->
<script>
                                    function validatePrice(input) {
                                        // Elimina cualquier caracter que no sea un número o punto decimal
                                        input.value = input.value.replace(/[^0-9.]/g, '');
                                        
                                        // Asegura que solo haya un punto decimal
                                        const parts = input.value.split('.');
                                        if (parts.length > 2) {
                                            input.value = parts[0] + '.' + parts.slice(1).join('');
                                        }
                                    }
                                    </script>
<!--VALIDACION DE NUMEROS Y SIMBOLOS DE DESCUENTO-->
<script>
                                    function validateDiscount(input) {
                                        // Permite solo números y un punto decimal
                                        input.value = input.value.replace(/[^0-9.]/g, '');

                                        // Asegura que solo haya un punto decimal
                                        const parts = input.value.split('.');
                                        if (parts.length > 2) {
                                            input.value = parts[0] + '.' + parts.slice(1).join('');
                                        }

                                        // Limita a dos decimales
                                        if (parts[1] && parts[1].length > 2) {
                                            input.value = parts[0] + '.' + parts[1].substring(0, 2);
                                        }

                                        // Asegura que el valor no supere el máximo permitido
                                        const max = 100;
                                        if (parseFloat(input.value) > max) {
                                            input.value = max;
                                        }
                                    }
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


<!--Estilo responsivo de campos de imagenes-->
<!--IMG1-->
<style>
    .preview-container {
        position: relative;
        width: 100%;
        padding-top: 100%; /* Mantiene la relación de aspecto cuadrada */
        border: 2px dashed #ccc;
        text-align: center;
        display: block;
    }

    .preview-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.3;
        display: none;
    }

    .remove-button {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #dae2cb;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
    }
</style>
<!--IMGE 2 3-->
<style>
    .preview-container {
        position: relative;
        width: 100%;
        padding-top: 100%; /* Mantiene la relación de aspecto cuadrada */
        border: 2px dashed #ccc;
        text-align: center;
        display: block;
    }

    .preview-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.3;
        display: none;
    }

    .remove-button {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #dae2cb;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
    }
</style>
<style>
    .preview-container {
        position: relative;
        width: 100%;
        padding-top: 100%; /* Mantiene la relación de aspecto cuadrada */
        border: 2px dashed #ccc;
        text-align: center;
        display: block;
    }

    .preview-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.3;
        display: none;
    }

    .remove-button {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #dae2cb;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
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
    <!--BOTON PARA RETIRAR LA IMAGEN-->
    <script>
    function previewImage(event, index) {
        const fileInput = document.getElementById(`fileUpload${index}`);
        const previewImage = document.getElementById(`preview${index}`);
        const removeButton = document.getElementById(`removeButton${index}`);
        
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';  // Muestra la imagen de vista previa
                previewImage.style.opacity = '1';  // Asegura que la imagen sea completamente visible
                removeButton.style.display = 'block';  // Muestra el botón de eliminar
            };
            reader.readAsDataURL(file);
        }
    }
    //BOTON DE QUITAR LA IMAGEN SELECCIONADAS
    function removeImage(index) {
        const previewImage = document.getElementById(`preview${index}`);
        const removeButton = document.getElementById(`removeButton${index}`);
        
        previewImage.style.display = 'none';
        removeButton.style.display = 'none';
        
        const fileInput = document.getElementById(`fileUpload${index}`);
        fileInput.value = ''; 
    }
    </script>