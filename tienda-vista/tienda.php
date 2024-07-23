<?php
include_once "cabecera.php";
?>

<!-- Contenido principal -->
<div class="container mt-5 pt-5">
    <div class="row">
        <!-- Producto Destacado -->
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100">
                <img src="assets/imagenes/vestido1.jpg" class="card-img-top" alt="Vestido 1">
                <div class="card-body">
                    <h5 class="card-title">Vestido Elegante</h5>
                    <p class="card-text">Un vestido elegante y cómodo para cualquier ocasión.</p>
                    <a href="ver.php" class="animated-btn">VER</a>
                </div>
            </div>
        </div>
        <!-- Lista de Productos -->
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100">
                <img src="assets/imagenes/vestido2.jpg" class="card-img-top" alt="Vestido 2">
                <div class="card-body">
                    <h5 class="card-title">Vestido Casual</h5>
                    <p class="card-text">Un vestido casual y moderno, perfecto para el día a día.</p>
                    <a href="ver.php" class="animated-btn">VER</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100">
                <img src="assets/imagenes/vestido3.jpg" class="card-img-top" alt="Vestido 3">
                <div class="card-body">
                    <h5 class="card-title">Vestido de Verano</h5>
                    <p class="card-text">Un vestido ligero y fresco, ideal para los días calurosos.</p>
                    <a href="ver.php" class="animated-btn">VER</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100">
                <img src="assets/imagenes/vestido1.jpg" class="card-img-top" alt="Vestido 1">
                <div class="card-body">
                    <h5 class="card-title">Vestido Elegante</h5>
                    <p class="card-text">Un vestido elegante y cómodo para cualquier ocasión.</p>
                    <a href="ver.php" class="animated-btn">VER</a>
                </div>
            </div>
        </div>
        <!-- Lista de Productos -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <img src="assets/imagenes/vestido2.jpg" class="card-img-top" alt="Vestido 2">
                <div class="card-body">
                    <h5 class="card-title">Vestido Casual</h5>
                    <p class="card-text">Un vestido casual y moderno, perfecto para el día a día.</p>
                    <a href="ver.php" class="animated-btn">VER</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <img src="assets/imagenes/vestido3.jpg" class="card-img-top" alt="Vestido 3">
                <div class="card-body">
                    <h5 class="card-title">Vestido de Verano</h5>
                    <p class="card-text">Un vestido ligero y fresco, ideal para los días calurosos.</p>
                    <a href="ver.php" class="animated-btn">VER</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <img src="assets/imagenes/vestido3.jpg" class="card-img-top" alt="Vestido 3">
                <div class="card-body">
                    <h5 class="card-title">Vestido de Verano</h5>
                    <p class="card-text">Un vestido ligero y fresco, ideal para los días calurosos.</p>
                    <a href="ver.php" class="animated-btn">VER</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once "pie.php";
include_once "../css/boton.php";
?>

<style>
body, html {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.container {
    flex: 1;
}

.card img {
    object-fit: cover;
    height: 200px; /* Ajusta según sea necesario */
}

.card-title {
    font-size: 1.25rem;
    font-weight: bold;
}

.card-text {
    font-size: 0.9rem;
    color: #6c757d;
}

.btn-primary {
    background-color: #343a40;
    border-color: #343a40;
}

.btn-primary:hover {
    background-color: #495057;
    border-color: #495057;
}

footer {
    background-color: #343a40;
    color: #fff;
    padding: 10px 0;
    text-align: center;
    width: 100%;
    position: static;
}
</style>
