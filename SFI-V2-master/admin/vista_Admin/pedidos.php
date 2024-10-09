<?php
include_once "cabecera.php";
include_once "../../conexion.php"; // Asegúrate de incluir tu conexión a la base de datos
?>

<div class="full-width panel mdl-shadow--2dp">
    <div class="full-width panel-tittle bg-primary text-center tittles">
        LISTA DE PEDIDOS
    </div>

    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--12-col">
            <div class="table-responsive">
                <div class="btn-container">
                    <a href="crear_pedido.php" class="btn-descargar">Crear Pedido</a>
                </div>

                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp centered-table">
                    <thead>
                        <tr>
                            <th>Fecha de Pedido</th>
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
                        // Consulta para obtener los pedidos
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
                 GROUP BY p.idpedido, s.fecha, cliente, s.estado, responsable";

                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Muestra cada pedido en una fila de la tabla
                                echo "<tr>";
                                    echo "<td>" . $row['fecha'] . "</td>";
                                    echo "<td>" . $row['responsable'] . "</td>"; // Mostrando el responsable
                                    echo "<td>" . $row['cliente'] . "</td>";
                                    echo "<td>" . obtenerProductos($row['idpedido'], $conn) . "</td>"; // Función para obtener productos
                                    echo "<td>" . $row['precio_total'] . "</td>";
                                    echo "<td>" . $row['estado'] . "</td>";
                                    echo "<td>
                                    
                                        <form method='POST' action='pedidos/atender_pedido.php'>
        <input type='hidden' name='idpedido' value='" . $row['idpedido'] . "'>
        <button type='submit' name='atender_pedido' class='btn-accion btn-editar'>Atender</button>
    </form>
                                    
                                        <form action='pedidos/cancelar_pedido.php' method='POST' style='display:inline;' onsubmit='return confirmCancel(event, this);'>
                                            <input type='hidden' name='idpedido' value='" . $row['idpedido'] . "'>
                                            <button type='submit' name='cancelar_pedido' class='btn-accion btn-eliminar'>Cancelar</button>
                                        </form>
                                        <a href='#' class='btn-accion btn-detalles'>Detalles</a>
                                    </td>";
                                echo "</tr>";

                            }
                        } else {
                            echo "<tr><td colspan='6'>No hay pedidos registrados.</td></tr>";
                        }

                        // Función para obtener los productos asociados a un pedido
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
                            return implode(", ", $productos); // Lista de productos como una cadena
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script>
// Confirmación con SweetAlert antes de cancelar el pedido
function confirmCancel(event, form) {
    event.preventDefault(); // Prevenir el envío del formulario para manejarlo manualmente

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
            // Crear un input oculto para enviar el valor de 'cancelar_pedido'
            let cancelarInput = document.createElement('input');
            cancelarInput.type = 'hidden';
            cancelarInput.name = 'cancelar_pedido';
            cancelarInput.value = 'true';
            form.appendChild(cancelarInput);

            // Enviar el formulario manualmente con el campo 'cancelar_pedido'
            form.submit();
        }
    });
}
</script>


<?php
include_once "pie.php";
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
