<?php
include_once "cabecera.php";
include_once "../../conexion.php"; // Conexión a la base de datos

// Número de filas por página
$filasPorPagina = 7;

// Obtener la página actual desde la URL, por defecto es 1 si no se ha especificado
if (isset($_GET['pagina']) && is_numeric($_GET['pagina'])) {
    $paginaActual = (int) $_GET['pagina'];
} else {
    $paginaActual = 1;
}

// Calcular el desplazamiento (offset) para la consulta SQL
$offset = ($paginaActual - 1) * $filasPorPagina;

// Consulta para obtener el total de pedidos (para la paginación)
$sqlTotal = "SELECT COUNT(*) AS total_pedidos FROM pedido";
$resultTotal = $conn->query($sqlTotal);
$totalPedidos = $resultTotal->fetch_assoc()['total_pedidos'];

// Calcular el número total de páginas
$totalPaginas = ceil($totalPedidos / $filasPorPagina);

// Consulta para obtener los pedidos con límite y desplazamiento
$sql = "SELECT p.idpedido, s.fecha, CONCAT(c.nombre_cliente, ' ', c.apellido_cliente, ' ', c.apellido2_cliente) AS cliente, 
        GROUP_CONCAT(pr.nombre SEPARATOR ', ') AS productos, 
        SUM(IF(pr.precioConDescuento IS NOT NULL, pr.precioConDescuento, pr.precio)) AS precio_total, 
        s.estado,
        CONCAT(pe.nombre, ' ', pe.apellido1, ' ', pe.apellido2) AS responsable
 FROM pedido p
 JOIN solicitud s ON p.solicitud_idsolicitud = s.idsolicitud
 JOIN cliente c ON s.cliente_idcliente = c.idcliente
 JOIN producto_solicitud ps ON s.idsolicitud = ps.solicitud_idsolicitud
 JOIN producto pr ON ps.producto_idproducto = pr.idproducto
 JOIN usuario u ON p.usuario_idusuario = u.idusuario
 JOIN persona pe ON u.persona_idpersona = pe.idpersona
 GROUP BY p.idpedido, s.fecha, cliente, s.estado, responsable
 ORDER BY s.fecha DESC
 LIMIT $filasPorPagina OFFSET $offset"; // Limitamos los resultados a 7 y aplicamos el desplazamiento

$result = $conn->query($sql);
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
                    <div class="card card-completados" onclick="location.href='pendientes.php'">
                        <h3>Pedidos Completados</h3>
                        <i class="fi fi-ss-shopping-cart-check"></i>
                    </div>
                    <div class="card card-pendientes" onclick="location.href='mas_vendidos.php'">
                        <h3>Pedidos Pendientes</h3>
                        <i class="fi fi-ss-order-history"></i>
                    </div>
                    <div class="card card-menos-vendidos" onclick="location.href='menos_vendidos.php'">
                        <h3>Menos Vendidos</h3>
                        <i class="fa fa-chart-bar"></i>
                    </div>
                </div>

                <!-- Buscador -->
                <div class="search-container text-center">
                    <form method="GET" action="">
                        <input type="text" name="busqueda" class="search-input" placeholder="Buscardor..." />
                    </form>
                </div>

                <!-- Filtro de fecha -->
                <div class="date-filter-container text-left">
                    <form method="GET" action="">
                        <label for="fecha_inicio">Desde:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="date-input" />
                        <label for="fecha_fin">Hasta:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="date-input" />
                        <button type="submit" class="btn-filter">Filtrar</button>
                    </form>
                </div>
               

                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp centered-table">
                    <thead>
                        <tr>
                            <th>Fecha de Pedido</th>
                            <th>Fecha de Venta</th>
                            <th>Responsable</th>
                            <th>Cliente</th>
                            <th>Productos</th>
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
                                echo "<td>" . $row['fecha'] . "</td>";
                                echo "<td>" . $row['fecha'] . "</td>";
                                echo "<td>" . $row['responsable'] . "</td>";
                                echo "<td>" . $row['cliente'] . "</td>";
                                echo "<td>" . obtenerProductos($row['idpedido'], $conn) . "</td>";
                                echo "<td>" . $row['precio_total'] . "</td>";
                                echo "<td class='" . ($row['estado'] == 'pendiente' ? 'estado-pendiente' : 'estado-completado') . "'>" . $row['estado'] . "</td>";
                                echo "<td>
                                        <div style='display: flex; gap: 5px;'>
                                            <form method='POST' action='pedidos/atender_pedido.php'>
                                                <input type='hidden' name='idpedido' value='" . $row['idpedido'] . "'>
                                                <button type='submit' name='atender_pedido' class='btn-accion btn-editar'>Atender</button>
                                            </form>
                                            <form action='pedidos/cancelar_pedido.php' method='POST' style='display:inline;' onsubmit='return confirmCancel(event, this);'>
                                                <input type='hidden' name='idpedido' value='" . $row['idpedido'] . "'>
                                                <button type='submit' name='cancelar_pedido' class='btn-accion btn-eliminar'>Cancelar</button>
                                            </form>
                                            <a href='#' class='btn-accion btn-detalles'>Detalles</a>
                                        </div>
                                    </td>";
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

                <!-- Paginación -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($paginaActual > 1): ?>
                            <li class="page-item"><a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?php if ($i == $paginaActual) echo 'active'; ?>"><a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php endfor; ?>
                        
                        <?php if ($paginaActual < $totalPaginas): ?>
                            <li class="page-item"><a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>

<script>
// Confirmación con SweetAlert antes de cancelar el pedido
function confirmCancel(event, form) {
    event.preventDefault();
    Swal.fire({
        title: '¿Estás seguro de que deseas cancelar el pedido?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cancelar pedido',
        cancelButtonText: 'No, mantener pedido'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>

<?php
include_once "pie.php"; // Incluir el pie de página
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


</style>