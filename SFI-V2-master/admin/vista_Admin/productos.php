    <?php
    include_once "cabecera.php";
    include_once "../../conexion.php";
    $query_categorias = "SELECT idcategoria, nombre FROM categoria";
    $result_categorias = mysqli_query($conn, $query_categorias);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ?>
    <div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
        <div class="mdl-tabs__tab-bar">
        <a href="#tabListProducts" class="mdl-tabs__tab is-active">LISTA DE PRODUCTOS</a>
            <a href="#tabNewProduct" class="mdl-tabs__tab">AGREGAR NUEVO PRODUCTO</a>
        </div>
                <div class="mdl-tabs__panel" id="tabNewProduct">
                    <div class="mdl-grid">
                        <div class="mdl-cell mdl-cell--12-col">
                            <div class="full-width panel mdl-shadow--2dp">
                            <div class="full-width panel-tittle bg-primary text-center tittles">
                                    Nuevo Producto
                            </div>

                        <div class="full-width panel-content">
                            <form action="../controlador_admin/ct_producto.php" method="POST" id="guardado" enctype="multipart/form-data">
                                <div class="mdl-grid">

                                <div class="col-md-6">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1" max="36" required inputmode="numeric" oninput="validateQuantity(this)" onblur="resetDefaultValue(this)">
                                    <div id="cantidadFeedback" class="feedback"></div>
                                </div>

                                    <div class="mdl-cell mdl-cell--12-col">
                                        <!--<legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACION DEL PRODUCTO</legend><br>-->
                                    </div>

                                    <div class="mdl-cell mdl-cell--3-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="text" pattern="-?[A-Za-z0-9áéíóúÁÉÍÓÚ-ñÑ ]*(\.[0-9]+)?" id="nombre" name="nombre">
                                            <label class="mdl-textfield__label" for="nombre">Nombre</label>
                                            <span class="mdl-textfield__error">Nombre Invalido</span>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="text" pattern="^[0-9]*\.?[0-9]*$" id="precio" name="precio" oninput="validatePrice(this)">
                                            <label class="mdl-textfield__label" for="precio">Precio</label>
                                            <span class="mdl-textfield__error">Precio Inválido</span>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="text" pattern="^[0-9]*\.?[0-9]{0,2}$" id="descuento" name="descuento" min="0" max="100" oninput="validateDiscount(this)">
                                            <label class="mdl-textfield__label" for="descuento">% Descuento</label>
                                            <span class="mdl-textfield__error">Descuento Inválido</span>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="text"  id="talla" name="talla">
                                            <label class="mdl-textfield__label" for="talla">Talla</label>
                                            <span class="mdl-textfield__error">Talla Invalida</span>
                                        </div>
                                    </div>
                                    <div class="mdl-cell mdl-cell--3-col">
                                        <div class="mdl-textfield mdl-js-textfield" id="idCategoria-field">
                                            <select class="mdl-textfield__input" name="categoria_idcategoria" id="categoria_idcategoria" onchange="redirectIfNewCategory(this)">
                                                <option value="" disabled selected>Selecciona Categoria</option>
                                                <?php while ($fila = mysqli_fetch_assoc($result_categorias)): ?>
                                                    <option value="<?php echo $fila['idcategoria']; ?>"><?php echo $fila['nombre']; ?></option>
                                                <?php endwhile; ?>
                                                <!-- Opción de nueva categoría con clase específica para estilizar -->
                                                <option value="new-category" class="new-category-option">+ Añadir Nueva Categoría</option>
                                            </select>
                                            <span class="mdl-textfield__error" id="idCategoria-error">Debe seleccionar una categoría</span>
                                        </div>
                                    </div>

                                    <script>
                                        function redirectIfNewCategory(selectElement) {
                                            // Redirige a la página de categorías con el parámetro 'tab=new-category'
                                            if (selectElement.value === 'new-category') {
                                                window.location.href = 'categoria.php?tab=new-category';
                                            }
                                        }
                                    </script>

                                    <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="text" pattern="-?[A-Za-z0-9áéíóúÁÉÍÓÚ-ñÑ ]*(\.[0-9]+)?" id="descripcion" name="descripcion">
                                            <label class="mdl-textfield__label" for="descripcion">Descripcion de Producto</label>
                                            <span class="mdl-textfield__error">Falta la Descripcción</span>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--3-col mdl-cell--4-col-tablet mdl-cell--6-col-phone">
                                        <div class="custom-file-upload">
                                            <input type="file" id="fileUpload1" accept="image/*" name="img1" onchange="previewImage(event, 1);" />
                                            <label for="fileUpload1" id="fileUploadLabel1">Seleccionar Imagen 1</label>
                                            
                                            <!-- Contenedor de previsualización -->
                                            <div id="previewContainer1" class="preview-container">
                                                <img id="preview1" src="#" class="preview-image">
                                                <button type="button" id="removeButton1" class="remove-button" onclick="removeImage(1)">X</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--3-col mdl-cell--4-col-tablet mdl-cell--6-col-phone">
                                        <div class="custom-file-upload">
                                            <input type="file" id="fileUpload2" accept="image/*" name="img2" onchange="previewImage(event, 2);" />
                                            <label for="fileUpload2" id="fileUploadLabel2">Seleccionar Imagen 2</label>
                                            
                                            <!-- Contenedor de previsualización -->
                                            <div id="previewContainer2" class="preview-container">
                                                <img id="preview2" src="#" class="preview-image">
                                                <button type="button" id="removeButton2" class="remove-button" onclick="removeImage(2)">X</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--3-col mdl-cell--4-col-tablet mdl-cell--6-col-phone">
                                        <div class="custom-file-upload">
                                            <input type="file" id="fileUpload3" accept="image/*" name="img3" onchange="previewImage(event, 3);" />
                                            <label for="fileUpload3" id="fileUploadLabel3">Seleccionar Imagen 3</label>
                                            
                                            <!-- Contenedor de previsualización -->
                                            <div id="previewContainer3" class="preview-container">
                                                <img id="preview3" src="#" class="preview-image">
                                                <button type="button" id="removeButton3" class="remove-button" onclick="removeImage(3)">X</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mdl-cell mdl-cell--12-col text-center">
    <div id="progressContainer" style="display:none; width: 100%; background-color: #e0e0e0; border-radius: 5px; margin-top: 20px;">
        <div id="progressBar" style="width: 0%; height: 20px; background-color: #4caf50; border-radius: 5px;"></div>
    </div>
    <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="btn-addProduct">
        <i class="zmdi zmdi-plus"></i>
    </button>
    <div class="mdl-tooltip" for="btn-addProduct">Agregar Producto</div>
</div>
<style>
    #progressContainer {
    width: 100%; /* Asegura que ocupe todo el ancho disponible */
    background-color: #e0e0e0; /* Fondo gris claro para el contenedor */
    border-radius: 5px; /* Bordes redondeados */
    margin-top: 20px; /* Espacio entre el botón y la barra de progreso */
    display: none; /* Inicialmente oculto */
}

#progressBar {
    height: 20px; /* Altura de la barra de progreso */
    background-color: #4caf50; /* Color verde para la barra */
    border-radius: 5px; /* Bordes redondeados */
    width: 0%; /* Comienza en 0% */
    transition: width 0.1s; /* Transición suave al cambiar el ancho */
}

</style>

<script>
    document.getElementById('guardado').addEventListener('submit', function (e) {
        e.preventDefault(); // Evitar el envío del formulario

        // Validar los campos del formulario
        if (this.checkValidity()) {
            const cantidad = parseInt(document.getElementById('cantidad').value);
            const btnAddProduct = document.getElementById('btn-addProduct');
            btnAddProduct.disabled = true; // Desactivar el botón

            // Mostrar la barra de progreso
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            progressContainer.style.display = 'block';

            // Simulación del proceso de carga
            let progress = 0;
            const interval = setInterval(() => {
                if (progress >= 100) {
                    clearInterval(interval);
                    // Aquí puedes enviar el formulario una vez que la barra de progreso esté completa
                    this.submit(); // Enviar el formulario
                } else {
                    progress += (100 / cantidad); // Incrementar según la cantidad
                    progressBar.style.width = progress + '%';
                }
            }, 100); // Actualiza cada 100 ms
        } else {
            // Si la validación falla, muestra un mensaje o haz otra cosa
            alert("Por favor, complete todos los campos requeridos correctamente.");
        }
    });

    // Validar la cantidad de productos
    function validateQuantity(input) {
        const value = parseInt(input.value);
        if (value < 1 || value > 36) {
            document.getElementById('cantidadFeedback').textContent = 'Cantidad debe estar entre 1 y 36.';
        } else {
            document.getElementById('cantidadFeedback').textContent = '';
        }
    }

    function resetDefaultValue(input) {
        if (input.value === '') {
            input.value = 1;
        }
    }
</script>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
$sql = "
WITH Imagenes AS (
    SELECT 
        producto.idproducto AS idproducto_img,
        MAX(COALESCE(producto.img1, '')) AS img1,
        MAX(COALESCE(producto.img2, '')) AS img2,
        MAX(COALESCE(producto.img3, '')) AS img3
    FROM 
        producto
    GROUP BY 
        producto.idproducto
),
CantidadDisponibles AS (
    SELECT 
        producto.nombre,
        producto.precio,
        producto.talla,
        producto.descuento,
        COUNT(*) AS cantidad_disponible
    FROM 
        producto
    JOIN 
        almacen ON producto.idproducto = almacen.producto_idproducto 
    WHERE 
        almacen.estado = 'disponible'
    GROUP BY 
        producto.nombre, producto.precio, producto.talla, producto.descuento
)
SELECT 
    producto.idproducto, 
    producto.nombre, 
    producto.precio, 
    producto.talla, 
    producto.descuento, 
    categoria.nombre AS categoria_nombre, 
    COUNT(almacen.producto_idproducto) AS cantidad,
    Imagenes.img1,
    Imagenes.img2,
    Imagenes.img3,
    almacen.estado
FROM 
    producto 
JOIN 
    almacen ON producto.idproducto = almacen.producto_idproducto 
JOIN 
    categoria ON almacen.categoria_idcategoria = categoria.idcategoria
JOIN 
    Imagenes ON producto.idproducto = Imagenes.idproducto_img
LEFT JOIN 
    CantidadDisponibles ON 
        producto.nombre = CantidadDisponibles.nombre AND 
        producto.precio = CantidadDisponibles.precio AND 
        producto.talla = CantidadDisponibles.talla AND 
        producto.descuento = CantidadDisponibles.descuento
WHERE
    almacen.estado IN ('disponible', 'agotado')
GROUP BY 
producto.idproducto,
    producto.nombre, 
    producto.precio, 
    producto.talla, 
    producto.descuento, 
    categoria.nombre, 

    almacen.estado
HAVING 
    (SELECT COUNT(*) FROM CantidadDisponibles WHERE 
        nombre = producto.nombre AND 
        precio = producto.precio AND 
        talla = producto.talla AND 
        descuento = producto.descuento) = 0 OR almacen.estado = 'disponible'
";

$result = $conn->query($sql);
$productos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}
$conn->close();
?>

<div class="mdl-tabs__panel is-active" id="tabListProducts">
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--12-col-desktop">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                <label class="mdl-button mdl-js-button mdl-button--icon" for="searchProduct">
                    <i class="zmdi zmdi-search"></i>    
                </label>
                <div class="mdl-textfield__expandable-holder">
                    <input class="mdl-textfield__input" type="text" id="searchProduct" onkeyup="searchProduct()" placeholder="Buscar productos...">
                    <label class="mdl-textfield__label" for="searchProduct"></label>
                </div>
            </div>
            <!-- Menú de navegación por categorías -->
            <div class="category-navigation">
                <?php 
                $categorias = array_unique(array_column($productos, 'categoria_nombre')); // Extraer categorías únicas
                ?>
                <ul class="category-menu">
                    <li>
                        <button class="category-btn" onclick="mostrarTodosProductos()">Todos</button>
                    </li>
                    <?php foreach ($categorias as $categoria): ?>
                        <li>
                            <button class="category-btn" onclick="filtrarProductosPorCategoria('<?php echo htmlspecialchars($categoria); ?>')">
                                <?php echo htmlspecialchars($categoria); ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div id="product-results" class="mdl-grid full-width text-center" style="display: flex; flex-wrap: wrap; justify-content: center; padding: 30px 0;">

    <?php if (!empty($productos)): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--4-col-tablet mdl-cell--3-col-desktop">
                <div class="mdl-card mdl-shadow--2dp full-width product-card" data-categoria="<?php echo htmlspecialchars($producto['categoria_nombre']); ?>">
                    <div class="mdl-card__title">
                        <!-- Contenido de la tarjeta del producto -->
                        <?php
                        $nombreCategoria = isset($producto['categoria_nombre']) ? htmlspecialchars($producto['categoria_nombre']) : 'default';
                        $directorioImagenes = 'img/categorias/' . $nombreCategoria . '/';
                        ?>
                        <div class="product-images">
                            <?php if (!empty($producto['img1'])): ?>
                                <img src="<?php echo htmlspecialchars($directorioImagenes . $producto['img1']); ?>" alt="img de producto 1" class="img-responsive product-image active">
                            <?php endif; ?>
                            <?php if (!empty($producto['img2'])): ?>
                                <img src="<?php echo htmlspecialchars($directorioImagenes . $producto['img2']); ?>" alt="img de producto 2" class="img-responsive product-image">
                            <?php endif; ?>
                            <?php if (!empty($producto['img3'])): ?>
                                <img src="<?php echo htmlspecialchars($directorioImagenes . $producto['img3']); ?>" alt="img de producto 3" class="img-responsive product-image">
                            <?php endif; ?>
                        </div>
                        <button class="prev-button">
                                            <i class="fi fi-rr-angle-small-left"></i>
                                        </button>
                                        <button class="next-button">
                                            <i class="fi fi-rr-angle-small-right"></i>
                                        </button>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <!-- Información del producto -->
                        <div class="product-info">
                            <small>CAT: <?php echo htmlspecialchars($producto['categoria_nombre']); ?></small>
                            <small class="separator">|</small>
                            <small>Talla: <?php echo htmlspecialchars($producto['talla']); ?></small>

                        </div>
                        <div class="btn-container">
                            <small>
                                <?php if ($producto['estado'] == 'agotado' || $producto['cantidad'] <= 0): ?>
                                    Cantidad: 0 (Agotado)
                                <?php else: ?>
                                    Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?>
                                <?php endif; ?>
                            </small>
                            <div class="btn-right">
                                <?php if ($producto['estado'] != 'agotado'): ?>
                                    <button onclick="mostrarAlerta('<?php echo $producto['idproducto']; ?>')" class="btn-aniadir">
                                        <span>Añadir</span>
                                        <i class="fi fi-sr-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <button onclick="mostrarAlerta('<?php echo $producto['idproducto']; ?>')" class="btn-aniadir">
                                        <span>Agotado</span>
                                        <i class="fi fi-sr-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="mdl-card__actions mdl-card--border">
                        <!-- Precios y botones adicionales -->
                        <div class="product-info">
                            <small><?php echo htmlspecialchars($producto['nombre']); ?></small>
                            <small class="separator">|</small>
                            <small> <?php echo htmlspecialchars($producto['estado']); ?></small>
                            <small class="separator">|</small>
                            <small> <?php echo htmlspecialchars($producto['idproducto']); ?></small>
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
                            <form method="post" action="buscar_similares.php" id="formSeleccionar<?php echo $producto['idproducto']; ?>" style="display:inline;">
                                <input type="hidden" name="idproducto" value="<?php echo $producto['idproducto']; ?>">
                                <input type="hidden" name="cantidad" id="cantidad<?php echo $producto['idproducto']; ?>" value="">
                                <span id="cantidadDisponible<?php echo $producto['idproducto']; ?>" style="display:none;">
                                    <?php echo htmlspecialchars($producto['cantidad']); ?>
                                </span>
                                <button type="button" 
                                    class="btn <?php echo ($producto['estado'] == 'disponible' && $producto['cantidad'] > 0) ? 'success' : 'agotado'; ?>" 
                                    onclick="seleccionarProducto('<?php echo $producto['idproducto']; ?>')" 
                                    <?php echo ($producto['estado'] == 'disponible' && $producto['cantidad'] > 0) ? '' : 'disabled'; ?>>
                                    <i class="fi fi-sr-shopping-cart"></i>
                                    <span><?php echo ($producto['estado'] == 'disponible' && $producto['cantidad'] > 0) ? 'Enviar' : 'Agotado'; ?></span>
                                </button>
                            </form>

                            <div class="btn-right">
                                <button class="btn primary mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect btn-update" onclick="location.href='editar_producto.php?idproducto=<?php echo $producto['idproducto']; ?>'">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button class="btn danger mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect btn-delete" data-id="<?php echo $producto['idproducto']; ?>">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No se encontraron productos.</p>
    <?php endif; ?>
</div>
        </div>
    </div>
</div>

<?php 
include_once "pie.php"; 
include_once "validaciones/val_producto.php";
?>

