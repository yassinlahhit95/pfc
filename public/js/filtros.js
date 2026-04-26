/**
 * Filtros dinámicos para tablas - CPS IBAIONDO
 * Este archivo permite filtrar cualquier tabla buscando texto en sus filas.
 */

/**
 * Filtra una tabla basándose en el texto de un input o select
 * @param {string} inputId - ID del campo que tiene el texto a buscar
 * @param {string} tablaId - ID de la tabla a filtrar
 * @param {number} columnaIndice - (Opcional) Índice de la columna específica (0, 1, 2...)
 */
function filtrarTabla(inputId, tablaId, columnaIndice = -1) {
    var inputBusqueda = document.getElementById(inputId);
    var textoABuscar = inputBusqueda.value.toUpperCase();
    var tabla = document.getElementById(tablaId);
    var filas = tabla.getElementsByTagName("tr");

    // Recorremos todas las filas (saltando el encabezado)
    for (var i = 1; i < filas.length; i++) {
        var mostrarFila = false;
        var celdas = filas[i].getElementsByTagName("td");
        
        if (celdas.length > 0) {
            // Si columnaIndice es -1, busca en todas las columnas
            if (columnaIndice === -1) {
                for (var j = 0; j < celdas.length; j++) {
                    var textoCelda = celdas[j].textContent || celdas[j].innerText;
                    if (textoCelda.toUpperCase().indexOf(textoABuscar) > -1) {
                        mostrarFila = true;
                    }
                }
            } else {
                // Busca solo en la columna especificada
                var textoCelda = celdas[columnaIndice].textContent || celdas[columnaIndice].innerText;
                if (textoCelda.toUpperCase().indexOf(textoABuscar) > -1) {
                    mostrarFila = true;
                }
            }
        }

        // Mostrar u ocultar la fila
        if (mostrarFila) {
            filas[i].style.display = "";
        } else {
            filas[i].style.display = "none";
        }
    }
}
