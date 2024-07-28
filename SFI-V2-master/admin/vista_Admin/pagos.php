<?php
session_start();

// Manejar la selección del producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idproducto'])) {
    if (!isset($_SESSION['productos_seleccionados'])) {
        $_SESSION['productos_seleccionados'] = [];
    }

    $idproducto = $_POST['idproducto'];
    include_once "../../conexion.php";

    $sql = "SELECT producto.*, categoria.nombre AS categoria_nombre 
            FROM producto 
            JOIN almacen ON producto.idproducto = almacen.producto_idproducto 
            JOIN categoria ON almacen.categoria_idcategoria = categoria.idcategoria
            WHERE producto.idproducto = $idproducto";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        $_SESSION['productos_seleccionados'][$idproducto] = $producto;
    }

    $conn->close();
}

// Manejar la cancelación del producto
if (isset($_GET['cancelar_id'])) {
    $cancelar_id = $_GET['cancelar_id'];
    if (isset($_SESSION['productos_seleccionados'][$cancelar_id])) {
        unset($_SESSION['productos_seleccionados'][$cancelar_id]);
    }
}
?>

<?php include_once "cabecera.php"; ?>


<div class="full-width panel-tittle bg-primary text-center tittles">
    PRODUCTOS SELECCIONADOS
</div>
<br>
<div class="container">
    <div class="top-bar">
        <input type="text" id="product-search" placeholder="Buscar producto..." class="product-search">
        <button id="realizar-venta" class="btn btn-primary btn-realizar-venta">
            <i class="fi fi-rr-usd-circle"></i>
            REALIZAR VENTA
        </button>
    </div>
    <div class="productos-seleccionados">
        <?php if (!empty($_SESSION['productos_seleccionados'])): ?>
            <div class="productos-grid">
                <?php foreach ($_SESSION['productos_seleccionados'] as $producto): ?>
                    <div class="product-card">
                        <div class="product-images">
                            <?php if (!empty($producto['img1'])): ?>
                                <img src="<?php echo htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img1']); ?>" alt="img de producto 1" class="product-image active">
                            <?php endif; ?>
                            <?php if (!empty($producto['img2'])): ?>
                                <img src="<?php echo htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img2']); ?>" alt="img de producto 2" class="product-image">
                            <?php endif; ?>
                            <?php if (!empty($producto['img3'])): ?>
                                <img src="<?php echo htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img3']); ?>" alt="img de producto 3" class="product-image">
                            <?php endif; ?>
                            <button class="prev-button">
                                <i class="fi fi-rr-angle-small-left"></i>
                            </button>
                            <button class="next-button">
                                <i class="fi fi-rr-angle-small-right"></i>
                            </button>
                        </div>
                        <div class="product-info">
                            <small>Categoria: <?php echo htmlspecialchars($producto['categoria_nombre']); ?></small>
                            <small class="separator">|</small>
                            <small>Talla: <?php echo htmlspecialchars($producto['talla']); ?></small>
                        </div>
                        <div class="product-price <?php echo $producto['descuento'] > 0 ? 'discount' : ''; ?>">
                            <?php if ($producto['descuento'] > 0): ?>
                                <span class="original-price"><?php echo htmlspecialchars($producto['precio']); ?>-Bs</span> 
                                | Des: <?php echo htmlspecialchars($producto['descuento']); ?>%
                                | Ahora: <?php echo number_format($producto['precio'] - ($producto['precio'] * ($producto['descuento'] / 100)), 2); ?>-Bs
                            <?php else: ?>
                                <?php echo htmlspecialchars($producto['precio']); ?>-Bs
                            <?php endif; ?>
                        </div>
                        <div class="btn-container">
                            <a href="pagos.php?cancelar_id=<?php echo htmlspecialchars($producto['idproducto']); ?>" class="btn btn-danger">
                                <i class="fi fi-rs-cross"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No hay productos seleccionados.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once "pie.php"; ?>



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
    width: 200px;
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
