<?php include_once "cabecera.php"; ?>


<!-- Encabezado -->
<div class="jumbotron" id="jumbotron">
    <div class="container text-center">
        <h1 class="display-4">ENCANTO</h1>
        <p class="lead">Encuentra las últimas tendencias en moda</p>
    </div>
</div>
<!--boton animado-->
    <div class="container text-center">
        <br>
        <br>
        <a href="tienda.php" class="animated-btn">Explorar productos</a>
    </div><br>  
<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-center">
            <div class="alert" style="background-color: #89edd4; border-color: #ddd;">
                <h1>CUANDO MÁS LOS NECESITAS</h1>
                <p>LOS INFALTABLES</p>
            </div>
            <br>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit sint eius nam ipsa facere nemo non, ea accusantium quisquam ipsam asperiores? Praesentium, eveniet deserunt aut excepturi id voluptate illo officiis?</p>
        </div>
    </div>
</div>

<!-- Productos destacados -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <a href="tienda.php" class="card shadow text-decoration-none">
                <div class="card-body text-center" style="background-color: #89edd4; border-color: #ddd;">
                    <h5 class="card-title">VESTIDOS CASUALES </h5>
                </div>
                <img src="assets/imagenes/vestido2.jpg" class="card-img-top" alt="Vestido Casual">
                
            </a>
        </div>
        <div class="col-md-6 mb-4">
            <a href="tienda.php" class="card shadow text-decoration-none">
                <div class="card-body text-center" style="background-color: #89edd4; border-color: #ddd;">
                    <h5 class="card-title">VESTIDOS CASUALES</h5>
                </div>
                <img src="assets/imagenes/vestido3.jpg" class="card-img-top" alt="Vestido Casual">
            </a>
        </div>
    </div>
</div>

<!-- Productos en oferta -->
<div class="container mt-5">
    <div class="row">
        <!-- Primer Producto -->
        <div class="col-md-4 mb-4">
            <a href="ver.php" class="card shadow text-decoration-none" id="1">
                <img src="assets/imagenes/vestido3.jpg" class="card-img-top" alt="Camisa a Cuadros">
                <div class="card-body text-center">
                    <h5 class="card-title">Camisa a Cuadros</h5>
                    <p class="card-text">Descripción de la camisa a cuadros.</p>
                </div>
            </a>
        </div>
        <!-- Segundo Producto -->
        <div class="col-md-4 mb-4">
            <a href="ver.php" class="card shadow text-decoration-none">
                <img src="assets/imagenes/vestido2.jpg" class="card-img-top" alt="Jeans Desgastados">
                <div class="card-body text-center">
                    <h5 class="card-title">Jeans Desgastados</h5>
                    <p class="card-text">Descripción de los jeans desgastados.</p>
                </div>
            </a>
        </div>
        <!-- Tercer Producto -->
        <div class="col-md-4 mb-4">
            <a href="ver.php" class="card shadow text-decoration-none">
                <img src="assets/imagenes/vestido3.jpg" class="card-img-top" alt="Chaqueta de Cuero">
                <div class="card-body text-center">
                    <h5 class="card-title">Chaqueta de Cuero</h5>
                    <p class="card-text">Descripción de la chaqueta de cuero.</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
    var images = [
        'assets/imagenes/vestido1.jpg', 
        'assets/imagenes/vestido2.jpg', 
        'assets/imagenes/vestido3.jpg'
    ]; // Lista de imágenes
    var currentIndex = 0;
    var jumbotron = document.getElementById('jumbotron');
    var preloadedImages = [];

    function preloadImages() {
        images.forEach(function(src) {
            var img = new Image();
            img.src = src;
            preloadedImages.push(img);
        });
    }

    function changeBackground() {
        currentIndex = (currentIndex + 1) % images.length; // Avanza al siguiente índice o regresa al primero si llega al final
        jumbotron.style.backgroundImage = 'url(' + images[currentIndex] + ')';
    }

    preloadImages();
    changeBackground(); // Mostrar la primera imagen inmediatamente
    setInterval(changeBackground, 5000); // Cambiar la imagen de fondo cada 5 segundos
</script>

<?php 
include_once "pie.php"; 
include_once "../css/boton.php";
?>

<style>
    .jumbotron {
        background-size: cover;
        background-position: center;
        color: #fff;
        padding: 120px 0; /* Ajusta el padding para hacer el jumbotron más largo */
        min-height: 400px; /* Altura mínima para el jumbotron */
    }
    .jumbotron h1 {
        font-size: 4rem;
    }
    .jumbotron p {
        font-size: 1.5rem;
    }
</style>
