
<!--BUSCADOR DE CLIENTES-->
<script>
document.getElementById('buscar').addEventListener('input', function() {
    let query = this.value;
    if (query.length > 0) {
        fetch(`buscador/buscar_cliente.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                let resultadosDiv = document.getElementById('resultados');
                resultadosDiv.innerHTML = ''; // Limpiar resultados previos
                data.forEach(cliente => {
                    resultadosDiv.innerHTML += `
                        <div class="resultado" data-id="${cliente.idcliente}" data-nombre="${cliente.nombre_cliente} ${cliente.apellido_cliente} ${cliente.apellido2_cliente} (${cliente.ci_cliente})">
                            <p>${cliente.nombre_cliente} ${cliente.apellido_cliente} ${cliente.apellido2_cliente} (${cliente.celular_cliente})( ci :${cliente.ci_cliente})( ciudad :${cliente.departamento_cliente})</p>
                        </div>`;
                });
                resultadosDiv.style.display = data.length > 0 ? 'block' : 'none'; // Mostrar u ocultar los resultados
            });
    } else {
        document.getElementById('resultados').innerHTML = ''; // Limpiar resultados si la búsqueda está vacía
        document.getElementById('resultados').style.display = 'none'; // Ocultar resultados
    }
});

// Agregar evento de clic para seleccionar un resultado
document.addEventListener('click', function(event) {
    if (event.target.closest('.resultado')) {
        const selectedResult = event.target.closest('.resultado');
        const clienteId = selectedResult.getAttribute('data-id'); // Obtener el ID del cliente
        console.log('ID del cliente seleccionado:', clienteId); // Verificar ID
        document.getElementById('buscar').value = selectedResult.getAttribute('data-nombre'); // Completar el campo de búsqueda
        document.getElementById('idcliente').value = clienteId; // Guardar el ID del cliente en un campo oculto
        document.getElementById('resultados').innerHTML = ''; // Limpiar los resultados
        document.getElementById('resultados').style.display = 'none'; // Ocultar resultados
    }
});
</script>
<!--ESTILOS DE BUSQUEDA-->
<style>
#resultados {
    background: white; /* Fondo blanco para los resultados */
    border: 1px solid #ccc; /* Borde gris */
    border-radius: 4px; /* Bordes redondeados */
    max-height: 150px; /* Altura máxima */
    overflow-y: auto; /* Desplazamiento si hay muchos resultados */
    position: absolute; /* Posicionamiento absoluto */
    z-index: 1000; /* Para que se superponga sobre otros elementos */
    width: 500px; /* Ajustar al ancho deseado */
    left: 50%; /* Centrar horizontalmente */
    transform: translateX(-50%); /* Ajustar para que quede centrado */
    margin-top: 5px; /* Margen superior para separación */
    display: none; /* Ocultar por defecto */
}

.resultado {
    padding: 10px; /* Espaciado interno */
    border-bottom: 1px solid #eee; /* Línea entre resultados */
}

.resultado:last-child {
    border-bottom: none; /* Sin borde en el último elemento */
}

.resultado p {
    margin: 0; /* Eliminar márgenes del párrafo */
}
#buscar {
    padding: 10px 15px; /* Aumenta el relleno */
    border: 1px solid #ccc; /* Borde suave */
    border-radius: 5px; /* Esquinas redondeadas */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Sombra */
    width: 80%; /* Ajustar al 80% del contenedor */
    max-width: 400px; /* Máximo ancho para el campo */
    transition: border-color 0.3s; /* Transición suave para el borde */
}

#buscar:focus {
    border-color: #007bff; /* Cambia el color del borde al enfocar */
    outline: none; /* Sin borde de enfoque por defecto */
}

</style>
<!--FUNCIONALIDAD DEL CHECKBOX -->
<script>
    // Escuchar el evento 'change' en los checkboxes
    document.querySelectorAll('.select-product').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const row = checkbox.closest('tr'); // Obtener la fila correspondiente
            const solicitudInput = row.querySelector('.input-solicitud');

            if (checkbox.checked) {
                // Habilitar el campo de entrada de cantidad
                solicitudInput.disabled = false;
                solicitudInput.value = ''; // Limpiar valor anterior
                solicitudInput.focus(); // Llevar el foco al campo de entrada
            } else {
                // Deshabilitar el campo de entrada y limpiar el valor
                solicitudInput.disabled = true;
                solicitudInput.value = 0; // O dejar vacío si prefieres
            }
        });
    });

    // Validar el campo de entrada para permitir solo números
    document.querySelectorAll('.input-solicitud').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); // Remover todo lo que no sea un número
        });
    });
</script>

<style>
    /* Estilos para el botón de crear cliente */
    .btn-crear {
        background-color: #007bff; /* Color azul */
        color: white; /* Texto blanco */
        padding: 5px; /* Espaciado vertical */
        font-size: 0.8rem; /* Tamaño de fuente */
        border-radius: 5px; /* Bordes redondeados */
        transition: background-color 0.3s, transform 0.3s; /* Transición suave */
    }

    /* Efectos al pasar el mouse */
    .btn-crear:hover {
        background-color: #0056b3; /* Color más oscuro al pasar el mouse */
    }

    /* Estilos para el buscador */
    .input-buscar {
        width: 100%; /* Ancho completo */
        padding: 10px; /* Espaciado */
        border: 1px solid #ced4da; /* Borde */
        border-radius: 5px; /* Bordes redondeados */
        font-size: 1rem; /* Tamaño de fuente */
        transition: border-color 0.3s; /* Transición suave */
    }

    .input-buscar:focus {
        border-color: #007bff; /* Cambia el color del borde al enfocar */
        outline: none; /* Quitar el borde de enfoque */
    }

    /* Media Queries para Responsividad */
    @media (max-width: 768px) {
        .mdl-grid {
            display: flex;
            flex-direction: column; /* Cambiar a columna en pantallas más pequeñas */
            align-items: center; /* Centrar elementos */
        }

        .mdl-cell {
            width: 100%; /* Asegura que los elementos ocupen el 100% del ancho */
            margin-bottom: 10px; /* Espaciado entre los elementos */
        }

        .btn-crear {
            margin: 0; /* Sin margen en móviles */
        }
    }
</style>

<!--ESTILOS DE MENU DE CATEGORIAS-->
<style>
    /* Estilos para el Navbar */
    .navbar {
        background-color: #f8f9fa; /* Color de fondo del navbar */
        padding: 10px 20px; /* Espaciado interno */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Sombra suave */
    }

    .navbar-container {
        display: flex; /* Flexbox para alinear elementos en fila */
        justify-content: center; /* Centrar los elementos horizontalmente */
        flex-wrap: nowrap; /* No permitir que se envuelvan */
    }

    .navbar-menu {
        list-style: none; /* Eliminar los puntos de la lista */
        padding: 0; /* Sin relleno */
        margin: 0; /* Sin margen */
        display: flex; /* Usar flex para el menú */
    }

    .navbar-menu li {
        margin: 0 10px; /* Espaciado horizontal entre botones */
    }

    .navbar-menu a {
        display: block; /* Hacer que el enlace ocupe todo el espacio del li */
        padding: 10px 15px; /* Espaciado interno del enlace */
        background-color: #ffffff; /* Color de fondo de cada botón */
        color: #007bff; /* Color del texto */
        text-decoration: none; /* Sin subrayado */
        border-radius: 5px; /* Bordes redondeados */
        transition: background-color 0.3s, transform 0.3s; /* Transiciones suaves */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Sombra del botón */
    }

    .navbar-menu a:hover {
        background-color: #e9ecef; /* Color de fondo al pasar el mouse */
        transform: translateY(-2px); /* Efecto de elevación al pasar el mouse */
    }

    /* Media Queries para Responsividad */
    @media (max-width: 768px) {
        .navbar-container {
            flex-direction: column; /* Cambiar a columna en pantallas más pequeñas */
            align-items: center; /* Centrar elementos */
        }

        .navbar-menu {
            flex-direction: column; /* Cambiar a columna para el menú en móviles */
        }

        .navbar-menu li {
            margin: 5px 0; /* Espaciado vertical entre botones en móviles */
        }
    }
</style>

<!--ESTILOS DE TABLA DE PRODUCTOS-->
<style>
    /* Estilo general de la tabla */
    .styled-table {
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 0.75em;
        min-width: 80%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        background-color: #ffffff;
    }

    .styled-table thead tr {
        background-color: #009879;
        color: #ffffff;
        text-align: left;
    }

    .styled-table th,
    .styled-table td {
        padding: 7px 10px;
        text-align: center;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .styled-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #009879;
    }

    .styled-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .styled-table tbody td {
        transition: transform 0.3s;
    }

    /* Añadir efecto al pasar sobre las filas */
    .styled-table tbody tr:hover td {
        transform: scale(1.02);
    }

    /* Estilo para celdas con datos financieros */
    .styled-table td:nth-child(2), 
    .styled-table td:nth-child(4) {
        font-weight: bold;
        color: #009879;
    }
</style>
