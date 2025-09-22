function formatearRUT(rut) {
    // Dejar solo números y K/k
    rut = rut.replace(/[^0-9kK]/g, "");
    rut = rut.replace(/k/g, "K"); // convertir k minúscula a K mayúscula

    // Limitar largo máximo a 9 caracteres (incluye DV)
    if (rut.length > 9) rut = rut.slice(0, 9);

    if (rut.length > 1) {
        let cuerpo = rut.slice(0, -1);
        let dv = rut.slice(-1);
        cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return cuerpo + "-" + dv;
    }
    return rut;
}

function validarLongitudRUT(input) {
    // mínimo 8 caracteres (sin puntos ni guion)
    let valor = input.value.replace(/\./g, "").replace("-", "");
    if (valor.length < 8) {
        input.setCustomValidity("El RUT debe tener mínimo 8 caracteres más DV");
    } else {
        input.setCustomValidity(""); // limpio el mensaje si es válido
    }
}

// Inputs de RUT
const rutEmpresa = document.getElementById("Rut_Empresa");
const rutRep = document.getElementById("Rut_Representante");

// Evento mientras se escribe (formateo visual)
[rutEmpresa, rutRep].forEach(input => {
    input.addEventListener("input", function () {
        this.value = formatearRUT(this.value);
        validarLongitudRUT(this);
    });
});

// =========================
//  Al enviar el formulario
//  limpia el RUT (sin puntos ni guion)
// =========================
function limpiarRUT(rut) {
    return rut.replace(/[^0-9K]/g, ""); // solo números y K
}

document.querySelector("form").addEventListener("submit", function () {
    rutEmpresa.value = limpiarRUT(rutEmpresa.value.toUpperCase());
    rutRep.value = limpiarRUT(rutRep.value.toUpperCase());
});






   document.addEventListener('DOMContentLoaded', function() {
            const addLinkBtn = document.getElementById('addLinkBtn');
            const linksContainer = document.getElementById('linksContainer');
            
            addLinkBtn.addEventListener('click', function() {
                const newLinkItem = document.createElement('div');
                newLinkItem.className = 'link-item';
                newLinkItem.innerHTML = `
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Tipo de Link</label>
                            <select class="form-select" name="Link_Nombre[]">
                                <option value="Página Web">Página Web</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Contenido del Link (ej: @empresa, https://...)</label>
                            <input type="text" class="form-control linkCuerpo" name="Link_Cuerpo[]">
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger w-100 remove-link">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                `;
                linksContainer.appendChild(newLinkItem);
                
                // Habilitar el botón de eliminar del primer elemento si hay más de uno
                if (linksContainer.children.length > 1) {
                    linksContainer.children[0].querySelector('.remove-link').disabled = false;
                }
                
                // Agregar evento al botón de eliminar
                newLinkItem.querySelector('.remove-link').addEventListener('click', function() {
                    linksContainer.removeChild(newLinkItem);
                    
                    // Deshabilitar el botón de eliminar del primer elemento si solo queda uno
                    if (linksContainer.children.length === 1) {
                        linksContainer.children[0].querySelector('.remove-link').disabled = true;
                    }
                });
            });
            
            // Inicializar tooltips de Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

const telInput = document.getElementById("Telefono_Interlocutor");

// Inicializar con +56
if (!telInput.value.startsWith("+56")) {
    telInput.value = "+56 ";
}

telInput.addEventListener("input", function() {
    // Quitar todo excepto números
    let numeros = this.value.replace(/\D/g, "");
    
    // Mantener solo los últimos 9 números
    numeros = numeros.slice(-9);

});