<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Pie de página -->
<!-- Pie de página -->
<footer class="footer">
  <div class="container">
    <div class="row">
      <!-- Enlaces de navegación -->
      <div class="col-md-3">
        <h5>Enlaces</h5>
        <ul class="footer-links">
          <li><a href="tienda.php">INICIO</a></li>
          <li><a href="tienda.php">TIENDA</a></li>
          <li><a href="tienda.php">OFERTAS</a></li>
          <li><a href="tienda.php">ACERCA DE</a></li>
          <li><a href="tienda.php">UNETENOS</a></li>
        </ul>
      </div>
      
      <!-- Información de contacto -->
      <div class="col-md-3">
        <h5>Contacto</h5>
        <ul class="footer-links">
          <li><p>MI NOMBRE</p></li>
          <li><a href="mailto:info@mitiendaderopa.com">info@mitiendaderopa.com</a></li>
          <li><a href="tel:+1234567890">+1 234 567 890</a></li>
        </ul>
      </div>
      
      <!-- Redes sociales -->
      <div class="col-md-3">
        <h5>REDES SOCIALES</h5>
        <ul class="footer-links social-links">
          <li><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
          <li><a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
          <li><a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
          <li><a href="#" aria-label="Tiktok"><i class="fab fa-tiktok"></i></a></li>
        </ul>
      </div>
      
      <!-- Derechos reservados -->
      <div class="col-md-3 text-center">
        <p>&copy; 2024 Mi Tienda de Ropa "ENCANTO". Todos los derechos reservados por Jhoan Limber Calderon Bernabe</p>
      </div>
    </div>
  </div>
</footer>


<style>
  .footer {
    background-color: #1ab188; /* Color de fondo del footer */
    color: #fff; /* Color del texto */
    padding: 40px 0; /* Espaciado superior e inferior */
    position: relative; /* Asegura que el pie de página esté posicionado correctamente */
    bottom: 0;
    width: 100%;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1); /* Sombra para un efecto de profundidad */
}

.footer .container {
    max-width: 1200px; /* Limita el ancho del contenido */
    margin: 0 auto; /* Centra el contenedor horizontalmente */
    padding: 0 15px; /* Espaciado horizontal */
}

.footer h5 {
    font-size: 18px; /* Tamaño de fuente para los títulos */
    margin-bottom: 20px; /* Espaciado debajo de los títulos */
    font-weight: bold; /* Hacer los títulos más prominentes */
}

.footer .footer-links {
    list-style: none; /* Elimina el estilo de lista */
    padding: 0; /* Elimina el padding por defecto */
    margin: 0; /* Elimina el margen por defecto */
}

.footer .footer-links li {
    margin-bottom: 10px; /* Espaciado entre elementos de la lista */
}

.footer .footer-links a {
    color: #fff; /* Color del texto de los enlaces */
    text-decoration: none; /* Elimina el subrayado de los enlaces */
    transition: color 0.3s, text-decoration 0.3s; /* Transición para el cambio de color y subrayado */
    font-size: 14px; /* Tamaño de fuente para los enlaces */
}

.footer .footer-links a:hover {
    color: #e0e0e0; /* Color del texto de los enlaces en hover */
    text-decoration: underline; /* Añadir subrayado en hover */
}

.social-links {
    display: flex; /* Alinea los íconos en fila */
    gap: 20px; /* Espacio entre íconos */
    justify-content: center; /* Centra los íconos horizontalmente */
    padding: 0; /* Elimina el padding por defecto */
}

.social-links li {
    margin: 0; /* Elimina el margen por defecto */
}

.social-links a {
    color: #fff; /* Color del texto de los enlaces */
    font-size: 24px; /* Tamaño del ícono */
    transition: color 0.3s; /* Transición para el cambio de color */
}

.social-links a:hover {
    color: #e0e0e0; /* Color del texto de los enlaces en hover */
}

.text-center {
    text-align: center; /* Centra el texto en el contenedor */
}

@media (max-width: 768px) {
    .footer h5 {
        font-size: 16px; /* Tamaño de fuente ajustado para pantallas más pequeñas */
    }
    
    .footer .footer-links a {
        font-size: 14px; /* Tamaño de fuente ajustado para los enlaces */
    }
}

</style>