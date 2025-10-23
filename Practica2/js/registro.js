document.addEventListener("DOMContentLoaded", function() {

    const form = document.getElementById("registro-form");
    const errorDialog = document.getElementById("error-dialog");
    const errorMensaje = document.getElementById("error-mensaje");
    const cerrarError = document.getElementById("cerrar-error");

    cerrarError.addEventListener("click", function() {
        errorDialog.close();
    });

    form.addEventListener("submit", function(event) {
        event.preventDefault();

        const usuario = document.getElementById("usuario").value.trim();
        const password = document.getElementById("password").value.trim();
        const confirmPassword = document.getElementById("confirm_password").value.trim();
        const email = document.getElementById("email").value.trim();
        const sexo = document.getElementById("sexo").value;
        const fechaNacimiento = document.getElementById("fecha_nacimiento").value;

        const usuarioRegex = /^[A-Za-z][A-Za-z0-9]{2,14}$/;
        if (!usuarioRegex.test(usuario)) {
            mostrarError("El nombre de usuario debe tener entre 3 y 15 caracteres, contener solo letras y números, y no empezar con un número.");
            return;
        }

        const passwordRegex = /^[A-Za-z0-9_-]{6,15}$/;
        if (!passwordRegex.test(password)) {
            mostrarError("La contraseña debe tener entre 6 y 15 caracteres y solo puede contener letras, números, guion y guion bajo.");
            return;
        }
        if (!(/[A-Z]/.test(password) && /[a-z]/.test(password) && /[0-9]/.test(password))) {
            mostrarError("La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un número.");
            return;
        }

        if (password !== confirmPassword) {
            mostrarError("Las contraseñas no coinciden.");
            return;
        }

        if (!validarEmail(email)) {
            mostrarError("Correo electrónico no válido.");
            return;
        }

        if (sexo === "") {
            mostrarError("Debes seleccionar un sexo.");
            return;
        }

        if (!validarFechaNacimiento(fechaNacimiento)) {
            mostrarError("Debes ser mayor de 18 años.");
            return;
        }

        form.submit();
    });

    function mostrarError(mensaje) {
        errorMensaje.textContent = mensaje;
        errorDialog.showModal();
    }

    function validarEmail(email) {
        if (email.length === 0 || email.length > 254) return false;

        const partes = email.split("@");
        if (partes.length !== 2) return false;

        const local = partes[0];
        const dominio = partes[1];

        if (local.length < 1 || local.length > 64) return false;
        if (dominio.length < 1 || dominio.length > 255) return false;

        const localRegex = /^[A-Za-z0-9!#$%&'*+\-/=?^_`{|}~]+(\.[A-Za-z0-9!#$%&'*+\-/=?^_`{|}~]+)*$/;
        if (!localRegex.test(local)) return false;

        const subdominios = dominio.split(".");
        for (let sub of subdominios) {
            if (sub.length < 1 || sub.length > 63) return false;
            if (!/^[A-Za-z0-9-]+$/.test(sub)) return false;
            if (sub.startsWith("-") || sub.endsWith("-")) return false;
        }

        return true;
    }

    function validarFechaNacimiento(fechaStr) {

        const partes = fechaStr.split("/");
        if (partes.length !== 3) return false;

        const dia = parseInt(partes[0], 10);
        const mes = parseInt(partes[1], 10) - 1; 
        const anio = parseInt(partes[2], 10);

        const fecha = new Date(anio, mes, dia);
        if (fecha.getFullYear() !== anio || fecha.getMonth() !== mes || fecha.getDate() !== dia) {
            return false; 
        }

        const hoy = new Date();
        let edad = hoy.getFullYear() - anio;
        if (hoy.getMonth() < mes || (hoy.getMonth() === mes && hoy.getDate() < dia)) {
            edad--;
        }

        return edad >= 18;
}


});
