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
$totalProductos = obtenerConteo("SELECT COUNT(*) AS total FROM almacen WHERE estado = 'disponible'");
$totalVentas = obtenerConteo("SELECT COUNT(*) AS total FROM venta");
$totalPedidos = obtenerConteo("SELECT COUNT(*) AS total FROM pedido");

// Cerrar la conexión
$db->CloseConnection();
?>

<section class="full-width text-center" style="padding: 40px 0;">
    <h3 class="text-center tittles" style="color: #5A5A5A; font-size: 2.5em; margin-bottom: 20px;">T I T U L O S</h3>
    <div class="card-container">
        <div class="card" style="background-color: #232037;" onclick="location.href='personal.php'">
            <h3>Personal</h3>
            <span class="text-condensedLight"><?php echo $totalPersonal; ?></span>
            
        </div>
        
        <div class="card" style="background-color: #594a75;" onclick="location.href='cliente.php'">
            <h3>Clientes</h3>
            <span class="text-condensedLight"><?php echo $totalClientes; ?></span>
            
        </div>
        
        <div class="card" style="background-color: #535692;" onclick="location.href='categoria.php'">
            <h3>Categorias</h3>
            <span class="text-condensedLight"><?php echo $totalCategorias; ?></span>
            
        </div>
        
        <div class="card" style="background-color: #512647;" onclick="location.href='stock.php'">
            <h3>Productos</h3>
            <span class="text-condensedLight"><?php echo $totalProductos; ?></span>
            
        </div>
        
        <div class="card" style="background-color: #1d2652;" onclick="location.href='ventas.php'">
            <h3>Ventas</h3>
            <span class="text-condensedLight"><?php echo $totalVentas; ?></span>
            
        </div>

        <div class="card" style="background-color: #727d91;" onclick="location.href='pedidos.php'">
            <h3>Pedidos</h3>
            <span class="text-condensedLight"><?php echo $totalPedidos; ?></span>
            
        </div>
    </div>
</section>

<style>
    .card-container {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px 0;
    }

    .card {
        width: 200px;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        color: white;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .card h3 {
        font-size: 1.5em;
        margin-bottom: 5px;
    }

    .card .text-condensedLight {
        font-size: 1.3em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .card-icon {
        font-size: 3em;
        margin-top: 10px;
    }
</style>

<?php
include_once "pie.php";
?>
