document.addEventListener("DOMContentLoaded", function() {

    // Popup login
    const loginPopup = document.getElementById("login-popup");
    loginPopup.style.display = "none";

    const loginBtns = document.querySelectorAll('a[href="index_identificado.html"]');
    loginBtns.forEach(function(btn) {
        btn.addEventListener("click", function(event) {
            event.preventDefault();
            loginPopup.style.display = "block";
        });
    });

    // Dialog de error
    const errorDialog = document.getElementById("error-dialog");
    const errorMensaje = document.getElementById("error-mensaje");
    const cerrarError = document.getElementById("cerrar-error");

    cerrarError.addEventListener("click", function() {
        errorDialog.close();
    });

    // Validación del formulario
    const loginForm = document.getElementById("login");
    loginForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const usuario = document.getElementById("usuario").value.trim();
        const password = document.getElementById("password").value.trim();

        if (usuario === "" || password === "") {
            errorMensaje.textContent = "Por favor, completa ambos campos correctamente.";
            errorDialog.showModal();
            return;
        }

        // Redirigir si pasa la validación
        window.location.href = "index_identificado.html";
    });

    // Cerrar popup al hacer clic fuera del formulario
    window.addEventListener("click", function(event) {
        if (event.target === loginPopup) {
            loginPopup.style.display = "none";
        }
    });

});
