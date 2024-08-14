<?php
include_once "cabecera.php";
include_once "../modelo_Admin/conexion/conexionBase.php";

// Crear una instancia de la conexión
$db = new conexionBase();
$db->CreateConnection();

// Función para obtener el conteo de registros
function obtenerConteo($sql) {
    global $db;
    $result = $db->GetConnection()->query($sql);
    if ($result === false) {
        return 0; // En caso de error, retornar 0
    }
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Consultas para obtener conteos
$totalPersonal = obtenerConteo("SELECT COUNT(*) AS total FROM usuario");
$totalClientes = obtenerConteo("SELECT COUNT(*) AS total FROM cliente");
$totalCategorias = obtenerConteo("SELECT COUNT(*) AS total FROM categoria");
$totalProductos = obtenerConteo("SELECT COUNT(*) AS total FROM producto");
$totalVentas = obtenerConteo("SELECT COUNT(*) AS total FROM venta");

// Cerrar la conexión
$db->CloseConnection();
?>

<section class="full-width text-center" style="padding: 40px 0;">
    <h3 class="text-center tittles">T I T U L O S</h3>
    <a >
        <article class="full-width tile">
            <div class="tile-text">
                <span class="text-condensedLight">
                    <?php echo $totalPersonal; ?><br>
                    <small>PERSONAL</small>
                </span>
            </div>
            <i class="zmdi zmdi-account tile-icon"></i>
        </article>
    </a>
    <article class="full-width tile">
        <div class="tile-text">
            <span class="text-condensedLight">
                <?php echo $totalClientes; ?><br>
                <small>Clientes</small>
            </span>
        </div>
        <i class="zmdi zmdi-accounts tile-icon"></i>
    </article>
    <article class="full-width tile">
        <div class="tile-text">
            <span class="text-condensedLight">
                <?php echo $totalCategorias; ?><br>
                <small>Categorias</small>
            </span>
        </div>
        <i class="zmdi zmdi-label tile-icon"></i>
    </article>
    <article class="full-width tile">
        <div class="tile-text">
            <span class="text-condensedLight">
                <?php echo $totalProductos; ?><br>
                <small>Productos</small>
            </span>
        </div>
        <i class="zmdi zmdi-washing-machine tile-icon"></i>
    </article>
    <article class="full-width tile">
        <div class="tile-text">
            <span class="text-condensedLight">
                <?php echo $totalVentas; ?><br>
                <small>Ventas</small>
            </span>
        </div>
        <i class="zmdi zmdi-shopping-cart tile-icon"></i>
    </article>
</section>

<?php
include_once "pie.php";
?>
