<?php
session_start();

// Prevenir que la página se almacene en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "proyecto");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Obtener el ID de usuario de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Consulta SQL para obtener los datos del usuario
$query = "SELECT p.nombre, p.apellido1, p.apellido2, p.foto, r.nombre as rol_nombre
          FROM usuario u
          JOIN persona p ON u.persona_idpersona = p.idpersona
          JOIN privilegio pr ON u.idusuario = pr.usuario_idusuario
          JOIN rol r ON pr.rol_idrol = r.idrol
          WHERE u.idusuario = ?";

// Preparar la consulta
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error al preparar la consulta: ' . htmlspecialchars($conn->error));
}

// Vincular parámetros y ejecutar la consulta
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Obtener los datos del usuario
$user_data = $result->fetch_assoc();

// Cerrar la consulta y la conexión
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ADMINISTRADOR</title>
	<link rel="stylesheet" href="../../css/normalize.css">
	<link rel="stylesheet" href="../../css/sweetalert2.css">
	<link rel="stylesheet" href="../../css/material.min.css">
	<link rel="stylesheet" href="../../css/material-design-iconic-font.min.css">
	<link rel="stylesheet" href="../../css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="../../css/main.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="../../js/jquery-1.11.2.min.js"><\/script>')</script>
	<script src="../../js/material.min.js" ></script>
	<script src="../../js/sweetalert2.min.js" ></script>
	<script src="../../js/jquery.mCustomScrollbar.concat.min.js" ></script>
	<script src="../../js/main.js" ></script>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM+R7fRZP5o1Bq6BOm29P4ViYp3oK0I4s3lgH9" crossorigin="anonymous">


<!--ESTILOS DE LETRA -->
	<style>
        .mdl-textfield__input {
            color: #333;
        }
        .mdl-textfield__label {
            color: #666;
        }
        .mdl-textfield--floating-label.is-focused .mdl-textfield__label {
            color: #333;
        }
    </style>

</head>
<body>
	
	<!-- navLateral -->
	<section class="full-width navLateral">
		<div class="full-width navLateral-bg btn-menu"></div>
		<div class="full-width navLateral-body">
			<div class="full-width navLateral-body-logo text-center tittles">
				<i class="zmdi zmdi-close btn-menu"></i> BIENVENIDO
			</div>
			<figure class="full-width navLateral-body-tittle-menu">
				<!--<div>
					<img src="<?php echo htmlspecialchars($user_data['foto']); ?>" alt="Avatar" class="img-responsive">
				</div>-->
				<figcaption>
				<span>
                        <?php echo isset($user_data['nombre']) ? htmlspecialchars($user_data['nombre']) . ' ' . htmlspecialchars($user_data['apellido1']) . ' ' . htmlspecialchars($user_data['apellido2']): 'Nombre no disponible'; ?><br>
						____________________<br>
                        <small><?php echo isset($user_data['rol_nombre']) ? htmlspecialchars($user_data['rol_nombre']) : 'Rol no disponible'; ?></small>
                    </span>
				</figcaption>
			</figure>
			<nav class="full-width">
				<ul class="full-width list-unstyle menu-principal">
					<li class="full-width">
						<a href="home.php" class="full-width">
							<div class="navLateral-body-cl">
								<i class="zmdi zmdi-view-dashboard"></i>
							</div>
							<div class="navLateral-body-cr">
								TODO
							</div>
						</a>
					</li>
					<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="#!" class="full-width btn-subMenu">
							<div class="navLateral-body-cl">
								<i class="zmdi zmdi-case"></i>
							</div>
							<div class="navLateral-body-cr">
								ADMINISTRACION
							</div>
							<span class="zmdi zmdi-chevron-left"></span>
						</a>
						<ul class="full-width menu-principal sub-menu-options">
							<li class="full-width">
								<a href="categoria.php" class="full-width">
									<div class="navLateral-body-cl">
										<i class="zmdi zmdi-label"></i>
									</div>
									<div class="navLateral-body-cr">
										CATEGORIAS
									</div>
								</a>
							</li>
							<li class="full-width">
								<a href="rol.php" class="full-width">
									<div class="navLateral-body-cl">
										<i class="zmdi zmdi-accounts"></i>
									</div>
									<div class="navLateral-body-cr">
										ROLES
									</div>
								</a>
							</li>
						</ul>
					</li>
					<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="#!" class="full-width btn-subMenu">
							<div class="navLateral-body-cl">
								<i class="zmdi zmdi-face"></i>
							</div>
							<div class="navLateral-body-cr">
								USUARIO
							</div>
							<span class="zmdi zmdi-chevron-left"></span>
						</a>
						<ul class="full-width menu-principal sub-menu-options">
							<li class="full-width">
								<a href="cliente.php" class="full-width">
									<div class="navLateral-body-cl">
										<i class="zmdi zmdi-accounts"></i>
									</div>
									<div class="navLateral-body-cr">
										CLIENTES
									</div>
								</a>
							</li>
							<li class="full-width">
								<a href="personal.php" class="full-width">
									<div class="navLateral-body-cl">
										<i class="zmdi zmdi-accounts"></i>
									</div>
									<div class="navLateral-body-cr">
										PERSONAL
									</div>
								</a>
							</li>
						</ul>
					</li>
					<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="productos.php" class="full-width">
							<div class="navLateral-body-cl">
							<i class="fi fi-ss-shirt-long-sleeve"></i>
							</div>
							<div class="navLateral-body-cr">
								PRODUCTOS
							</div>
						</a>
					</li>
					<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="pagos.php" class="full-width">
							<div class="navLateral-body-cl">
							<i class="fi fi-sr-coins"></i>
							</div>
							<div class="navLateral-body-cr">
								PREALIZAR VENTA
							</div>
						</a>
					</li>
					<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="ventas.php" class="full-width">
							<div class="navLateral-body-cl">
								<i class="zmdi zmdi-shopping-cart"></i>
							</div>
							<div class="navLateral-body-cr">
								VENTAS
							</div>
						</a>
					</li>
					<!--<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="reportes.php" class="full-width">
							<div class="navLateral-body-cl">
							<i class="fi fi-sr-journal-alt"></i>
							</div>
							<div class="navLateral-body-cr">
								REPORTES
							</div>
						</a>
					</li>-->
					<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="pedidos.php" class="full-width">
							<div class="navLateral-body-cl">
							<i class="fi fi-ss-order-history"></i>
							</div>
							<div class="navLateral-body-cr">
								PEDIDOS
							</div>
						</a>
					</li>
					<li class="full-width divider-menu-h"></li>
					<li class="full-width">
						<a href="stock.php" class="full-width">
							<div class="navLateral-body-cl">
								<i class="zmdi zmdi-store"></i>
							</div>
							<div class="navLateral-body-cr">
								STOCK
							</div>
						</a>
					</li>
					
				</ul>
			</nav>
		</div>
	</section>
	<section class="full-width pageContent">
		<!-- navBar -->
		<div class="full-width navBar">
			<div class="full-width navBar-options">
				<i class="zmdi zmdi-swap btn-menu" id="btn-menu"></i>	
				<div class="mdl-tooltip" for="btn-menu">Esconder / Mostrar Menu</div>
				<nav class="navBar-options-list">
					<ul class="list-unstyle">
						<li class="btn-exit" id="exit">
							<i class="zmdi zmdi-power"></i>
							<div class="mdl-tooltip" for="exit">Cerrar Sesión</div>
						</li>
						<li class="text-condensedLight noLink" ><small>-</small></li>
						<li class="noLink">.</li>
					</ul>
				</nav>

			</div>
		</div>
		<!-------------------------------------------------------------->
		<!-- Alerta de cierre de sesion -->
		<!-------------------------------------------------------------->
		<script>
document.addEventListener('DOMContentLoaded', function () {
    var logoutButton = document.getElementById('exit');

	logoutButton.addEventListener('click', function () {
    Swal.fire({
        title: '¿Estás seguro de que deseas cerrar sesión?',
        text: 'Se cerrará tu sesión actual.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Asegúrate de que la ruta a logout.php es correcta
            window.location.href = '../../logout.php';
        }
    });
});

});
</script>

