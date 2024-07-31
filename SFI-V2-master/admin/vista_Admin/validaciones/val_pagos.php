



<style>
/* Estilos existentes */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

.top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.product-search {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    width: 400px;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 400px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.btn-realizar-venta {
    background-color: #176098;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1rem;
    display: flex;
    align-items: center;
    transition: transform 0.2s ease-in-out;
}

.btn-realizar-venta i {
    margin-right: 5px;
}

/* Estilo de animación para el botón */
.btn-realizar-venta:hover {
    transform: translateY(-5px);
}

/* Estilos adicionales */
.total-cost {
    text-align: right;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 20px;
}

.productos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.product-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.product-images {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.product-image {
    position: absolute;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.product-image.active {
    display: block;
}

.prev-button, .next-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 5px;
    cursor: pointer;
    z-index: 1;
}

.prev-button {
    left: 5px;
}

.next-button {
    right: 5px;
}

.product-info {
    padding: 15px;
    display: flex;
    justify-content: space-between;
}

.product-info small {
    font-size: 0.875em;
}

.separator {
    margin: 0 5px;
}

.product-price {
    padding: 10px;
    font-size: 0.75rem;
}

.product-price.discount {
    color: black;
}

.original-price {
    text-decoration: line-through;
    margin-right: 5px;
}

.btn-container {
    padding: 10px;
    display: flex;
    justify-content: flex-end;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    padding: 7px 10px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1rem;
    display: flex;
    align-items: center;
}

.btn-danger i {
    margin-right: 5px;
}

</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const images = card.querySelectorAll('.product-image');
        const prevButton = card.querySelector('.prev-button');
        const nextButton = card.querySelector('.next-button');

        let currentIndex = 0;

        const showImage = index => {
            images.forEach((img, i) => img.classList.toggle('active', i === index));
        };

        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
            showImage(currentIndex);
        });

        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
            showImage(currentIndex);
        });

        showImage(currentIndex);
    });
});

</script>

