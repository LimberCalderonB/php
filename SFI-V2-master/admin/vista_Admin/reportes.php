<?php
include_once "cabecera.php";
?>
<h2 class="text-center mb-4">Reportes</h2>
<section class="full-width text-center" style="padding: 40px 0;">
    
    <div class="container">
        <div class="row justify-content-center">
            <!-- Productos -->
            <a href="stock.php" class="tile shadow text-center">
                <div class="tile-icon">
                <i class="fi fi-ss-shirt-long-sleeve"></i>
                </div>
                <div class="tile-text">
                    <span class="text-condensedLight">Productos</span>
                    <p class="card-text">REPORTE DE PRODUCTOS</p>
                </div>
            </a>

            <!-- Ventas -->
            <a href="reporte_ventas.php" class="tile shadow text-center">
                <div class="tile-icon">
                    <i class="fi fi-ss-cart-shopping-fast"></i>
                </div>
                <div class="tile-text">
                    <span class="text-condensedLight">Ventas</span>
                    <p class="card-text">REPORTE DE VENTAS</p>
                </div>
            </a>

            <!-- Clientes -->
            <a href="reporte_clientes.php" class="tile shadow text-center">
                <div class="tile-icon">
                    <i class="fi fi-ss-user"></i>
                </div>
                <div class="tile-text">
                    <span class="text-condensedLight">Clientes</span>
                    <p class="card-text">REPORTE DE CLIENTES</p>
                </div>
            </a>

            <!-- Pedidos -->
            <a href="reporte_pedidos.php" class="tile shadow text-center">
                <div class="tile-icon">
                    <i class="fi fi-ss-paper-plane"></i>
                </div>
                <div class="tile-text">
                    <span class="text-condensedLight">Pedidos</span>
                    <p class="card-text">REPORTE DE PEDIDOS</p>
                </div>
            </a>

            <!-- Usuarios -->
            <a href="reporte_usuarios.php" class="tile shadow text-center">
                <div class="tile-icon">
                    <i class="fi fi-ss-users"></i>
                </div>
                <div class="tile-text">
                    <span class="text-condensedLight">Usuarios</span>
                    <p class="card-text">REPORTE DE USUARIOS</p>
                </div>
            </a>
        </div>
    </div>
</section>
<?php
include_once "pie.php";
?>

<style>
    .tile {
        background-color: #fff; /* Fondo blanco para las tarjetas */
        border-radius: 10px; /* Bordes redondeados */
        padding: 20px; /* Aumenté el padding */
        margin: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        min-height: 130px; /* Altura mínima */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Sombra sutil */
        text-decoration: none; /* Sin subrayado */
        color: inherit; /* Heredar color de texto */
    }

    .tile:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Sombra más intensa al pasar el mouse */
    }

    .tile-icon {
        font-size: 80px; /* Tamaño del ícono */
        color: #77767c; /* Cambié el color del ícono a azul */
        margin-bottom: 20px; /* Espacio entre el ícono y el texto */
    }

    .text-condensedLight {
        font-size: 1.6rem; /* Aumenté el tamaño del texto */
        font-weight: bold;
        display: block;
        margin-bottom: 5px; /* Espacio reducido entre el título y el párrafo */
    }

    .card-text {
        font-size: 1.1rem; /* Aumenté el tamaño del texto para que sea más legible */
        margin-bottom: 10px; /* Espacio entre el texto y el ícono */
    }

    .container {
        padding: 30px 0;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center; /* Centramos las tarjetas en la fila */
    }
</style>
