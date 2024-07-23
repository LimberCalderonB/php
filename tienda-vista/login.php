<?php
include_once "cabecera.php";
?>

<style>
    body {
    font-family: Arial, sans-serif;
    background: #f0f0f0;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    min-height: 100vh; /* Asegura que el body ocupe toda la altura */
}

.contenedor-formularios {
    background: #fff;
    padding: 40px;
    max-width: 600px;
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    margin-bottom: 20px; /* Espacio para el pie de página */
}

footer {
    background: #1ab188;
    color: #fff;
    text-align: center;
    padding: 10px;
    width: 100%;
    position: fixed;
    bottom: 0;
    left: 0;
}

footer p {
    margin: 0;
    font-size: 14px;
}


    .contenedor-tabs {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .contenedor-tabs li {
        flex: 1;
    }

    .contenedor-tabs li a {
        display: block;
        text-decoration: none;
        padding: 15px;
        background: #f0f0f0;
        color: #333;
        text-align: center;
        font-size: 16px;
        transition: background 0.3s, color 0.3s;
    }

    .contenedor-tabs li a:hover,
    .contenedor-tabs li.active a {
        background: #1ab188;
        color: #fff;
    }

    .contenido-tab > div {
        display: none;
    }

    .contenido-tab > div.active {
        display: block;
    }

    h1 {
        text-align: center;
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        color: #333;
    }

    input[type="text"], input[type="password"], input[type="number"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 16px;
        color: #333;
        box-sizing: border-box;
    }

    input[type="submit"] {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 4px;
        background: #1ab188;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        transition: background 0.3s;
    }

    input[type="submit"]:hover {
        background: #0f9d58;
    }

    .forgot {
        text-align: right;
        margin-bottom: 20px;
    }

    .forgot a {
        color: #1ab188;
        text-decoration: none;
        font-size: 14px;
    }

    .forgot a:hover {
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 500px) {
        .contenedor-formularios {
            padding: 20px;
        }

        .contenedor-tabs li a {
            font-size: 14px;
            padding: 10px;
        }

        input[type="submit"] {
            font-size: 16px;
        }
    }
</style>
<br>
<br>
<br>
<br>
<div class="contenedor-formularios">
    <ul class="contenedor-tabs">
        <li class="tab active"><a href="#iniciar-sesion">Iniciar Sesión</a></li>
        <li class="tab"><a href="#registrarse">Registrarse</a></li>
    </ul>

    <div class="contenido-tab">
        <!-- Iniciar Sesión -->
        <div id="iniciar-sesion" class="active">
        <br>
            <h1>Iniciar Sesión</h1>
            <form action="#" method="post">
                <label for="loginUsername">Usuario</label>
                <input id="loginUsername" type="text" required>
                <label for="loginPassword">Contraseña</label>
                <input id="loginPassword" type="password" required>
                <p class="forgot"><a href="#">¿Olvidaste tu contraseña?</a></p>
                <input type="submit" value="Iniciar Sesión">
            </form>
        </div>

        <!-- Registrarse -->
        <div id="registrarse">
            <br>
            <h1>Registrarse</h1>
            <form action="#" method="post">
                <label for="registerName">Nombre</label>
                <input id="registerName" type="text" required>
                <label for="registerSurname">Apellido</label>
                <input id="registerSurname" type="text" required>
                <label for="registerPhone">Celular</label>
                <input id="registerPhone" type="number" required>
                <label for="registerUsername">Usuario</label>
                <input id="registerUsername" type="text" required>
                <label for="registerPassword">Contraseña</label>
                <input id="registerPassword" type="password" required>
                <input type="submit" value="Registrarse">
            </form>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.contenedor-tabs li a');
        const forms = document.querySelectorAll('.contenido-tab > div');

        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();

                const targetId = tab.getAttribute('href');
                
                // Remove active class from all tabs and forms
                tabs.forEach(t => t.parentElement.classList.remove('active'));
                forms.forEach(f => f.classList.remove('active'));

                // Add active class to clicked tab and corresponding form
                tab.parentElement.classList.add('active');
                document.querySelector(targetId).classList.add('active');
            });
        });
    });
</script>
<br>
<br>
<br>
<?php
include_once "pie.php";
?>
