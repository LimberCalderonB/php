<?php
include_once "cabecera.php";
?>
<br>
<div class="container">
    <button id="realizar-venta" class="btn-realizar-venta">
        <i class="fi fi-rr-usd-circle"></i>
        REALIZAR VENTA
    </button>
</div>

<?php
include_once "pie.php";
?>

<!--ESTILO DE BOTON -->
<style>
body {
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
}

.container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-end; /* Align to bottom */
    margin-bottom: 20px; /* Adjust as needed */
}

.btn-realizar-venta {
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-realizar-venta i {
    margin-right: 10px;
    font-size: 20px;
}

.btn-realizar-venta:hover {
    background-color: #218838;
}
</style>

<!--ESTILO JS DE BOTON-->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ventaButton = document.getElementById('realizar-venta');

    ventaButton.addEventListener('click', () => {
        alert('Venta realizada');
    });
});
</script>
