
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Ropa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos personalizados */
        body {
            font-family: Arial, sans-serif;
        }
        .background {
            background-image: url('assets/fondo/fondo.jpg'); /* Verifica la ruta de la imagen */
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .navbar {
            background-color: #343a40 !important;
            transition: background-color 0.3s;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .nav-link {
            color: #fff !important;
            margin-right: 30px; /* Incrementa la separación */
        }
        .nav-link:hover {
            color: #ffc107 !important;
        }
        .navbar.scrolled {
            background-color: #000 !important;
        }
        .cart-icon {
            font-size: 1.5rem; /* Ajusta el tamaño del icono */
        }
        .cart-window {
            display: none;
            position: fixed;
            right: 0;
            top: 70px;
            width: 300px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 15px;
            border-radius: 5px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .animated-btn {
            background-color: #343a40;
            color: #fff;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 5px;
        }
        .animated-btn:hover {
            background-color: #ffc107;
            color: #343a40;
        }
    </style>
</head>
<body>

<div class="background"></div>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">ENCANTO</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">INICIO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tienda.php">TIENDA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ofertas.php">OFERTAS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="acercade.php">ACERCA DE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">LOGIN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="cart-icon"><i class="fas fa-shopping-cart cart-icon"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Ventana del carrito -->
<div class="cart-window" id="cart-window">
    <h4>Carrito de Compras</h4>
    <div class="cart-item">
        <span>Camiseta Azul</span>
        <span>$29.99</span>
    </div>
    <div class="cart-item">
        <span>Pantalones Negros</span>
        <span>$39.99</span>
    </div>
    <div>
    <button id="realizar-pedido">Realizar Pedido</button>
    
    </div>
   
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-qLxfk6mrmGmYPJLW39P+blNH4gARfU1yAXK94JqHbpaOcV+wwu1TgaSB9F9ZajE3" crossorigin="anonymous"></script>
<script>
    // Cambiar color de la barra de navegación al hacer scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mostrar/ocultar la ventana del carrito al hacer clic en el icono del carrito
    document.getElementById('cart-icon').addEventListener('click', function() {
        const cartWindow = document.getElementById('cart-window');
        if (cartWindow.style.display === 'none' || cartWindow.style.display === '') {
            cartWindow.style.display = 'block';
        } else {
            cartWindow.style.display = 'none';
        }
    });
</script>

