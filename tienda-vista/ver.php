<?php
include_once "cabecera.php";

// Obtener el ID del producto desde la URL
$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Datos de ejemplo del producto
// En un caso real, deberías recuperar estos datos de la base de datos usando el ID
$productos = [
    1 => [
        'nombre' => 'Camiseta Azul',
        'descripcion' => 'Camiseta de algodón azul, cómoda y elegante.',
        'precio' => '29.99',
        'imagen' => 'ruta-a-imagen/camiseta-azul.jpg'
    ],
    2 => [
        'nombre' => 'Pantalones Negros',
        'descripcion' => 'Pantalones negros, ideales para cualquier ocasión.',
        'precio' => '39.99',
        'imagen' => 'ruta-a-imagen/pantalones-negros.jpg'
    ]
];

$product = isset($productos[$id_producto]) ? $productos[$id_producto] : null;

if (!$product) {
    echo '<p>Producto no encontrado.</p>';
    include_once "pie.php";
    include_once "../css/boton.php";
    exit;
}
?>
<div class="product">
    <h2>Camiseta Azul</h2>
    <p>Precio: $29.99</p>
    <button onclick="addToCart(1)">Agregar al carrito</button>
</div>


<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo htmlspecialchars($product['imagen']); ?>" alt="<?php echo htmlspecialchars($product['nombre']); ?>" class="img-fluid">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['nombre']); ?></h2>
            <p><?php echo htmlspecialchars($product['descripcion']); ?></p>
            <h4>Precio: $<?php echo htmlspecialchars($product['precio']); ?></h4>
            <a href="tienda.php" class="animated-btn">Agregar al carrito</a>
        </div>
    </div>
</div>

<?php
include_once "pie.php";
include_once "../css/boton.php";
?>
