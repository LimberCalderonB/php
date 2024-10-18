

<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-eliminar').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Botón eliminar clickeado'); // Para probar si el evento funciona
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
            // Hacer la petición AJAX para eliminar la cantidad ingresada
            $.ajax({
                type: 'POST',
                url: '../crud/productos/eliminar.php', // Cambia la URL si es necesario
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
<script>
function realTimeSearch(query) {
    if (query.length === 0) {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'buscador/search.php?query=' + encodeURIComponent(query), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const productos = JSON.parse(xhr.responseText);
            const tbody = document.querySelector('tbody'); // Asegúrate de que este selector sea correcto

            // Limpiar el contenido actual de la tabla
            tbody.innerHTML = '';

            // Añadir los resultados de la búsqueda a la tabla
            productos.forEach(producto => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${producto.fecha_actualizacion}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.categoria_nombre}</td>
                    <td>${producto.talla}</td>
                    <td>${producto.precio}</td>
                    <td>${producto.descuento}</td>
                    <td class="${producto.estado === 'disponible' ? 'estado-disponible' : 'estado-agotado'}">${producto.estado}</td>
                    <td>${producto.cantidad_disponible}</td>
                    <td>
                        <a href="#" class="btn-accion btn-editar">Editar</a>
                        <a href="#" class="btn-accion btn-eliminar">Eliminar</a>
                        <a href="#" class="btn-accion btn-detalles">Detalles</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
    };
    xhr.send();
}
</script>




<style>
.estado-disponible {
    color: #4CAF50;
    font-weight: bold;
}

.estado-agotado {
    color: red;
    font-weight: bold;
}


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
.card-todo {
    background-color: #a5baff; /* Color verde para completados */
    color: white;
}
.card-completados {
    background-color: #4CAF50; /* Color verde para completados */
    color: white;
}

.card-pendientes {
    background-color: #FF9800; /* Color naranja para pendientes */
    color: white;
}

.card-menor-precio {
    background-color: #F44336; /* Color rojo para menos vendidos */
    color: white;
}
.card-mayor-precio {
    background-color: #17539c; /* Color rojo para menos vendidos */
    color: white;
}
.card-disponibles {
    background-color: #7b5ca5; /* Color rojo para menos vendidos */
    color: white;
}
.card-agotados {
    background-color: #26294d; /* Color rojo para menos vendidos */
    color: white;
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
    .btn-container {
        padding: 5px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-descargar {
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
    }

    .btn-accion {
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        margin-right: 5px;
        font-size: 0.75rem; /* 14px */
        display: inline-block;
        transition: background-color 0.3s; /* Transición suave */
    }

    .btn-editar {
        background-color: #ffc107; /* Amarillo */
    }

    .btn-eliminar {
        background-color: #dc3545; /* Rojo */
    }

    .btn-detalles {
        background-color: #17a2b8; /* Cian */
    }

    .btn-accion:hover {
        opacity: 0.8;
    }
</style>

<style>
.centered-table th,
.centered-table td {
    text-align: center;
}

.table-responsive {
    overflow-x: auto;
}

.mdl-data-table {
    width: 100%;
    margin: auto;
}

.mdl-data-table th,
.mdl-data-table td {
    padding: 10px 14px;
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

/* Estilos para el contenedor del botón */
.btn-container {
    text-align: right;
    margin-top: 20px;
    margin-right: 20px;
}

/* Estilos para el botón de descarga */
.btn-descargar {
    background-color: #007bff; /* Color de fondo */
    color: #fff; /* Color del texto */
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px; /* Bordes redondeados */
    transition: background-color 0.3s, transform 0.3s; /* Transición suave para color y tamaño */
    display: inline-flex;
    align-items: center;
}

.btn-descargar i {
    margin-left: 10px; /* Espacio entre el texto y el ícono */
    font-size: 18px; /* Tamaño del ícono */
}

/* Estilos cuando se pasa el ratón sobre el botón */
.btn-descargar:hover {
    background-color: #0056b3; /* Color de fondo cuando se pasa el ratón */
    transform: scale(1.05); /* Aumenta el tamaño ligeramente */
}

.btn-descargar:focus {
    outline: none; /* Quitar el borde de enfoque */
}
</style>

<script>
// Función para exportar la tabla a CSV
function exportTableToCSV(filename, tableSelector) {
    var csv = [];
    var rows = document.querySelectorAll(tableSelector + " tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");

        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }

        csv.push(row.join(","));        
    }

    // Crear un enlace para descargar el archivo
    var csvFile;
    var downloadLink;

    csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);

    downloadLink.click();
}

// Asignar la función al botón para exportar la tabla activa
document.querySelector(".btn-descargar").addEventListener("click", function() {
    var activeTab = document.querySelector(".mdl-tabs__panel.is-active");
    exportTableToCSV("productos.csv", activeTab.querySelector("table").outerHTML);
});
</script>
