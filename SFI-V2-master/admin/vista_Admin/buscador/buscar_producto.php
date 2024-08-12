<?php
include_once "../../../conexion.php";

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$searchWords = array_filter(explode(' ', $searchTerm));

$sql = "SELECT producto.*, 
               categoria.nombre AS categoria_nombre, 
               (producto.precio - (producto.precio * (producto.descuento / 100))) AS precio_con_descuento
        FROM producto 
        JOIN almacen ON producto.idproducto = almacen.producto_idproducto 
        JOIN categoria ON almacen.categoria_idcategoria = categoria.idcategoria
        WHERE 1=1"; 

foreach ($searchWords as $word) {
    $word = trim($word);
    if (!empty($word)) {
        $sql .= " AND (producto.nombre LIKE '%$word%' 
                OR producto.descripcion LIKE '%$word%' 
                OR producto.talla LIKE '%$word%' 
                OR producto.precio LIKE '%$word%' 
                OR categoria.nombre LIKE '%$word%' 
                OR producto.descuento LIKE '%$word%' 
                OR (producto.precio - (producto.precio * (producto.descuento / 100))) LIKE '%$word%')";
    }
}

$result = $conn->query($sql);
$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

$conn->close();

if (!empty($productos)) {
    foreach ($productos as $producto) {
        echo '<div class="mdl-card mdl-shadow--2dp full-width product-card">';
        echo '<div class="mdl-card__title">';
        echo '<div class="product-images">';
        $activeSet = false; 

        if (!empty($producto['img1'])) {
            echo '<img src="'. htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img1']) .'" alt="img de producto 1" class="img-responsive product-image ' . (!$activeSet ? 'active' : '') . '">';
            $activeSet = true;
        }

        if (!empty($producto['img2'])) {
            echo '<img src="'. htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img2']) .'" alt="img de producto 2" class="img-responsive product-image ' . (!$activeSet ? 'active' : '') . '">';
            if (!$activeSet) $activeSet = true;
        }

        if (!empty($producto['img3'])) {
            echo '<img src="'. htmlspecialchars('../vista_Admin/img/vestidos/' . $producto['img3']) .'" alt="img de producto 3" class="img-responsive product-image ' . (!$activeSet ? 'active' : '') . '">';
            if (!$activeSet) $activeSet = true; 
        }

        echo '</div>';
        echo '<button class="prev-button"><i class="fi fi-rr-angle-small-left"></i></button>';
        echo '<button class="next-button"><i class="fi fi-rr-angle-small-right"></i></button>';
        echo '</div>';
        echo '<div class="mdl-card__supporting-text">';
        echo '<div class="product-info">';
        echo '<small>CAT: ' . htmlspecialchars($producto['categoria_nombre']) . '</small>';
        echo '<small class="separator">|</small>';
        echo '<small>Talla:' . htmlspecialchars($producto['talla']) . '</small>';
        echo '</div>';
        echo '<div class="product-date"><small>' . htmlspecialchars($producto['fecha_actualizacion']) . '</small></div>';
        echo '</div>';
        echo '<div class="mdl-card__actions mdl-card--border">';
        echo '<div class="product-info"><small>' . htmlspecialchars($producto['nombre']) . '</small></div>';
        echo '<div class="product-price ' . ($producto['descuento'] > 0 ? 'discount' : '') . '">';
        if ($producto['descuento'] > 0) {
            echo '<span class="original-price">' . htmlspecialchars($producto['precio']) . '-Bs</span>';
            echo '| Des: ' . htmlspecialchars($producto['descuento']) . '%';
            echo '| Ahora: ' . number_format($producto['precio_con_descuento'], 2) . '-Bs';
        } else {
            echo htmlspecialchars($producto['precio']) . '-Bs';
        }
        echo '</div>';
        echo '<div class="btn-container">';
        echo '<form method="post" action="pagos.php" style="display:inline;">';
        echo '<input type="hidden" name="idproducto" value="' . $producto['idproducto'] . '">';
        echo '<button type="submit" class="btn success"><i class="fi fi-ss-social-network"></i><span>Seleccionar</span></button>';
        echo '</form>';
        echo '<div class="btn-right">';
        echo '<button class="btn primary mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect btn-update" onclick="location.href=\'editar_producto.php?idproducto=' . $producto['idproducto'] . '\'"><i class="zmdi zmdi-edit"></i></button>';
        echo '<button class="btn danger mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect btn-delete" data-id="' . $producto['idproducto'] . '"><i class="zmdi zmdi-delete"></i></button>';
        echo '</div></div></div></div>';
    }
} else {
    echo '<p>No se encontraron productos.</p>';
}
?>
