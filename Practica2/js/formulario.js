document.addEventListener("DOMContentLoaded", function() {

    const form = document.querySelector("form");
    const errorDialog = document.getElementById("error-dialog");
    const errorMensaje = document.getElementById("error-mensaje");
    const cerrarError = document.getElementById("cerrar-error");

    cerrarError.addEventListener("click", () => {
        errorDialog.close();
    });

    form.addEventListener("submit", function(event) {
        event.preventDefault();

        // --- Tipo de anuncio ---
        const tipoAnuncio = document.querySelector('input[name="tipo_anuncio"]:checked');
        if (!tipoAnuncio) {
            mostrarError("Debes seleccionar un tipo de anuncio (Venta o Alquiler).");
            return;
        }

        // --- Tipo de vivienda ---
        const tipoVivienda = document.querySelector('input[name="tipo_vivienda"]:checked');
        if (!tipoVivienda) {
            mostrarError("Debes seleccionar un tipo de vivienda.");
            return;
        }

        // --- Ciudad ---
        const ciudad = document.getElementById("ciudad").value.trim();
        if (ciudad === "") {
            mostrarError("Debes introducir una ciudad.");
            return;
        }

        // --- País ---
        const pais = document.getElementById("pais").value.trim();
        if (pais === "") {
            mostrarError("Debes introducir un país.");
            return;
        }

        // --- Precios ---
        const precioMinStr = document.getElementById("precio_min").value.trim();
        const precioMaxStr = document.getElementById("precio_max").value.trim();

        if (precioMinStr === "" || precioMaxStr === "") {
            mostrarError("Debes introducir el precio mínimo y máximo.");
            return;
        }

        const precioMin = parseFloat(precioMinStr);
        const precioMax = parseFloat(precioMaxStr);
        if (isNaN(precioMin) || isNaN(precioMax)) {
            mostrarError("Los precios deben ser números válidos.");
            return;
        }
        if (precioMax < precioMin) {
            mostrarError("El precio máximo no puede ser menor que el mínimo.");
            return;
        }

        // --- Fecha desde ---
        const fechaStr = document.getElementById("fecha").value.trim();
        if (fechaStr === "") {
            mostrarError("Debes introducir la fecha desde.");
            return;
        }
        if (!validarFecha(fechaStr)) {
            mostrarError("La fecha debe ser válida y en formato dd/mm/yyyy.");
            return;
        }
        if (!fechaNoMayorActual(fechaStr)) {
            mostrarError("La fecha no puede ser mayor que la fecha actual.");
            return;
        }

        // --- Si todo está correcto, enviar formulario ---
        form.submit();
    });

    function mostrarError(mensaje) {
        errorMensaje.textContent = mensaje;
        errorDialog.showModal();
    }

    function validarFecha(fechaStr) {
        const partes = fechaStr.split("/");
        if (partes.length !== 3) return false;

        const dia = parseInt(partes[0], 10);
        const mes = parseInt(partes[1], 10) - 1;
        const anio = parseInt(partes[2], 10);

        const fecha = new Date(anio, mes, dia);
        return fecha.getFullYear() === anio && fecha.getMonth() === mes && fecha.getDate() === dia;
    }

    function fechaNoMayorActual(fechaStr) {
        const partes = fechaStr.split("/");
        const dia = parseInt(partes[0], 10);
        const mes = parseInt(partes[1], 10) - 1;
        const anio = parseInt(partes[2], 10);

        const fecha = new Date(anio, mes, dia);
        const hoy = new Date();

        // Solo fecha, sin hora
        hoy.setHours(0,0,0,0);
        fecha.setHours(0,0,0,0);

        return fecha <= hoy;
    }

});
