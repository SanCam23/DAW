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

// Generación de la tabla
function generarTablaCostesPractica6() {
  const tarifas = {
    envio: 10,
    paginas: { menos5: 2, entre5y10: 1.8, mas10: 1.6 },
    color: { bn: 0, color: 0.5 },
    resol: { baja: 0, alta: 0.2 }
  };

  const paginas = Array.from({ length: 15 }, (_, i) => i + 1);
  const fotos = Array.from({ length: 15 }, (_, i) => (i + 1) * 3);

  // Crear tabla
  const tabla = document.createElement("table");
  tabla.border = "1";

  const caption = document.createElement("caption");
  caption.textContent = "Tabla de costes de un folleto publicitario impreso";
  tabla.appendChild(caption);

  // Cabecera principal
  const thead = document.createElement("thead");
  const tr1 = document.createElement("tr");

  const thPag = document.createElement("th");
  thPag.rowSpan = 2;
  thPag.textContent = "Número de páginas";
  tr1.appendChild(thPag);

  const thFotos = document.createElement("th");
  thFotos.rowSpan = 2;
  thFotos.textContent = "Número de fotos";
  tr1.appendChild(thFotos);

  const thBN = document.createElement("th");
  thBN.colSpan = 2;
  thBN.textContent = "Blanco y negro";
  tr1.appendChild(thBN);

  const thColor = document.createElement("th");
  thColor.colSpan = 2;
  thColor.textContent = "Color";
  tr1.appendChild(thColor);

  thead.appendChild(tr1);

  // Subcabecera
  const tr2 = document.createElement("tr");
  ["150-300 dpi", "450-900 dpi", "150-300 dpi", "450-900 dpi"].forEach(txt => {
    const th = document.createElement("th");
    th.textContent = txt;
    tr2.appendChild(th);
  });
  thead.appendChild(tr2);
  tabla.appendChild(thead);

  // Cuerpo de la tabla
  const tbody = document.createElement("tbody");

  paginas.forEach((p, idx) => {
    const tr = document.createElement("tr");

    const tdPag = document.createElement("td");
    tdPag.textContent = p;
    tr.appendChild(tdPag);

    const tdFotos = document.createElement("td");
    tdFotos.textContent = fotos[idx];
    tr.appendChild(tdFotos);

    // BN 150-300 dpi
    const tdBNbaja = document.createElement("td");
    tdBNbaja.textContent = calcularCoste(p, fotos[idx], "bn", "baja", tarifas).toFixed(2) + " €";
    tr.appendChild(tdBNbaja);

    // BN 450-900 dpi
    const tdBNalta = document.createElement("td");
    tdBNalta.textContent = calcularCoste(p, fotos[idx], "bn", "alta", tarifas).toFixed(2) + " €";
    tr.appendChild(tdBNalta);

    // Color 150-300 dpi
    const tdColorBaja = document.createElement("td");
    tdColorBaja.textContent = calcularCoste(p, fotos[idx], "color", "baja", tarifas).toFixed(2) + " €";
    tr.appendChild(tdColorBaja);

    // Color 450-900 dpi
    const tdColorAlta = document.createElement("td");
    tdColorAlta.textContent = calcularCoste(p, fotos[idx], "color", "alta", tarifas).toFixed(2) + " €";
    tr.appendChild(tdColorAlta);

    tbody.appendChild(tr);
  });

  tabla.appendChild(tbody);
  return tabla;
}

// Cálculo de precios por bloques (idéntico al enunciado)
function calcularCoste(pags, fotos, color, resol, t) {
  let costePaginas = 0;

  if (pags < 5) {
    costePaginas = pags * t.paginas.menos5;
  } else if (pags <= 10) {
    // Primer tramo: las primeras 5 páginas
    costePaginas = 5 * t.paginas.menos5;
    // Segundo tramo: el resto hasta 10
    costePaginas += (pags - 5) * t.paginas.entre5y10;
  } else {
    // Primer tramo: 5 páginas a 2 €
    costePaginas = 5 * t.paginas.menos5;
    // Segundo tramo: 5 páginas siguientes a 1.8 €
    costePaginas += 5 * t.paginas.entre5y10;
    // Tercer tramo: el resto a 1.6 €
    costePaginas += (pags - 10) * t.paginas.mas10;
  }

  // Coste adicional por color
  const costeColor = color === "color" ? fotos * t.color.color : 0;
  // Coste adicional por resolución alta
  const costeResol = resol === "alta" ? fotos * t.resol.alta : 0;

  return t.envio + costePaginas + costeColor + costeResol;
}



// Mostrar / Ocultar tabla
function toggleTablaCostes() {
  const cont = $("tablaCostes");
  const btn = $("btnTablaCostes");

  if (cont.hasChildNodes()) {
    cont.innerHTML = "";
    btn.textContent = "Mostrar tabla de costes";
  } else {
    const tabla = generarTablaCostesPractica6();
    cont.appendChild(tabla);
    btn.textContent = "Ocultar tabla de costes";
  }
}

// Inicialización
function iniciar() {
  $("formularioFolleto").addEventListener("submit", validarFormulario);
  $("cerrarModal").addEventListener("click", cerrarModal);
  $("btnTablaCostes").addEventListener("click", toggleTablaCostes);

  const rangoResolucion = $("resolucion");
  const salidaResolucion = rangoResolucion.nextElementSibling;

  salidaResolucion.textContent = `${rangoResolucion.value} DPI`;

  rangoResolucion.addEventListener("input", () => {
    salidaResolucion.textContent = `${rangoResolucion.value} DPI`;
  });
}


document.addEventListener("DOMContentLoaded", iniciar);
