
function $(id) {
  return document.getElementById(id);
}

function mostrarErrores(mensajes) {
  const modal = $("modalErrores");
  const lista = $("listaErrores");
  lista.innerHTML = "";
  mensajes.forEach(msg => {
    const li = document.createElement("li");
    li.textContent = msg;
    lista.appendChild(li);
  });
  modal.showModal();
}

function cerrarModal() {
  $("modalErrores").close();
}

function validarFormulario(event) {
  const errores = [];

  const tipoMensaje = document.querySelector('input[name="tipo"]:checked');
  const mensaje = $("mensaje").value.trim();

  if (!tipoMensaje) {
    errores.push("Debes seleccionar un tipo de mensaje.");
  }

  if (mensaje === "") {
    errores.push("El campo de mensaje no puede estar vacío.");
  } else if (mensaje.length < 10) {
    errores.push("El mensaje debe tener al menos 10 caracteres.");
  }

  if (errores.length > 0) {
    event.preventDefault();
    mostrarErrores(errores);
  }
}

function iniciar() {
  const formulario = document.querySelector("form");
  formulario.addEventListener("submit", validarFormulario);
  $("cerrarModal").addEventListener("click", cerrarModal);
}

document.addEventListener("DOMContentLoaded", iniciar);
