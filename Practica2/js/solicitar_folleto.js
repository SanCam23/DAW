// Utilidad para acceder por ID
function $(id) {
  return document.getElementById(id);
}

// Mostrar modal con errores
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

// Cerrar modal
function cerrarModal() {
  $("modalErrores").close();
}

// Validación del formulario
function validarFormulario(event) {
  const errores = [];

  const nombre = $("nombre").value.trim();
  const email = $("email").value.trim();
  const calle = $("calle").value.trim();
  const numero = $("numero").value.trim();
  const cp = $("cp").value.trim();
  const localidad = $("localidad").value.trim();
  const provincia = $("provincia").value.trim();
  const pais = $("pais").value;
  const copias = parseInt($("copias").value);
  const resolucion = parseInt($("resolucion").value);
  const anuncio = $("anuncio").value;

  if (nombre === "") errores.push("El nombre completo es obligatorio.");
  if (email === "" || !email.includes("@")) errores.push("El correo electrónico es obligatorio y debe tener formato válido.");
  if (calle === "") errores.push("La calle es obligatoria.");
  if (numero === "") errores.push("El número es obligatorio.");
  if (cp === "") errores.push("El código postal es obligatorio.");
  if (localidad === "") errores.push("La localidad es obligatoria.");
  if (provincia === "") errores.push("La provincia es obligatoria.");
  if (pais === "") errores.push("El país es obligatorio.");
  if (isNaN(copias) || copias < 1 || copias > 99) errores.push("El número de copias debe estar entre 1 y 99.");
  if (isNaN(resolucion) || resolucion < 150 || resolucion > 900) errores.push("La resolución debe estar entre 150 y 900 DPI.");
  if (anuncio === "") errores.push("Debe seleccionar un anuncio.");

  if (errores.length > 0) {
    event.preventDefault();
    mostrarErrores(errores);
  }
}

// Inicializar eventos
function iniciar() {
  $("formularioFolleto").addEventListener("submit", validarFormulario);
  $("cerrarModal").addEventListener("click", cerrarModal);
}

document.addEventListener("DOMContentLoaded", iniciar);
