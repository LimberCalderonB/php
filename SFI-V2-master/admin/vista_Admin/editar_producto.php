<?php
include_once "cabecera.php";
include_once "../../conexion.php";

// Obtener el ID del producto a editar
$idproducto = $_GET['idproducto'];

// Consulta para obtener los detalles del producto junto con la categoría
$query_producto = "
    SELECT p.*, a.categoria_idcategoria
    FROM producto p
    LEFT JOIN almacen a ON p.idproducto = a.producto_idproducto
    WHERE p.idproducto = $idproducto
";
$result_producto = mysqli_query($conn, $query_producto);
if (!$result_producto) {
    die('Error en la consulta del producto: ' . mysqli_error($conn));
}
$producto = mysqli_fetch_assoc($result_producto);

// Consulta para obtener las categorías
$query_categorias = "SELECT idcategoria, nombre FROM categoria";
$result_categorias = mysqli_query($conn, $query_categorias);
if (!$result_categorias) {
    die('Error en la consulta de categorías: ' . mysqli_error($conn));
}

// Verifica si 'categoria_idcategoria' está definido en el array $producto
$categoria_idcategoria = isset($producto['categoria_idcategoria']) ? $producto['categoria_idcategoria'] : '';

?>

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <div class="full-width panel mdl-shadow--2dp">
                <div class="full-width panel-tittle bg-primary text-center tittles">
                    Editar Producto
                </div>
                <div class="full-width panel-content">
                    <form action="../controlador_admin/ct_producto.php" method="POST" id="guardado" enctype="multipart/form-data">
                        <input type="hidden" name="idproducto" value="<?php echo htmlspecialchars($producto['idproducto']); ?>">
                        <div class="mdl-grid">
                            <div class="mdl-cell mdl-cell--12-col">
                                <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACION DEL PRODUCTO</legend><br>
                            </div>
                            <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="text" pattern="-?[A-Za-z0-9áéíóúÁÉÍÓÚ ]*(\.[0-9]+)?" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                    <label class="mdl-textfield__label" for="nombre">Nombre</label>
                                    <span class="mdl-textfield__error" style="color:red;">Nombre Invalido</span>
                                </div>
                            </div>
                            <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="text" pattern="-?[0-9.]*(\.[0-9]+)?" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>">
                                    <label class="mdl-textfield__label" for="precio">Precio</label>
                                    <span class="mdl-textfield__error" style="color:red;">Precio Invalido</span>
                                </div>
                            </div>
                            <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="number" pattern="-?[0-9]*(\.[0-9]+)?" id="descuento" name="descuento" value="<?php echo htmlspecialchars($producto['descuento']); ?>">
                                    <label class="mdl-textfield__label" for="descuento">% Descuento</label>
                                    <span class="mdl-textfield__error" style="color:red;">Descuento Invalido</span>
                                </div>
                            </div>
                            <div class="mdl-cell mdl-cell--2-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="number" pattern="-?[0-9]*(\.[0-9]+)?" id="talla" name="talla" value="<?php echo htmlspecialchars($producto['talla']); ?>">
                                    <label class="mdl-textfield__label" for="talla">Talla</label>
                                    <span class="mdl-textfield__error" style="color:red;">Talla Invalida</span>
                                </div>
                            </div>
                            <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="text" pattern="-?[A-Za-z0-9áéíóúÁÉÍÓÚ-ñÑ ]*(\.[0-9]+)?" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($producto['descripcion']); ?>">
                                    <label class="mdl-textfield__label" for="descripcion">Descripcion de Producto</label>
                                    <span class="mdl-textfield__error" style="color:red;">Falta la Descripción</span>
                                </div>
                            </div>
                            
                            <div class="mdl-cell mdl-cell--12-col">
                                <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; CATEGORIA</legend><br>
                            </div>
                            <div class="mdl-cell mdl-cell--12-col">
                                <div class="mdl-textfield mdl-js-textfield" id="idCategoria-field">
                                <?php
                                // Verifica si 'categoria_idcategoria' está definido en el array $producto
                                $categoria_idcategoria = isset($producto['categoria_idcategoria']) ? $producto['categoria_idcategoria'] : '';
                                ?>
                                <select class="mdl-textfield__input" name="categoria_idcategoria" id="categoria_idcategoria">
                                    <option value="" disabled="">Selecciona Categoria</option>
                                    <?php while ($fila = mysqli_fetch_assoc($result_categorias)): ?>
                                        <option value="<?php echo htmlspecialchars($fila['idcategoria']); ?>"
                                                <?php echo $fila['idcategoria'] == $categoria_idcategoria ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($fila['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                    <span class="mdl-textfield__error" id="idCategoria-error">Debe seleccionar una categoría</span>
                                </div>
                            </div>
                            <div class="mdl-cell mdl-cell--12-col">
                                <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; OTROS DATOS</legend><br>
                            </div>

                            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield file-upload-container">
                                    <label>Imagen 1:</label><br>
                                    <img id="imgPreview1" class="img-thumbnail" src="<?php echo $producto['img1'] ? 'img/vestidos/' . htmlspecialchars($producto['img1']) : ''; ?>" alt="Imagen 1" />
                                    <div class="file-upload-controls">
                                        <button type="button" class="btn-remove" onclick="removeImage(1)">Quitar</button>
                                        <input type="file" id="fileUpload1" accept="image/*" name="img1" onchange="updatePreview(this, 'imgPreview1')" />
                                        <button type="button" class="file-upload-button" onclick="document.getElementById('fileUpload1').click()">Seleccionar</button>
                                        <input type="hidden" id="remove_img1" name="remove_img1" value="0">
                                        <input type="hidden" name="original_img1" value="<?php echo htmlspecialchars($producto['img1']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield file-upload-container">
                                    <label>Imagen 2:</label><br>
                                    <img id="imgPreview2" class="img-thumbnail" src="<?php echo $producto['img2'] ? 'img/vestidos/' . htmlspecialchars($producto['img2']) : ''; ?>" alt="Imagen 2" />
                                    <div class="file-upload-controls">
                                        <button type="button" class="btn-remove" onclick="removeImage(2)">Quitar</button>
                                        <input type="file" id="fileUpload2" accept="image/*" name="img2" onchange="updatePreview(this, 'imgPreview2')" />
                                        <button type="button" class="file-upload-button" onclick="document.getElementById('fileUpload2').click()">Seleccionar</button>
                                        <input type="hidden" id="remove_img2" name="remove_img2" value="0">
                                        <input type="hidden" name="original_img2" value="<?php echo htmlspecialchars($producto['img2']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">
                                <div class="mdl-textfield mdl-js-textfield file-upload-container">
                                    <label>Imagen 3:</label><br>
                                    <img id="imgPreview3" class="img-thumbnail" src="<?php echo $producto['img3'] ? 'img/vestidos/' . htmlspecialchars($producto['img3']) : ''; ?>" alt="Imagen 3" />
                                    <div class="file-upload-controls">
                                        <button type="button" class="btn-remove" onclick="removeImage(3)">Quitar</button>
                                        <input type="file" id="fileUpload3" accept="image/*" name="img3" onchange="updatePreview(this, 'imgPreview3')" />
                                        <button type="button" class="file-upload-button" onclick="document.getElementById('fileUpload3').click()">Seleccionar</button>
                                        <input type="hidden" id="remove_img3" name="remove_img3" value="0">
                                        <input type="hidden" name="original_img3" value="<?php echo htmlspecialchars($producto['img3']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mdl-cell mdl-cell--12-col">
                                <p class="text-center">
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-primary">Actualizar Producto</button>
                                    <a href="productos.php" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored bg-danger" onclick="clearSessionData()">
                            Cancelar
                        </a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php 
include_once "pie.php"; 
include_once "validaciones/val_producto.php";
?>

