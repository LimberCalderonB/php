<style>


/* Botón para realizar la venta */
.btn-realizar-venta {
    background-color: #176098; /* Color de fondo del botón */
    color: white; /* Color del texto del botón */
    padding: 10px 20px; /* Espaciado interno del botón */
    border: none; /* Sin borde */
    border-radius: 5px; /* Bordes redondeados */
    font-size: 1rem; /* Tamaño de fuente del texto */
    display: flex; /* Flexbox para alinear el contenido */
    align-items: center; /* Alinea verticalmente el contenido */
    transition: transform 0.2s ease-in-out; /* Transición suave al hacer hover */
}

.btn-realizar-venta i {
    margin-right: 5px; /* Espacio entre el icono y el texto */
}

/* Efecto hover en el botón de venta */
.btn-realizar-venta:hover {
    transform: translateY(-5px); /* Mueve el botón hacia arriba al pasar el mouse */
}

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