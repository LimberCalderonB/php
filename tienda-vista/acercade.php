<?php
include_once "cabecera.php";
?>
<br>
<br>
<br>
<!-- Sección "ACERCA DE" -->
<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2>ACERCA DE NOSOTROS</h2>
            <p class="lead">Descubre más sobre nuestra tienda de vestidos para niñas y lo que nos hace especiales.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <h3>Nuestra Historia</h3>
            <p>En <strong>Encanto Niñas</strong>, nos dedicamos a ofrecer los vestidos más encantadores y de alta calidad para niñas de todas las edades. Desde nuestros inicios, hemos crecido y evolucionado, siempre manteniendo nuestro compromiso con la elegancia, la comodidad y la moda infantil.</p>
        </div>
        <div class="col-md-6 mb-4">
            <img src="assets/imagenes/vestido4.jpg" alt="Nuestra Historia" class="img-fluid rounded shadow">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="assets/fondo/mision.jpg"  alt="Nuestra Misión" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6 mb-4">
            <h3>Nuestra Misión</h3>
            <p>Nuestra misión es hacer que cada niña se sienta especial y única con nuestros vestidos. Nos esforzamos por ofrecer productos de calidad, diseños exclusivos y un servicio al cliente excepcional para garantizar la satisfacción de nuestros clientes.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <h3>Nuestra Visión</h3>
            <p>Aspiramos a ser la tienda líder en moda infantil, reconocida por nuestra dedicación a la excelencia y nuestra capacidad para anticiparnos a las tendencias del mercado. Queremos ser la primera opción para los padres que buscan ropa hermosa y duradera para sus hijas.</p>
        </div>
        <div class="col-md-6 mb-4">
            <img src="assets/fondo/vision.jpg" alt="Nuestra Visión" class="img-fluid rounded shadow">
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h3>Nuestros Valores</h3>
        </div>
        <div class="col-md-3 text-center mb-4">
            <img src="assets/imagenes/vestido4.jpg" alt="Calidad" class="img-fluid rounded-circle mb-3 shadow">
            <h4>Calidad</h4>
            <p>Nos comprometemos a ofrecer productos de la más alta calidad, garantizando la durabilidad y el confort de cada vestido.</p>
        </div>
        <div class="col-md-3 text-center mb-4">
            <img src="assets/imagenes/vestido1.jpg" alt="Innovación" class="img-fluid rounded-circle mb-3 shadow">
            <h4>Innovación</h4>
            <p>Nos mantenemos a la vanguardia de la moda infantil, introduciendo diseños frescos y únicos que encantan a niñas y padres por igual.</p>
        </div>
        <div class="col-md-3 text-center mb-4">
            <img src="assets/imagenes/vestido1.jpg" alt="Satisfacción" class="img-fluid rounded-circle mb-3 shadow">
            <h4>Satisfacción</h4>
            <p>Nuestra prioridad es la satisfacción del cliente, brindando un servicio amable, eficiente y siempre dispuesto a ayudar.</p>
        </div>
        <div class="col-md-3 text-center mb-4">
            <img src="assets/imagenes/vestido1.jpg" alt="Comunidad" class="img-fluid rounded-circle mb-3 shadow">
            <h4>Comunidad</h4>
            <p>Fomentamos un sentido de comunidad, apoyando causas locales y creando un impacto positivo en la sociedad.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h3>Nuestro Equipo</h3>
        </div>
        <div class="col-md-4 text-center mb-4">
            <img src="assets/imagenes/equipo1.jpg" alt="Fundadora" class="img-fluid rounded-circle mb-3 shadow">
            <h5>María López</h5>
            <p>Fundadora y Diseñadora Principal</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <img src="assets/imagenes/equipo2.jpg" alt="Gerente de Ventas" class="img-fluid rounded-circle mb-3 shadow">
            <h5>Carmen García</h5>
            <p>Gerente de Ventas</p>
        </div>
        <div class="col-md-4 text-center mb-4">
            <img src="assets/imagenes/equipo3.jpg" alt="Atención al Cliente" class="img-fluid rounded-circle mb-3 shadow">
            <h5>Laura Martínez</h5>
            <p>Especialista en Atención al Cliente</p>
        </div>
    </div>
</div>

<?php
include_once "pie.php";
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

img {
    width: 100%;
    height: auto;
}

.text-center {
    text-align: center;
}

.shadow {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.lead {
    font-size: 1.25rem;
    font-weight: 300;
}

.rounded-circle {
    border-radius: 50%;
}

.rounded {
    border-radius: 0.25rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: bold;
}

.card-text {
    font-size: 0.9rem;
    color: #6c757d;
}

footer {
    background-color: #343a40;
    color: #fff;
    padding: 10px 0;
    text-align: center;
    width: 100%;
    position: relative;
}
</style>
