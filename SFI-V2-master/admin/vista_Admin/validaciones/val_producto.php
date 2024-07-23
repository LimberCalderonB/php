

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
    width: 100%;
}

.custom-file-upload input[type="file"] {
    display: none;
}

.custom-file-upload label {
    display: block;
    padding: 10px 20px;
    background-color: #194EF5;
    color: #fff;
    text-align: center;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.custom-file-upload label:hover {
    background-color: #0288d1;
}

.custom-file-upload label:active {
    background-color: #0277bd;
}
</style>

<!--navegacion de fotos de producto-->
<style>
    .product-images {
    position: relative;
    width: 100%;
    height: 300px; /* Ajusta la altura según sea necesario */
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