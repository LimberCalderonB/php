<?php
include_once "cabecera.php";

// ALERTA DE REGISTRO
if (isset($_SESSION['registro_exitoso_categoria']) && $_SESSION['registro_exitoso_categoria'] == true) {
    echo "<script>
    $(document).ready(function() {
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Categoría agregada exitosamente',
            showConfirmButton: false,
            timer: 1500
        });
    });
    </script>";
    unset($_SESSION['registro_exitoso_categoria']);
}

// Variable para mantener la pestaña activa
$activeTab = 'tabListCategory';
if (isset($_SESSION['error_categoria'])) {
    $activeTab = 'tabNewCategory';
}
?>

<br>
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
    <div class="mdl-tabs__tab-bar">
        <a href="#tabListCategory" class="mdl-tabs__tab <?php echo $activeTab == 'tabListCategory' ? 'is-active' : ''; ?>">LISTA DE CATEGORIAS</a>
        <a href="#tabNewCategory" class="mdl-tabs__tab <?php echo $activeTab == 'tabNewCategory' ? 'is-active' : ''; ?>">AGREGAR NUEVA CATEGORIA</a>
    </div>

    <div class="mdl-tabs__panel <?php echo $activeTab == 'tabListCategory' ? 'is-active' : ''; ?>" id="tabListCategory">
        <!-- Lista de Categorías -->
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--4-col-phone mdl-cell--8-col-tablet mdl-cell--8-col-desktop mdl-cell--2-offset-desktop">
                <div class="full-width panel mdl-shadow--2dp">
                    <div class="full-width panel-tittle bg-success text-center tittles">
                        LISTA DE CATEGORIAS
                    </div>
                    <div class="full-width panel-content">
                        <form action="#">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                                <label class="mdl-button mdl-js-button mdl-button--icon" for="searchCategories">
                                    <i class="zmdi zmdi-search"></i>
                                </label>
                                <div class="mdl-textfield__expandable-holder">
                                    <input class="mdl-textfield__input" type="text" id="searchCategories">
                                    <label class="mdl-textfield__label"></label>
                                </div>
                            </div>
                        </form>
                        <div class="mdl-list">
                            <?php
                            include_once "../../conexion.php";
                            if ($conn->connect_error) {
                                die("Error de conexión: " . $conn->connect_error);
                            }
                            $sql = "SELECT idcategoria, nombre FROM categoria";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $count = 1;
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <div class='mdl-list__item mdl-list__item--two-line'>
                                        <span class='mdl-list__item-primary-content'>
                                            <i class='zmdi zmdi-label mdl-list__item-avatar'></i>
                                            <span><?php echo $count . ". " . htmlspecialchars($row['nombre']); ?></span> 
                                            <span class='mdl-list__item-sub-title'>ID: <?php echo $row['idcategoria']; ?></span> 
                                        </span>
                                        <span class='mdl-list__item-secondary-action'>
                                            <!-- EDITAR -->
                                            <button class='mdl-button mdl-js-button mdl-button--icon' onclick='window.location.href="editar_categoria.php?idcategoria=<?php echo $row["idcategoria"]; ?>"'>
                                                <i class='zmdi zmdi-edit'></i>
                                            </button>
                                            <!-- ELIMINAR -->
                                            <button id='deleteBtn_<?php echo $row["idcategoria"]; ?>' class='mdl-button mdl-js-button mdl-button--icon' onclick='confirmDelete(<?php echo $row["idcategoria"]; ?>)'>
                                                <i class='zmdi zmdi-delete'></i>
                                            </button>
                                        </span>
                                    </div>
                                    <li class='full-width divider-menu-h'></li>
                                    <?php
                                    $count++;
                                }
                            } else {
                                echo "No se encontraron categorías.";
                            }
                            $conn->close();
                            ?>
                        </div>
                        <script>
                            // ALERTA DE ELIMINACION
                            function confirmDelete(idcategoria) {
                                Swal.fire({
                                    title: '¿Estás seguro?',
                                    text: "¡No podrás revertir esto!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Sí, eliminarlo!',
                                    cancelButtonText: 'Cancelar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            type: 'POST',
                                            url: '../crud/categoria/eliminar.php', 
                                            data: {idcategoria: idcategoria},
                                            success: function(response) {
                                                Swal.fire({
                                                    title: '¡Eliminado!',
                                                    text: 'La categoría ha sido eliminada.',
                                                    icon: 'success'
                                                }).then((result) => {
                                                    location.reload();
                                                });
                                            },
                                            error: function(xhr, status, error) {
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'Se produjo un error al intentar eliminar la categoría.',
                                                    icon: 'error'
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mdl-tabs__panel <?php echo $activeTab == 'tabNewCategory' ? 'is-active' : ''; ?>" id="tabNewCategory">
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col">
                <div class="full-width panel mdl-shadow--2dp">
                    <div class="full-width panel-tittle bg-primary text-center tittles">
                        Nueva Categoría
                    </div>
                    <div class="full-width panel-content">
                        <form action="../controlador_admin/ct_categoria.php" method="post" class="row g-3 needs-validation" novalidate>
                            <div class="mdl-grid">
                                <div class="mdl-cell mdl-cell--12-col">
                                    <legend class="text-condensedLight"><i class="zmdi zmdi-border-color"></i> &nbsp; INFORMACION</legend><br>
                                </div>
                                <div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
                                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php echo isset($_SESSION['error_categoria']) ? 'is-invalid' : ''; ?>">
                                        <input class="mdl-textfield__input" type="text" id="nombre" name="nombre" value="<?php echo isset($_SESSION['nombre_categoria']) ? $_SESSION['nombre_categoria'] : ''; ?>">
                                        <label class="mdl-textfield__label" for="nombre">Nombre de Categoría</label>
                                        <?php if(isset($_SESSION['error_categoria'])): ?>
                                            <span class="mdl-textfield__error" style="color:red;"><?php echo $_SESSION['mensaje_categoria']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center">
                                <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored bg-primary" id="agregar" type="submit">
                                    <i class="zmdi zmdi-plus"></i>
                                </button>
                                <div class="mdl-tooltip" for="agregar">Agregar Categoría</div>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
unset($_SESSION['error_categoria']); 
unset($_SESSION['mensaje_categoria']);
unset($_SESSION['nombre_categoria']);
include_once "pie.php";
?>
