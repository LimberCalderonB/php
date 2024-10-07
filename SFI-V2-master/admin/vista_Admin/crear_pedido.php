<?php
include_once "cabecera.php";
include_once "../../conexion.php";

// Consulta para obtener categorías que tienen productos disponibles
$query = "SELECT categoria.idcategoria, categoria.nombre 
          FROM categoria 
          JOIN almacen ON categoria.idcategoria = almacen.categoria_idcategoria
          JOIN producto ON almacen.producto_idproducto = producto.idproducto
          WHERE almacen.cantidad > 0 
          GROUP BY producto.nombre, producto.precio, producto.descuento, producto.talla, categoria.nombre"; 

$result = $conn->query($query);
$categorias = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// Consulta según la categoría seleccionada o todos los productos
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category == 'all') {
    $query = "SELECT 
                p.idproducto,
                p.nombre, 
                p.precio, 
                p.descuento, 
                p.talla, 
                c.nombre AS categoria, 
                SUM(a.cantidad) AS cantidad,
                p.precioConDescuento
              FROM almacen a
              JOIN producto p ON a.producto_idproducto = p.idproducto
              JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
              WHERE a.estado = 'disponible'
              GROUP BY p.nombre, p.precio, p.descuento, p.talla, c.nombre";

} else {
    $query = "SELECT 
                p.idproducto,
                p.nombre, 
                p.precio, 
                p.descuento, 
                p.talla, 
                c.nombre AS categoria, 
                SUM(a.cantidad) AS cantidad,
                p.precioConDescuento
              FROM almacen a
              JOIN producto p ON a.producto_idproducto = p.idproducto
              JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
              WHERE a.estado = 'disponible' AND c.idcategoria = ?
              GROUP BY p.nombre, p.precio, p.descuento, p.talla, c.nombre";
}

$stmt = $conn->prepare($query);

if ($category != 'all') {
    $stmt->bind_param("i", $category); // Vincula la categoría seleccionada
}
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="full-width panel mdl-shadow--2dp">
    <div class="full-width panel-tittle bg-primary text-center tittles">
        CREAR PEDIDO
    </div>

    <nav class="navbar">
        <div class="navbar-container">
            <ul class="navbar-menu">
                <li><a href="?category=all">Todo</a></li>
                <?php if (!empty($categorias)) : ?>
                    <?php foreach ($categorias as $categoria) : ?>
                        <li><a href="?category=<?= $categoria['idcategoria']; ?>"><?= $categoria['nombre']; ?></a></li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li>No hay categorías disponibles</li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="mdl-grid">
        <div class="mdl-cell mdl-cell--4-col" style="text-align: left;">
            <a href="cliente.php" class="mdl-button mdl-js-button mdl-button--raised btn-crear">
                Crear Cliente
            </a>
        </div>

        <div class="mdl-cell mdl-cell--4-col" style="text-align: center;">
            <input type="text" id="buscar" placeholder="Buscar cliente" class="mdl-textfield__input">
            <input type="hidden" id="idcliente" name="idcliente">
            <div id="resultados" style="display:none;"></div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="mdl-grid">
    <div class="mdl-cell mdl-cell--7-col">
            <h4>Productos Disponibles</h4>
            <div class="table-responsive">
    <button id="btn-agregar" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">Agregar Seleccionados</button>
    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table styled-table" id="tabla-productos">
        <thead>
            <tr>
                <th>Seleccionar</th>
                <th>Solicitud</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Talla</th>
                <th>Cantidad</th>
            </tr>
        </thead>


<tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $precioConDescuento = isset($row['precioConDescuento']) ? $row['precioConDescuento'] : $row['precio'] - ($row['precio'] * ($row['descuento'] / 100));
    ?>
    <tr>
        <td>
            <input type="checkbox" class="select-product" data-product-id="<?= $row['idproducto']; ?>" data-product-name="<?= $row['nombre']; ?>">
        </td>
        <td>
            <input type="number" class="input-solicitud" min="0" value="0" disabled max="<?php echo $row['cantidad']; ?>">
        </td>
        <td><?php echo $row['nombre']; ?></td>
        <td><?php echo $row['categoria']; ?></td>
        <td><?php echo $row['talla']; ?></td>
        <td><?php echo $row['cantidad']; ?></td>
    </tr>
    <?php
        }
    } else {
        echo "<tr><td colspan='6'>No hay productos disponibles</td></tr>";
    }
    ?>
</tbody>

    </table>
</div>
    </div>

<div class="mdl-cell mdl-cell--5-col">
    <h4>Productos Seleccionados</h4>
    <div class="table-responsive">
        <button id="btn-guardar" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">Guardar Pedido</button>
                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp full-width centered-table styled-table" id="tabla-seleccionados">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Talla</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $querySeleccionados = "SELECT p.nombre, c.nombre AS categoria, p.talla, SUM(a.cantidad) AS cantidad
                       FROM almacen a
                       JOIN producto p ON a.producto_idproducto = p.idproducto
                       JOIN categoria c ON a.categoria_idcategoria = c.idcategoria
                       WHERE a.estado = 'espera'
                       GROUP BY p.nombre, c.nombre, p.talla"; 
                        $resultSeleccionados = $conn->query($querySeleccionados);
                        if ($resultSeleccionados->num_rows > 0) {
                            while ($row = $resultSeleccionados->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['nombre']}</td>
                                        <td>{$row['categoria']}</td>
                                        <td>{$row['talla']}</td>
                                        <td>{$row['cantidad']}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No hay productos seleccionados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
                    <!--SCRIPT PARA GUARDAR-->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnGuardar = document.getElementById('btn-guardar');
    const checkboxes = document.querySelectorAll('.select-product');
    const inputSolicitudes = document.querySelectorAll('.input-solicitud');

    btnGuardar.addEventListener('click', function () {
        const productosSeleccionados = [];

        checkboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                const cantidadSolicitada = inputSolicitudes[index].value;
                productosSeleccionados.push({
                    idproducto: checkbox.dataset.productId,
                    cantidad: cantidadSolicitada
                });
            }
        });

        const idCliente = document.getElementById('idcliente').value;
        // Enviar los datos a guardar_pedido.php
        fetch('guardar_pedido.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        id_cliente: idCliente,
        productos: productosSeleccionados
    }),
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Pedido guardado correctamente.');
        window.location.href = 'crear_pedido.php';
    } else {
        alert('Error al guardar el pedido: ' + data.message);
    }
})
.catch(error => {
    console.error('Error al enviar los datos:', error);
    alert('Error de conexión. Verifique su conexión a Internet.');
});
file_put_contents('request_log.txt', print_r($data, true));

    });
});
</script>


                    <!--SCRIPT PARA MOVER LOS PRODUCTOS SOLICITADOS-->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.select-product');
    const inputSolicitudes = document.querySelectorAll('.input-solicitud');
    const btnAgregar = document.getElementById('btn-agregar');
    const tablaSeleccionados = document.getElementById('tabla-seleccionados').getElementsByTagName('tbody')[0];

    checkboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function () {
            inputSolicitudes[index].disabled = !checkbox.checked; // Habilita o deshabilita el input
            if (!checkbox.checked) {
                inputSolicitudes[index].value = 0; // Resetea el valor si se desmarca
            }
        });
    });

    btnAgregar.addEventListener('click', function () {
        tablaSeleccionados.innerHTML = ''; // Limpia la tabla antes de agregar productos

        let productosSeleccionados = 0; // Contar solo productos seleccionados
        let productosProcesados = 0;

        checkboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                productosSeleccionados++; // Contar productos seleccionados

                const cantidadSolicitada = inputSolicitudes[index].value;
                const nombreProducto = checkbox.dataset.productName;
                const categoriaProducto = checkbox.closest('tr').querySelector('td:nth-child(4)').textContent;
                const tallaProducto = checkbox.closest('tr').querySelector('td:nth-child(5)').textContent;

                enviarDatosSeleccionados(nombreProducto, categoriaProducto, tallaProducto, cantidadSolicitada)
                    .then(cantidadRealEnviada => {
                        if (cantidadRealEnviada > 0) {
                            const nuevaFila = `
                                <tr>
                                    <td>${nombreProducto}</td>
                                    <td>${categoriaProducto}</td>
                                    <td>${tallaProducto}</td>
                                    <td>${cantidadRealEnviada}</td>
                                </tr>`;
                            tablaSeleccionados.innerHTML += nuevaFila;
                        } else {
                            alert('No hay suficiente cantidad disponible');
                        }

                        productosProcesados++;

                        if (productosProcesados === productosSeleccionados) {
                            limpiarFormulario();
                        }
                    })
                    .catch(error => console.error('Error al enviar los datos:', error));
            }
        });
    });

    function enviarDatosSeleccionados(nombreProducto, categoriaProducto, tallaProducto, cantidadSolicitada) {
        return fetch('procesar_pedido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                nombre: nombreProducto,
                categoria: categoriaProducto,
                talla: tallaProducto,
                cantidad: cantidadSolicitada
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.cantidadEnviada || 0; 
            } else {
                alert('Error: ' + data.message);
                return 0;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            return 0;
        });
    }

    function limpiarFormulario() {
        checkboxes.forEach((checkbox, index) => {
            checkbox.checked = false;
            inputSolicitudes[index].disabled = true;
            inputSolicitudes[index].value = 0;
        });
    }
    btnAgregar.addEventListener('click', function () {
    window.location.href = 'crear_pedido.php';
});

});
</script>
<?php
include_once "pie.php";
include_once "validaciones/val_pedido.php";
?>
