<?php
include_once "cabecera.php";
include_once "../../conexion.php"; // Conexión a la base de datos

$filasPorPagina = 7;

$paginaActual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

$offset = ($paginaActual - 1) * $filasPorPagina;

// Obtener el estado de los pedidos de la URL (por defecto, mostrar todos)
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
$cantidad = isset($_GET['cantidad']) ? $_GET['cantidad'] : null;
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;

$sql = "SELECT 
            p.idpedido, 
            s.fecha AS fecha_pedido, 
            v.fecha_venta AS fecha_venta, 
            c.ci_cliente, 
            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.apellido2_cliente) AS cliente,
            GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos,
            SUM(IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio)) AS precio_total,
            COUNT(pr.idproducto) AS cantidad_productos, 
            s.estado,
            CONCAT(pe.nombre, ' ', pe.apellido1, ' ', pe.apellido2) AS responsable
        FROM pedido p
        JOIN solicitud s ON p.solicitud_idsolicitud = s.idsolicitud
        JOIN cliente c ON s.cliente_idcliente = c.idcliente
        JOIN producto_solicitud ps ON s.idsolicitud = ps.solicitud_idsolicitud
        JOIN producto pr ON ps.producto_idproducto = pr.idproducto
        JOIN usuario u ON p.usuario_idusuario = u.idusuario
        LEFT JOIN venta v ON v.pedido_venta_idpedido_venta = (
            SELECT idpedido_venta 
            FROM pedido_venta 
            WHERE pedido_idpedido = p.idpedido 
            LIMIT 1
        )
        JOIN persona pe ON u.persona_idpersona = pe.idpersona";


// Inicializar un arreglo para las condiciones
$conditions = [];

// Filtro por estado si está presente
if ($estado) {
    $conditions[] = "s.estado = '" . $conn->real_escape_string($estado) . "'";
}

// Filtro por fecha de inicio
if (!empty($fecha_inicio)) {
    $conditions[] = "s.fecha >= '" . $conn->real_escape_string($fecha_inicio) . "'";
}

// Filtro por fecha de fin
if (!empty($fecha_fin)) {
    // Agregar un día a la fecha de fin para incluir el día completo
    $fecha_fin = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
    $conditions[] = "s.fecha < '" . $conn->real_escape_string($fecha_fin) . "'";
}

// Aplicar las condiciones de filtros
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

// Agrupar por el ID del pedido
$sql .= " GROUP BY p.idpedido";

// Aplicar filtros adicionales
if ($filtro === 'mayor_cantidad') {
    $sql .= " ORDER BY cantidad_productos DESC";
} elseif ($filtro === 'menor_cantidad') {
    $sql .= " ORDER BY cantidad_productos ASC";
} elseif ($filtro === 'mayor_precio') {
    $sql .= " ORDER BY precio_total DESC";
} elseif ($filtro === 'menor_precio') {
    $sql .= " ORDER BY precio_total ASC";
} else {
    // Por defecto, ordenar por fecha
    $sql .= " ORDER BY s.fecha DESC";
}

// Limitar los resultados por paginación
$sql .= " LIMIT $offset, $filasPorPagina";

$result = $conn->query($sql);

// Obtener el total de pedidos para la paginación
$sqlTotal = "SELECT COUNT(*) AS total_pedidos FROM pedido";
$resultTotal = $conn->query($sqlTotal);
$totalPedidos = $resultTotal->fetch_assoc()['total_pedidos'];

// Calcular el número total de páginas
$totalPaginas = ceil($totalPedidos / $filasPorPagina);

?>

<div class="full-width panel mdl-shadow--2dp">
    <div class="full-width panel-tittle bg-primary text-center tittles">
        LISTA DE PEDIDOS
    </div>

    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <div class="table-responsive">

                <!-- Botones como cartas -->
                <div class="menu-container">
                    <div class="card card-menos-vendidos" onclick="location.href='pedidos.php'">
                        <h3>Todos los Pedidos</h3>
                        <i class="fi fi-sr-globe"></i>
                    </div>
                    <div class="card card-completados <?php echo ($estado == 'completado') ? 'active' : ''; ?>" onclick="location.href='pedidos.php?estado=completado'">
                        <h3>Pedidos Completados</h3>
                        <i class="fi fi-ss-shopping-cart-check"></i>
                    </div>
                    <div class="card card-pendientes <?php echo ($estado == 'pendiente') ? 'active' : ''; ?>" onclick="location.href='pedidos.php?estado=pendiente'">
                        <h3>Pedidos Pendientes</h3>
                        <i class="fi fi-ss-order-history"></i>
                    </div>
                    <div class="card card-mayor" onclick="location.href='pedidos.php?filtro=mayor_cantidad'">
                        <h3>Pedido Con Mayor Cantidad</h3>
                        
                    </div>
                    <div class="card card-menor" onclick="location.href='pedidos.php?filtro=menor_cantidad'">
                        <h3>Pedido Con Menor Cantidad</h3>
                    </div>
                    <div class="card card-mayorprecio" onclick="location.href='pedidos.php?filtro=mayor_precio'">
                        <h3>Pedidos Con Mayor Precio</h3>
                    </div>
                    <div class="card card-menorprecio" onclick="location.href='pedidos.php?filtro=menor_precio'">
                        <h3>Pedidos Con Menor Precio</h3>
                    </div>
                </div>

                <!-- Buscador -->
                <div class="search-container text-center">
                    <form method="GET" action="">
                        <input type="text" name="busqueda" class="search-input" placeholder="Buscador..." value="<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" />
                    </form>
                </div>

                <div class="container row">
                    <div class="filter-container col-12">
                        <form method="GET" action="pedidos.php" class="filter-form">
                            <div class="date-filter-container text-left">
                                <label for="fecha_inicio">Desde:</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="date-input" value="<?php echo isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : ''; ?>" />
                                <label for="fecha_fin">Hasta:</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" class="date-input" value="<?php echo isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : ''; ?>" />
                                <button type="submit" class="btn-filter">Filtrar</button>
                            </div>
                        </form>
                    </div>
                    <div class="btn-container col-12">
                        <form method="get" action="../generarPDF/todos_pedidos_pdf.php" target="_blank">
                            <input type="hidden" name="estado" value="<?php echo isset($_GET['estado']) ? $_GET['estado'] : ''; ?>">

                            <input type="hidden" name="cantidad" value="<?php echo isset($_GET['cantidad']) ? $_GET['cantidad'] : ''; ?>">
                            <input type="hidden" name="filtro" value="<?php echo isset($_GET['filtro']) ? $_GET['filtro'] : ''; ?>">
                            <button type="submit" class="btn-exportar">Descargar Datos PDF</button>
                        </form>
                    </div>
                </div>
                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp centered-table">
                    <thead>
                        <tr>
                            <th>Fecha de Pedido</th>
                            <th>Fecha de Venta</th>
                            <th>Cliente</th>
                            <th>C.I.</th>
                            <th>Productos</th>
                            <th>Cantidad</th>
                            <th>Precio Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['fecha_pedido'] . "</td>";
                                echo "<td>" . $row['fecha_venta'] . "</td>";
                                echo "<td>" . $row['cliente'] . "</td>";
                                echo "<td>" . $row['ci_cliente'] . "</td>";
                                echo "<td>" . obtenerProductos($row['idpedido'], $conn) . "</td>";
                                echo "<td>" . $row['cantidad_productos'] . "</td>";
                                echo "<td>" . $row['precio_total'] . "</td>";
                                echo "<td class='" . ($row['estado'] == 'pendiente' ? 'estado-pendiente' : 'estado-completado') . "'>" . $row['estado'] . "</td>";
                                echo "<td>
                                <div style='display: flex; gap: 5px;'>";

                                // Aquí comienza el bloque PHP para la lógica condicional
                                if ($row['estado'] != 'completado') {
                                    echo '<form method="POST" action="pedidos/atender_pedido.php">
                                            <input type="hidden" name="idpedido" value="' . $row['idpedido'] . '">
                                            <button type="submit" name="atender_pedido" class="btn-accion btn-editar">Atender</button>
                                        </form>';
                                } else {
                                    // Si está completado, se muestra un botón deshabilitado
                                    echo '<button class="btn-accion btn-editar" disabled>Atender</button>';
                                }

                                // Lógica para el botón de anulación
                                if ($row['estado'] != 'completado') {
                                    echo '<form action="pedidos/cancelar_pedido.php" method="POST" style="display:inline;" onsubmit="return confirmCancel(event, this);">
                                            <input type="hidden" name="idpedido" value="' . $row['idpedido'] . '">
                                            <input type="hidden" name="cancelar_pedido" value="1">
                                            <button type="submit" class="btn-accion btn-eliminar">Anular</button>
                                        </form>';
                                } else {
                                    // Si está completado, se muestra un botón deshabilitado
                                    echo '<button class="btn-accion btn-eliminar" disabled>Anular</button>';
                                }

                                echo '<a href="../generarPDF/pedidos_pdf.php?idpedido=' . $row['idpedido'] . '" class="btn-accion btn-detalles">Detalles</a>
                                    </div>
                                    </td>';

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No hay pedidos registrados.</td></tr>";
                        }

                        function obtenerProductos($idpedido, $conn) {
                            $sql = "SELECT pr.nombre 
                                    FROM producto_solicitud ps
                                    JOIN producto pr ON ps.producto_idproducto = pr.idproducto
                                    WHERE ps.solicitud_idsolicitud = (
                                        SELECT solicitud_idsolicitud FROM pedido WHERE idpedido = ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $idpedido);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            $productos = [];
                            while ($row = $result->fetch_assoc()) {
                                $productos[] = $row['nombre'];
                            }
                            // Solo mostrar el primer producto y puntos suspensivos si hay más de uno
                            if (count($productos) > 1) {
                                return $productos[0] . '...';
                            } else if (count($productos) === 1) {
                                return $productos[0];
                            } else {
                                return 'Sin productos'; // En caso de que no haya productos
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php 
                        // Obtener parámetros actuales
                        $queryParams = $_GET;
                        
                        // Si hay un estado, mantenerlo en la paginación
                        if ($paginaActual > 1): 
                            $queryParams['pagina'] = $paginaActual - 1; 
                        ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPaginas; $i++): 
                            $queryParams['pagina'] = $i;
                        ?>
                            <li class="page-item <?php if ($i == $paginaActual) echo 'active'; ?>">
                                <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($paginaActual < $totalPaginas): 
                            $queryParams['pagina'] = $paginaActual + 1;
                        ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmCancel(event, form) {
    event.preventDefault(); // Evita el envío inmediato del formulario

    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esta acción!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // Enviar el formulario si el usuario confirma
        }
    });
}

</script>
<script>
$(document).ready(function() {
    // Cuando el usuario escribe en el campo de búsqueda
    $('.search-input').on('keyup', function() {
        var busqueda = $(this).val(); // Obtener el valor del input

        // Enviar la solicitud AJAX
        $.ajax({
            url: 'buscador/buscar_pedidos.php', // Archivo PHP que procesa la búsqueda
            method: 'POST',
            data: { busqueda: busqueda },
            success: function(response) {
                // Actualizar el contenido de la tabla con los resultados
                $('tbody').html(response);
            }
        });
    });
});
</script>


<?php
include_once "pie.php";
//include_once "validaciones/val_pedidos.php";
?>


<!-- Estilos CSS -->
<style>
    
    .btn-container {
        padding: 5px;
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .btn-descargar {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.3s, transform 0.3s;
    }

    .btn-descargar:hover {
        background-color: #218838;
        transform: scale(1.05);
    }

    .btn-accion {
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        margin-right: 5px;
        font-size: 0.75rem;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .btn-editar {
        background-color: #ffc107;
    }

    .btn-eliminar {
        background-color: #dc3545;
    }

    .btn-detalles {
        background-color: #17a2b8;
    }

    .btn-accion:hover {
        opacity: 0.8;
    }

    .centered-table th,
    .centered-table td {
        text-align: center;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .mdl-data-table {
        width: 100%;
        margin: auto;
    }

    .mdl-data-table th,
    .mdl-data-table td {
        padding: 10px 14px;
    }

    .mdl-data-table th {
        background-color: #f2f2f2;
    }

    .mdl-data-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .mdl-data-table tbody tr:hover {
        background-color: #f1f1f1;
    }
</style>

<style>
/* Contenedor de la paginación */
.pagination {
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 20px 0;
    list-style: none;
    gap: 10px; /* Espacio entre botones */
}

/* Estilo general de los botones de paginación */
.page-item {
    display: inline-block;
}

.page-link {
    color: #007bff; /* Color del texto del botón */
    background-color: white; /* Fondo del botón */
    border: 1px solid #007bff; /* Borde del botón */
    padding: 8px 16px; /* Espaciado dentro del botón */
    border-radius: 5px; /* Bordes redondeados */
    text-decoration: none; /* Quitar subrayado */
    transition: background-color 0.3s, color 0.3s, transform 0.3s; /* Efectos de transición */
}

/* Efecto hover para los botones */
.page-link:hover {
    background-color: #007bff;
    color: white;
    transform: scale(1.05); /* Efecto de agrandamiento */
}

/* Estilos para el botón activo */
.page-item.active .page-link {
    background-color: #0056b3; /* Fondo del botón activo */
    color: white; /* Color del texto del botón activo */
    border-color: #0056b3; /* Borde del botón activo */
    font-weight: bold; /* Negrita en el botón activo */
    transform: scale(1.1); /* Efecto de crecimiento en el botón activo */
}

/* Quitar el focus outline al hacer clic */
.page-link:focus {
    outline: none;
}
</style>
<!--ESTILO DE CAMBIO DE COLOR DE ESTADO-->
<style>
    .estado-pendiente {
    color: red; /* Color para estado pendiente */
}
.estado-completado {
    color: #0fbb28; /* Color para estado completado */
}

</style>
<style>
    /* Estilos para el buscador */
.search-container {
    margin: 20px 0;
}

.search-input {
    width: 250px;
    padding: 10px;
    margin: 0 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-buscar {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-buscar:hover {
    background-color: #0056b3;
}

/* Estilos para el filtro de fecha */
.date-filter-container {
    margin: 20px 0;
    text-align: left; /* Alinear el contenido a la derecha */
}

.date-input {
    padding: 10px;
    margin: 0 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.btn-filter {
    padding: 10px 15px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-filter:hover {
    background-color: #218838;
}

</style>
<style>
/* Estilos generales */
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 20px;
}

.filter-container, .btn-container {
    margin: 7px 0;
}

.filter-form {
    display: flex;
    flex-direction: column;
}

.date-row {
    display: flex;
    justify-content: space-between;
    gap: 7px;
    align-items: center;
}

.date-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    flex-grow: 1;
}

.animated-label {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.input-fecha {
    padding: 5px;
    font-size: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s ease;
    width: 80%;
}

.input-fecha:hover, .input-fecha:focus {
    border-color: #007bff;
}

/* Botones */
.btn-filtrar, .btn-exportar {
    padding: 12px 15px;
    font-size: 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 20px;
}

.btn-filtrar {
    background-color: #28a745;
    color: white;
}

.btn-filtrar:hover {
    background-color: #218838;
}

.btn-exportar {
    background-color: #17a2b8;
    color: white;
}

.btn-exportar:hover {
    background-color: #138496;
}

/* Estilo responsive */
@media (max-width: 668px) {
    .date-row {
        flex-direction: column;
    }

    .container {
        flex-direction: column;
        align-items: center;
    }

    .filter-container, .btn-container {
        width: 100%;
    }
}
</style>
<style>
/* Estilos para el contenedor de tarjetas */
.menu-container {
    display: flex;
    justify-content: 10px; /* Espacio entre las cartas */
    margin: 10px 0; /* Espaciado vertical */
    flex-wrap: wrap; /* Permitir que las cartas se envuelvan en varias líneas */
}

/* Estilos generales para cada carta */
.card {
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    padding: 15px 20px;
    width: 210px; /* Ajusta el ancho */
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
    transition: transform 0.2s; /* Transición suave */
    margin: 7px; /* Espaciado entre cartas */
}

/* Estilo para el texto */
.card h3 {
    margin: 0;
    font-size: 16px; /* Reducir el tamaño de la letra */
    text-align: left;
}

/* Estilo para los iconos */
.card i {
    font-size: 30px; /* Tamaño del ícono */
    margin-left: 5px; /* Espacio entre el texto y el ícono */
}

/* Efecto hover */
.card:hover {
    transform: scale(1.05); /* Aumenta ligeramente el tamaño al pasar el mouse */
}

/* Colores individuales */
.card-completados {
    background-color: #4CAF50; /* Color verde para completados */
    color: white;
}

.card-pendientes {
    background-color: #FF9800; /* Color naranja para pendientes */
    color: white;
}

.card-menos-vendidos {
    background-color: #F44336; /* Color rojo para menos vendidos */
    color: white;
}
.card-mayor {
    background-color: #877677; /* Color rojo para menos vendidos */
    color: white;
}
.card-menor {
    background-color: #12525d; /* Color rojo para menos vendidos */
    color: white;
}
.card-mayorprecio {
    background-color: #111f34; /* Color rojo para menos vendidos */
    color: white;
}
.card-menorprecio {
    background-color: #9d4173; /* Color rojo para menos vendidos */
    color: white;
}

</style>