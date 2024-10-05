<style>
    .btn-container {
        padding: 5px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-descargar {
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
    }

    .btn-accion {
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        margin-right: 5px;
        font-size: 0.75rem; /* 14px */
        display: inline-block;
        transition: background-color 0.3s; /* Transición suave */
    }

    .btn-editar {
        background-color: #ffc107; /* Amarillo */
    }

    .btn-eliminar {
        background-color: #dc3545; /* Rojo */
    }

    .btn-detalles {
        background-color: #17a2b8; /* Cian */
    }

    .btn-accion:hover {
        opacity: 0.8;
    }
</style>

<style>
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

/* Estilos para el contenedor del botón */
.btn-container {
    text-align: right;
    margin-top: 20px;
    margin-right: 20px;
}

/* Estilos para el botón de descarga */
.btn-descargar {
    background-color: #007bff; /* Color de fondo */
    color: #fff; /* Color del texto */
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px; /* Bordes redondeados */
    transition: background-color 0.3s, transform 0.3s; /* Transición suave para color y tamaño */
    display: inline-flex;
    align-items: center;
}

.btn-descargar i {
    margin-left: 10px; /* Espacio entre el texto y el ícono */
    font-size: 18px; /* Tamaño del ícono */
}

/* Estilos cuando se pasa el ratón sobre el botón */
.btn-descargar:hover {
    background-color: #0056b3; /* Color de fondo cuando se pasa el ratón */
    transform: scale(1.05); /* Aumenta el tamaño ligeramente */
}

.btn-descargar:focus {
    outline: none; /* Quitar el borde de enfoque */
}
</style>

<script>
// Función para exportar la tabla a CSV
function exportTableToCSV(filename, tableSelector) {
    var csv = [];
    var rows = document.querySelectorAll(tableSelector + " tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");

        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }

        csv.push(row.join(","));        
    }

    // Crear un enlace para descargar el archivo
    var csvFile;
    var downloadLink;

    csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);

    downloadLink.click();
}

// Asignar la función al botón para exportar la tabla activa
document.querySelector(".btn-descargar").addEventListener("click", function() {
    var activeTab = document.querySelector(".mdl-tabs__panel.is-active");
    exportTableToCSV("productos.csv", activeTab.querySelector("table").outerHTML);
});
</script>
