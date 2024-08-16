    <?php

    include_once "cabecera.php";
    include_once "../../conexion.php";
    $query_categorias = "SELECT idcategoria, nombre FROM categoria";
    $result_categorias = mysqli_query($conn, $query_categorias);

    ?>

    <div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
        <div class="mdl-tabs__tab-bar">
        <a href="#tabListProducts" class="mdl-tabs__tab is-active">LISTA DE PRODUCTOS</a>
            <a href="#tabNewProduct" class="mdl-tabs__tab">NUEVO</a>
            
            
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

                                <div class="col-md-4">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" value="0" min="0" max="30" step="1" required inputmode="numeric">
                                    <div id="cantidadFeedback" class="feedback" style="display: none; color: red; margin-top: 5px;">
                                        La cantidad debe estar entre 0 y 30 y solo deben ser números.
                                    </div>
                                </div>

                                    <div class="mdl-cell mdl-cell--12-col">
                                        <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACION DEL PRODUCTO</legend><br>
                                    </div>

                                    <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="text" pattern="-?[A-Za-z0-9áéíóúÁÉÍÓÚ ]*(\.[0-9]+)?" id="nombre" name="nombre">
                                            <label class="mdl-textfield__label" for="nombre">Nombre</label>
                                            <span class="mdl-textfield__error">Nombre Invalido</span>
                                        </div>
                                    </div>
                                    <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="number" pattern="-?[0-9.]*(\.[0-9]+)?" id="precio" name="precio">
                                            <label class="mdl-textfield__label" for="precio">Precio</label>
                                            <span class="mdl-textfield__error">Precio Invalido</span>
                                        </div>
                                    </div>
                                    <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="number" pattern="-?[0-9]*(\.[0-9]+)?" id="descuento" name="descuento">
                                            <label class="mdl-textfield__label" for="descuento">% Descuento</label>
                                            <span class="mdl-textfield__error">Descuento Invalido</span>
                                        </div>
                                    </div>
                                    <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="number" pattern="-?[0-9]*(\.[0-9]+)?" id="talla" name="talla">
                                            <label class="mdl-textfield__label" for="talla">Talla</label>
                                            <span class="mdl-textfield__error">Talla Invalida</span>
                                        </div>
                                    </div>
                                    <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <input class="mdl-textfield__input" type="text" pattern="-?[A-Za-z0-9áéíóúÁÉÍÓÚ-ñÑ ]*(\.[0-9]+)?" id="descripcion" name="descripcion">
                                            <label class="mdl-textfield__label" for="descripcion">Descripcion de Producto</label>
                                            <span class="mdl-textfield__error">Falta la Descripcción</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mdl-cell mdl-cell--12-col">
                                        <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; CATEGORIA</legend><br>
                                    </div>
                                    <div class="mdl-cell mdl-cell--12-col">
                                        <div class="mdl-textfield mdl-js-textfield" id="idCategoria-field">
                                            <select class="mdl-textfield__input" name="categoria_idcategoria" id="categoria_idcategoria">
                                                <option value="" disabled="" selected="">Selecciona Categoria</option>
                                                <?php while ($fila = mysqli_fetch_assoc($result_categorias)): ?>
                                                    <option value="<?php echo $fila['idcategoria']; ?>"><?php echo $fila['nombre']; ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                            <span class="mdl-textfield__error" id="idCategoria-error">Debe seleccionar una categoría</span>
                                        </div>
                                    </div>
                                    <div class="mdl-cell mdl-cell--12-col">
                                        <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; OTROS DATOS</legend><br>
                                    </div>
                                    
                                    <div class="mdl-cell mdl-cell--3-col mdl-cell--0-col-tablet mdl-cell--2-col">
                                        <div class="custom-file-upload">
                                            <input type="file" id="fileUpload1" accept="image/*" name="img1" onchange="previewImage(event, 1);" />
                                            <label for="fileUpload1" id="fileUploadLabel1">Seleccionar Imagen 1</label>
                                            
                                            <!-- Contenedor de previsualización -->
                                            <div id="previewContainer1" style="position: relative; width: 150px; height: 150px; border: 2px dashed #ccc; text-align: center; display: block;">
                                                <img id="preview1" src="#" style="width: 100%; height: 100%; opacity: 0.3; display: none;">
                                                <button type="button" id="removeButton1" style="position: absolute; top: 5px; right: 5px; background-color: #dae2cb; color: white; border: none; border-radius: 50%; cursor: pointer; display: none;" onclick="removeImage(1)">X</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--3-col mdl-cell--0-col-tablet mdl-cell--2-col">
                                        <div class="custom-file-upload">
                                            <input type="file" id="fileUpload2" accept="image/*" name="img2" onchange="previewImage(event, 2);" />
                                            <label for="fileUpload2" id="fileUploadLabel2">Seleccionar Imagen 2</label>
                                            
                                            <!-- Contenedor de previsualización -->
                                            <div id="previewContainer2" style="position: relative; width: 150px; height: 150px; border: 2px dashed #ccc; text-align: center; display: block;">
                                                <img id="preview2" src="#" style="width: 100%; height: 100%; opacity: 0.3; display: none;">
                                                <button type="button" id="removeButton2" style="position: absolute; top: 5px; right: 5px; background-color: #dae2cb; color: white; border: none; border-radius: 50%; cursor: pointer; display: none;" onclick="removeImage(2)">X</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mdl-cell mdl-cell--3-col mdl-cell--0-col-tablet mdl-cell--2-col">
                                        <div class="custom-file-upload">
                                            <input type="file" id="fileUpload3" accept="image/*" name="img3" onchange="previewImage(event, 3);" />
                                            <label for="fileUpload3" id="fileUploadLabel3">Seleccionar Imagen 3</label>
                                            
                                            <!-- Contenedor de previsualización -->
                                            <div id="previewContainer3" style="position: relative; width: 150px; height: 150px; border: 2px dashed #ccc; text-align: center; display: block;">
                                                <img id="preview3" src="#" style="width: 100%; height: 100%; opacity: 0.3; display: none;">
                                                <button type="button" id="removeButton3" style="position: absolute; top: 5px; right: 5px; background-color: #dae2cb; color: white; border: none; border-radius: 50%; cursor: pointer; display: none;" onclick="removeImage(3)">X</button>
                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>
                                <div class="mdl-cell mdl-cell--12-col text-center">
                                    <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="btn-addProduct">
                                        <i class="zmdi zmdi-plus"></i>
                                    </button>
                                    <div class="mdl-tooltip" for="btn-addProduct">Agregar Producto</div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    //lista de productos
    include_once "../../conexion.php";

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta original sin el buscador
    $sql = "SELECT producto.*, categoria.nombre AS categoria_nombre 
            FROM producto 
            JOIN almacen ON producto.idproducto = almacen.producto_idproducto 
            JOIN categoria ON almacen.categoria_idcategoria = categoria.idcategoria";

    $result = $conn->query($sql);
    $productos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Excluir productos que ya están seleccionados
            if (!isset($_SESSION['productos_seleccionados'][$row['idproducto']])) {
                $productos[] = $row;
            }
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

    <div id="product-results" class="full-width text-center" style="padding: 30px 0;">
        <!-- CARGA DE LOS PRODUCTOS EN TIEMPO REAL -->

                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $producto): ?>
                            <div class="mdl-card mdl-shadow--2dp full-width product-card">
                                <div class="mdl-card__title">
                                <?php
                                // Supongamos que $nombreCategoria contiene el nombre de la categoría, por ejemplo, "Electronics"
                                $nombreCategoria = isset($producto['categoria_nombre']) ? htmlspecialchars($producto['categoria_nombre']) : 'default';

                                // Construir el directorio de imágenes basado en el nombre de la categoría
                                $directorioImagenes = 'img/categorias/' . $nombreCategoria . '/';
                                ?>

                                <!-- Mostrar las imágenes -->
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
                                    <div class="product-info">
                                        <small>CAT: <?php echo htmlspecialchars($producto['categoria_nombre']); ?></small>
                                        <small class="separator">|</small>
                                        <small>Talla:<?php echo htmlspecialchars($producto['talla']); ?></small>
                                    </div>
                                    <div class="product-date">
                                        
                                        <small>Cantidad: </small>
                                    </div>
                                </div>
                                <div class="mdl-card__actions mdl-card--border">
                                    <div class="product-info">
                                        <small><?php echo htmlspecialchars($producto['nombre']); ?></small>||
                                        
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
                                        <form method="post" action="pagos.php" style="display:inline;">
                                            <input type="hidden" name="idproducto" value="<?php echo $producto['idproducto']; ?>">
                                            <button type="submit" class="btn success">
                                                <i class="fi fi-ss-social-network"></i>
                                                <span>Seleccionar</span>
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


    <!--BOTON PARA RETIRAR LA IMAGEN-->
    <script>
    function previewImage(event, index) {
        const fileInput = document.getElementById(`fileUpload${index}`);
        const previewImage = document.getElementById(`preview${index}`);
        const removeButton = document.getElementById(`removeButton${index}`);
        
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';  // Muestra la imagen de vista previa
                previewImage.style.opacity = '1';  // Asegura que la imagen sea completamente visible
                removeButton.style.display = 'block';  // Muestra el botón de eliminar
            };
            reader.readAsDataURL(file);
        }
    }
    //BOTON DE QUITAR LA IMAGEN SELECCIONADAS
    function removeImage(index) {
        const previewImage = document.getElementById(`preview${index}`);
        const removeButton = document.getElementById(`removeButton${index}`);
        
        previewImage.style.display = 'none';
        removeButton.style.display = 'none';
        
        const fileInput = document.getElementById(`fileUpload${index}`);
        fileInput.value = ''; 
    }
    </script>
    <script>
        //BUSCADOR DE PRODUCTOS
        function searchProduct() {
        var searchTerm = document.getElementById("searchProduct").value;
        console.log(searchTerm); //VERIFICAR LO QUE SE ENVIA
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "buscador/buscar_producto.php?search=" + encodeURIComponent(searchTerm), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById("product-results").innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }
    </script>

