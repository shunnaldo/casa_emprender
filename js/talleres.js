function formatearRut(rut) {
    rut = rut.replace(/[^\dkK]/g, ""); 
    if (rut.length > 1) {
        let cuerpo = rut.slice(0, -1);
        let dv = rut.slice(-1).toUpperCase(); 
        cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return cuerpo + "-" + dv;
    }
    return rut.toUpperCase(); 
}

document.getElementById("rut").addEventListener("input", function(e) {
    let valor = e.target.value.replace(/\./g, "").replace(/-/g, "");
    e.target.value = formatearRut(valor);
});
