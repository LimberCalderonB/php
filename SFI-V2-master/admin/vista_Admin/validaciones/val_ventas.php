
<script>
$(document).ready(function() {
    // Cuando el usuario escribe en el campo de búsqueda
    $('.search-input').on('keyup', function() {
        var busqueda = $(this).val(); // Obtener el valor del input

        // Enviar la solicitud AJAX
        $.ajax({
            url: 'buscador/buscar_ventas.php', // Archivo PHP que procesa la búsqueda
            method: 'POST',
            data: { busqueda: busqueda },
            success: function(response) {
                // Actualizar el contenido de la tabla con los resultados
                $('tbody').html(response);
            }
        });
    });
});
</script>

<style>
/* Estilos para el contenedor de tarjetas */
.menu-container {
    display: flex;
    justify-content: 10px; /* Espacio entre las cartas */
    margin: 10px 0; /* Espaciado vertical */
    flex-wrap: wrap; /* Permitir que las cartas se envuelvan en varias líneas */
}

/* Estilos generales para cada carta */
.card {
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    padding: 15px 20px;
    width: 210px; /* Ajusta el ancho */
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
    transition: transform 0.2s; /* Transición suave */
    margin: 7px; /* Espaciado entre cartas */
}

/* Estilo para el texto */
.card h3 {
    margin: 0;
    font-size: 16px; /* Reducir el tamaño de la letra */
    text-align: left;
}

/* Estilo para los iconos */
.card i {
    font-size: 45px; /* Tamaño del ícono */
    margin-left: 5px; /* Espacio entre el texto y el ícono */
}

/* Efecto hover */
.card:hover {
    transform: scale(1.05); /* Aumenta ligeramente el tamaño al pasar el mouse */
}

/* Colores individuales */
.card-completados {
    background-color: #4CAF50; /* Color verde para completados */
    color: white;
}

.card-pendientes {
    background-color: #FF9800; /* Color naranja para pendientes */
    color: white;
}

.card-menos-vendidos {
    background-color: #F44336; /* Color rojo para menos vendidos */
    color: white;
}
</style>


<!--Estilos de los botones de accion-->
<style>
.btn-container {
    display: flex; /* Alinea los botones en una sola fila */
    gap: 10px; /* Espacio entre los botones */
}

.btn-accion {
    padding: 5px 10px;
    font-size: 14px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    color: white; 
}

/* Diferentes colores para cada botón */
.btn-ver {
    background-color: #17a2b8;
}

.btn-ver:hover {
    background-color: #138496;
}

.btn-editar {
    background-color: #ffc107;
}

.btn-editar:hover {
    background-color: #e0a800;
}

.btn-eliminar {
    background-color: #dc3545;
}

.btn-eliminar:hover {
    background-color: #c82333;
}

.btn-reimprimir {
    background-color: #6c757d;
}

.btn-reimprimir:hover {
    background-color: #5a6268;
}
</style>

<style>
/* Contenedor de la paginación */
.pagination {
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 20px 0;
    list-style: none;
    gap: 10px; /* Espacio entre botones */
}

/* Estilo general de los botones de paginación */
.page-item {
    display: inline-block;
}

.page-link {
    color: #007bff; /* Color del texto del botón */
    background-color: white; /* Fondo del botón */
    border: 1px solid #007bff; /* Borde del botón */
    padding: 8px 16px; /* Espaciado dentro del botón */
    border-radius: 5px; /* Bordes redondeados */
    text-decoration: none; /* Quitar subrayado */
    transition: background-color 0.3s, color 0.3s, transform 0.3s; /* Efectos de transición */
}

/* Efecto hover para los botones */
.page-link:hover {
    background-color: #007bff;
    color: white;
    transform: scale(1.05); /* Efecto de agrandamiento */
}

/* Estilos para el botón activo */
.page-item.active .page-link {
    background-color: #0056b3; /* Fondo del botón activo */
    color: white; /* Color del texto del botón activo */
    border-color: #0056b3; /* Borde del botón activo */
    font-weight: bold; /* Negrita en el botón activo */
    transform: scale(1.1); /* Efecto de crecimiento en el botón activo */
}

/* Quitar el focus outline al hacer clic */
.page-link:focus {
    outline: none;
}
</style>

<style>
    /* Estilo para centrar el contenido de las celdas de la tabla */
.mdl-data-table th, .mdl-data-table td {
    text-align: center; /* Centra el texto en los encabezados y celdas */
}

/* Opcional: ajustar la altura de las filas para mejor apariencia */
.mdl-data-table tr {
    height: 50px; /* Altura de la fila */
}

</style>

<style>
/* Estilos generales */
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 20px;
}

.filter-container, .btn-container {
    margin: 7px 0;
}

.filter-form {
    display: flex;
    flex-direction: column;
}

.date-row {
    display: flex;
    justify-content: space-between;
    gap: 7px;
    align-items: center;
}

.date-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    flex-grow: 1;
}

.animated-label {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.input-fecha {
    padding: 5px;
    font-size: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s ease;
    width: 80%;
}

.input-fecha:hover, .input-fecha:focus {
    border-color: #007bff;
}

/* Botones */
.btn-filtrar, .btn-exportar {
    padding: 12px 15px;
    font-size: 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 20px;
}

.btn-filtrar {
    background-color: #28a745;
    color: white;
}

.btn-filtrar:hover {
    background-color: #218838;
}

.btn-exportar {
    background-color: #17a2b8;
    color: white;
}

.btn-exportar:hover {
    background-color: #138496;
}

/* Estilo responsive */
@media (max-width: 668px) {
    .date-row {
        flex-direction: column;
    }

    .container {
        flex-direction: column;
        align-items: center;
    }

    .filter-container, .btn-container {
        width: 100%;
    }
}
</style>

<style>
    /* Estilos para el buscador */
.search-container {
    margin: 20px 0;
}

.search-input {
    width: 250px;
    padding: 10px;
    margin: 0 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-buscar {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-buscar:hover {
    background-color: #0056b3;
}

/* Estilos para el filtro de fecha */
.date-filter-container {
    margin: 20px 0;
    text-align: left; /* Alinear el contenido a la derecha */
}

.date-input {
    padding: 10px;
    margin: 0 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-filter {
    padding: 10px 15px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-filter:hover {
    background-color: #218838;
}

</style>

<style>
    .centered-table th, .centered-table td {
        text-align: center;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .mdl-data-table {
        width: 100%;
        margin: auto;
    }

    .mdl-data-table th, .mdl-data-table td {/*anchi de la tabla */
        padding: 4px 30px;
    }

    .mdl-data-table th {
        background-color: #f2f2f2;
    }

    .mdl-data-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .mdl-data-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .btn-container {
        text-align: right;
        margin-top: 5px;
        margin-right: 5px;
    }

    .btn-descargar {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 6px 12px;/*tamaño de boton*/ 
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-descargar i {
        margin-left: 2px;
        font-size: 14px;
    }

    .btn-descargar:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .btn-descargar:focus {
        outline: none;
    }
</style>

<!--ESTILO DE LA PAGINACION-->
<style>
    /* Estilos generales para la paginación */
.pagination {
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 20px 0;
    list-style: none;
}

.page-item {
    margin: 0 5px;
}

.page-link {
    display: block;
    padding: 10px 15px;
    font-size: 1em;
    color: #007bff;
    text-decoration: none;
    border: 1px solid #007bff;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.page-link:hover {
    background-color: #007bff;
    color: #ffffff;
}

.page-item.active .page-link {
    background-color: #007bff;
    color: #ffffff;
    border-color: #007bff;
    font-weight: bold;
}

/* Agrega estilos para el estado deshabilitado (si es necesario) */
.page-item.disabled .page-link {
    color: #6c757d;
    border-color: #6c757d;
    cursor: not-allowed;
}

/* Asegúrate de que los estilos se apliquen en diferentes tamaños de pantalla */
@media (max-width: 768px) {
    .pagination {
        font-size: 0.9em;
    }
}

</style>

