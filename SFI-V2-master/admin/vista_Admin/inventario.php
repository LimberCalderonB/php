<?php
include_once "cabecera.php";
include_once '../modelo_admin/mod_producto.php';

$modelo = new ModeloProducto();
$productosConDescuento = $modelo->obtenerProductosConDescuento();
$productosSinDescuento = $modelo->obtenerProductosSinDescuento();
?>

<div class="full-width panel-tittle bg-primary text-center tittles">
    ALMACEN DE PRODUCTOS 
</div>

<div class="btn-container">
    <a href="../generarPDF/inventario_pdf.php" target="_blank">
        <button class="btn-descargar">
            DESCARGAR DATOS   
            <i class="fi fi-rs-down-to-line"></i>
        </button>
    </a>
</div>


<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__tab-bar">
        <a href="#tabNewAdmin" class="mdl-tabs__tab is-active">SIN DESCUENTO</a>
        <a href="#tabListAdmin" class="mdl-tabs__tab">CON DESCUENTO</a>
    </div>
    <div class="mdl-tabs__panel is-active" id="tabNewAdmin">
        <div class="full-width divider-menu-h"></div>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
                <div class="table-responsive">
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                        <thead>
                            <tr>
                                <th>FECHA Y HORA</th>
                                <th>NOMBRE</th>
                                <th>CATEGORIA</th>
                                <th>TALLA</th>
                                <th>PRECIO</th>
                                <th>CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosSinDescuento as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['fecha_actualizacion']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['talla']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mdl-tabs__panel" id="tabListAdmin">
        <div class="full-width divider-menu-h"></div>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
                <div class="table-responsive">
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width table-responsive centered-table">
                        <thead>
                            <tr>
                                <th>FECHA Y HORA</th>
                                <th>NOMBRE</th>
                                <th>CATEGORIA</th>
                                <th>TALLA</th>
                                <th>PRECIO</th>
                                <th>DESCUENTO</th>
                                <th>P.DESCUENTO</th>
                                <th>CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosConDescuento as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['fecha_actualizacion']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['talla']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['descuento']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['precioConDescuento']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once "pie.php";
include_once "validaciones/val_inventario.php";
?>



<style>
    .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.product-search {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    width: 400px;
}

.btn-realizar-venta {
    background-color: #176098;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1rem;
    display: flex;
    align-items: center;
}

.btn-realizar-venta i {
    margin-right: 5px;
}

.productos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Ajusta el ancho mínimo a 280px */
    gap: 20px;
}

.product-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.product-images {
    position: relative;
    width: 100%;
    height: 200px; /* Ajusta la altura según sea necesario */
    overflow: hidden;
}

.product-image {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.product-image.active {
    display: block;
}

.prev-button, .next-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 5px;
    cursor: pointer;
    z-index: 1;
}

.prev-button {
    left: 5px;
}

.next-button {
    right: 5px;
}

.product-info {
    padding: 15px;
    display: flex;
    justify-content: space-between;
}

.product-info small {
    font-size: 0.875em; /* 14px, ajusta según tus necesidades */
}

.separator {
    margin: 0 5px;
}

.product-price {
    padding: 10px;
    font-size: 0.75rem; /* Reducido a 12px */
}

.product-price.discount {
    color: black;
}

.original-price {
    text-decoration: line-through;
    margin-right: 5px;
}

.btn-container {
    padding: 10px;
    display: flex;
    justify-content: flex-end;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    padding: 7px 10px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1rem;
    display: flex;
    align-items: center;
}

.btn-danger i {
    margin-right: 5px;
}

</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const prevButton = card.querySelector('.prev-button');
        const nextButton = card.querySelector('.next-button');
        const images = card.querySelectorAll('.product-image');
        let currentImageIndex = 0;

        prevButton.addEventListener('click', () => {
            images[currentImageIndex].classList.remove('active');
            currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
            images[currentImageIndex].classList.add('active');
        });

        nextButton.addEventListener('click', () => {
            images[currentImageIndex].classList.remove('active');
            currentImageIndex = (currentImageIndex + 1) % images.length;
            images[currentImageIndex].classList.add('active');
        });
    });
});

</script>
