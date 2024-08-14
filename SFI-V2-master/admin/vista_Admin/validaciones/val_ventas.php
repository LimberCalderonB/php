
<style>
    .centered-table th, .centered-table td {
        text-align: center;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .mdl-data-table {
        width: 100%;
        margin: auto;
    }

    .mdl-data-table th, .mdl-data-table td {/*anchi de la tabla */
        padding: 4px 40px;
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

    .btn-container {
        text-align: right;
        margin-top: 5px;
        margin-right: 5px;
    }

    .btn-descargar {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 6px 12px;/*tamaño de boton*/ 
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-descargar i {
        margin-left: 2px;
        font-size: 14px;
    }

    .btn-descargar:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .btn-descargar:focus {
        outline: none;
    }
</style>

<!--ESTILO DE LA PAGINACION-->
<style>
    /* Estilos generales para la paginación */
.pagination {
    display: flex;
    justify-content: center;
    padding: 0;
    margin: 20px 0;
    list-style: none;
}

.page-item {
    margin: 0 5px;
}

.page-link {
    display: block;
    padding: 10px 15px;
    font-size: 1em;
    color: #007bff;
    text-decoration: none;
    border: 1px solid #007bff;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.page-link:hover {
    background-color: #007bff;
    color: #ffffff;
}

.page-item.active .page-link {
    background-color: #007bff;
    color: #ffffff;
    border-color: #007bff;
    font-weight: bold;
}

/* Agrega estilos para el estado deshabilitado (si es necesario) */
.page-item.disabled .page-link {
    color: #6c757d;
    border-color: #6c757d;
    cursor: not-allowed;
}

/* Asegúrate de que los estilos se apliquen en diferentes tamaños de pantalla */
@media (max-width: 768px) {
    .pagination {
        font-size: 0.9em;
    }
}

</style>