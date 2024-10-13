<script>// Escuchar cambios en el checkbox
document.getElementById('habilitar-venta').addEventListener('change', function() {
    var botonRealizarVenta = document.getElementById('realizar-venta');

    if (this.checked) {
        // Si el checkbox está marcado, habilitar el botón y cambiar el color
        botonRealizarVenta.disabled = false;
        botonRealizarVenta.classList.add('habilitado');
    } else {
        // Si el checkbox está desmarcado, deshabilitar el botón y volver al color opaco
        botonRealizarVenta.disabled = true;
        botonRealizarVenta.classList.remove('habilitado');
    }
});
</script>

<script>document.addEventListener('DOMContentLoaded', function () {
    // Checkbox para habilitar el botón de Guardar Pedido y el buscador
    const checkboxPedido = document.getElementById('habilitar-pedido');
    const btnGuardarPedido = document.getElementById('guardar-pedido');
    const campoBuscar = document.getElementById('buscar');
    
    checkboxPedido.addEventListener('change', function () {
        if (this.checked) {
            // Habilitar el botón de Guardar Pedido y el campo de búsqueda
            btnGuardarPedido.disabled = false;
            btnGuardarPedido.style.backgroundColor = '#6c757d'; // Color gris
            btnGuardarPedido.style.opacity = '1'; // Activado

            campoBuscar.disabled = false;
            campoBuscar.style.backgroundColor = ''; // Restablecer fondo del campo de búsqueda
        } else {
            // Deshabilitar el botón de Guardar Pedido y el campo de búsqueda
            btnGuardarPedido.disabled = true;
            btnGuardarPedido.style.backgroundColor = ''; // Color por defecto
            btnGuardarPedido.style.opacity = '0.5'; // Opaco

            campoBuscar.disabled = true;
            campoBuscar.style.backgroundColor = '#e9ecef'; // Fondo gris claro para indicar que está deshabilitado
        }
    });
});

</script>

<!--SCRIPT DEL BUSCADOR -->
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
                            <p>${cliente.idcliente} = ${cliente.nombre_cliente} ${cliente.apellido_cliente} ${cliente.apellido2_cliente} (${cliente.celular_cliente}) (ci: ${cliente.ci_cliente}) (ciudad: ${cliente.departamento_cliente})</p>
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
        document.getElementById('buscar').value = selectedResult.getAttribute('data-nombre'); // Completar el campo de búsqueda con el nombre del cliente
        document.getElementById('idcliente').value = clienteId; // Guardar el ID del cliente en un campo oculto
        document.getElementById('resultados').innerHTML = ''; // Limpiar los resultados
        document.getElementById('resultados').style.display = 'none'; // Ocultar resultados
    }
});
</script>
<!--ESTILOS DE CAMPO DE BUSQUEDA-->
<style>
#resultados {
    background: white; /* Fondo blanco para los resultados */
    border: 1px solid #ccc; /* Borde gris */
    border-radius: 4px; /* Bordes redondeados */
    max-height: 150px; /* Altura máxima */
    overflow-y: auto; /* Desplazamiento si hay muchos resultados */
    position: absolute; /* Posicionamiento absoluto */
    z-index: 1000; /* Para que se superponga sobre otros elementos */
    width: 400px; /* Ajustar al ancho deseado */
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


<!--ALERTA DE BOTON DE CANCELAR--->
<script>
function cancelarProducto(idproducto, cantidadMaxima) {
    Swal.fire({
        title: 'Cancelar Productos',
        text: `¿Cuántos productos deseas cancelar? (Máximo: ${cantidadMaxima})`,
        input: 'number',
        inputAttributes: {
            min: 1,
            max: cantidadMaxima,  // Limitar el máximo al número de productos disponibles
            step: 1
        },
        showCancelButton: true,
        confirmButtonText: 'Cancelar Productos',
        cancelButtonText: 'Volver',
        inputValidator: (value) => {
            if (!value || isNaN(value) || value <= 0) {
                return 'Por favor, ingresa un número válido.';
            } else if (value > cantidadMaxima) {
                return `No puedes cancelar más de ${cantidadMaxima} productos.`;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let cantidad = result.value;
            if (cantidad !== null && cantidad > 0 && cantidad <= cantidadMaxima) {
                window.location.href = "pagos.php?cancelar_id=" + idproducto + "&cantidad=" + cantidad;
            }
        }
    });
}
</script>

<style>

/* Coste total, alineado a la derecha */
.total-cost {
    text-align: right; /* Alinea el texto a la derecha */
    font-size: 1.2rem; /* Tamaño de fuente del total */
    font-weight: bold; /* Negrita */
    margin-bottom: 20px; /* Margen inferior */
}

/* Grid para organizar las tarjetas de productos */
.productos-grid {
    display: flex;             /* Activamos flexbox */
    flex-wrap: wrap;           /* Permite que los elementos se envuelvan a la siguiente línea si no caben en una fila */
    justify-content: space-between; /* Distribuye las tarjetas uniformemente */
}

.product-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid #ddd;
    margin: 0px;             /* Añade espacio entre las tarjetas */
    padding: 1px;
    box-sizing: border-box;    /* Asegura que padding y borde se incluyan en el tamaño total */
    width: 200px;             /* Establece un ancho fijo para las tarjetas */
}

.container {
    max-width: 1200px; /* Limita el ancho del contenedor */
    margin: 0 auto;    /* Centra el contenedor */
    padding: 5px;
    box-sizing: border-box; /* Padding y borde se incluyen en el tamaño total */
}


/* Información del producto (categoría y talla) */
.product-info {
    padding: 5px; /* Espaciado interno de la sección de información */

    justify-content: space-between; /* Alinea los elementos a los extremos */
}

.product-info small {
    font-size: 0.85em; /* Tamaño de fuente reducido */
}

.separator {
    margin: 0 5px; /* Espacio entre la categoría y la talla */
}

/* Estilo del precio del producto */
.product-price {
    padding: 5px; /* Espaciado interno de la sección de precio */
    font-size: 0.85rem; /* Tamaño de fuente del precio */
}

.product-price.discount {
    color: #d9534f; /* Color rojo para precios con descuento */
}

/* Precio original con línea tachada */
.original-price {
    text-decoration: line-through; /* Tachado del precio original */
    margin-right: 5px; /* Espacio a la derecha del precio original */
}

/* Contenedor para el botón de cancelar */
.btn-container {
    padding: 5px; /* Espaciado interno del contenedor */
    display: flex; /* Flexbox para alinear el botón */

}

/* Estilo del botón de cancelar */
.btn-danger {
    background-color: #dc3545; /* Color de fondo rojo */
    color: white; /* Texto en blanco */
    padding: 4px 7px; /* Espaciado interno del botón */
    border: none; /* Sin borde */
    border-radius: 5px; /* Bordes redondeados */
    text-decoration: none; /* Sin subrayado en el texto */
    font-size: 1rem; /* Tamaño de fuente */
    display: flex; /* Flexbox para alinear el contenido */
    align-items: center; /* Alinea verticalmente el contenido */
}
</style>
<style>
    /* Botón para realizar la venta */
.btn-realizar-venta {
    font-size: 0.9rem; /* Reducido el tamaño de fuente del botón */
}

/* Coste total, alineado a la derecha */
.total-cost {
    font-size: 0.6rem; /* Reducido el tamaño de fuente del total */
}

/* Grid para organizar las tarjetas de productos */
.product-card {
    font-size: 0.5rem; /* Reducido el tamaño de fuente general de las tarjetas */
}

/* Información del producto (categoría y talla) */
.product-info small {
    font-size: 0.71rem; /* Tamaño de fuente aún más reducido para la información del producto */
}

/* Estilo del precio del producto */
.product-price {
    font-size: 0.67rem; /* Reducido el tamaño de fuente del precio */
}

.product-price.discount {
    font-size: 0.67rem; /* Aplicado también para los precios con descuento */
}

/* Precio original con línea tachada */
.original-price {
    font-size: 0.67rem; /* Reducido el tamaño de fuente del precio original */
}

/* Estilo del botón de cancelar */
.btn-danger {
    font-size: 0.9rem; /* Reducido el tamaño de fuente del botón cancelar */
}

</style>