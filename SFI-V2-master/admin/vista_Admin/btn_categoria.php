<div class="mdl-cell mdl-cell--4-col">
    <div class="custom-file-upload">
        <button id="agregar-categoria" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect">
            AGREGAR CATEGORIA
        </button>
    </div>
</div>

<script>
document.getElementById('agregar-categoria').addEventListener('click', function(event) {
    event.preventDefault();

    Swal.fire({
        title: 'Agregar Categoría',
        input: 'text',
        inputLabel: 'Nombre de la categoría',
        inputPlaceholder: 'Escribe el nombre de la categoría',
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Por favor, escribe el nombre de la categoría';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../controlador_admin/ct_categoria.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ categoria: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Categoría Agregada',
                        text: `La categoría "${result.value}" ha sido agregada exitosamente.`,
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al agregar la categoría.',
                        icon: 'error'
                    });
                }
            });
        }
    });
});
</script>