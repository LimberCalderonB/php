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
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

.productos-seleccionados .row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.product-card {
    width: 100%;
    max-width: 250px;
    height: 400px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.product-images {
    position: relative;
    width: 100%;
    height: 60%;
    overflow: hidden;
}

.product-image {
    display: none;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-image.active {
    display: block;
}

.prev-button, .next-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.7);
    border: none;
    cursor: pointer;
}

.prev-button {
    left: 0;
}

.next-button {
    right: 0;
}

.product-info {
    padding: 10px;
    text-align: center;
}

.product-info small {
    display: block;
}

.product-date small {
    display: block;
    color: gray;
}

.product-price {
    padding: 10px;
    text-align: center;
}

.product-price.discount {
    color: red;
}

.product-price.discount::after {
    content: ' ';
    display: block;
    height: 1px;
    background-color: red;
    width: 100%;
    position: relative;
    top: -0.5em;
}

.btn-container {
    margin-top: auto;
    padding: 10px;
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const ventaButton = document.getElementById('realizar-venta');

    ventaButton.addEventListener('click', () => {
        alert('Venta realizada');
    });
});

</script>