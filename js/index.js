// ---- RUT con formato ----
function formatearRUT(rut){
    rut = rut.toUpperCase().replace(/[^0-9K]/g,'');
    if(rut.length <= 1) return rut;
    let cuerpo = rut.slice(0,-1);
    let dv = rut.slice(-1);
    cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return cuerpo + '-' + dv;
}

// Aplicar formato al RUT en input
$('#rut').on('input', function(){
    let val = $(this).val().toUpperCase();
    val = val.replace(/[^0-9K]/g,'');
    if(val.indexOf('K') !== -1){ val = val.replace(/K/g,'') + 'K'; }
    $(this).val(formatearRUT(val));
});

// Funciones para el popup
function mostrarPopup() {
    $('#popupOverlay').addClass('active');
}
function cerrarPopup() {
    $('#popupOverlay').removeClass('active');
}
function masInformacion() {
    window.location.href = "https://www.fomentolf.cl/convenios.php";
}

// Asignar eventos a los botones del popup
$('#closePopup, #closeBtn').on('click', cerrarPopup);
$('#moreInfo').on('click', masInformacion);

// Buscar RUT y mostrar popup si no se encuentra
$('#rut').on('blur', function(){
    let rut = $(this).val().trim();
    if(rut.length < 8) return;
    $.post('php/flujo_cowork/buscar_rut.php', {rut:rut}, function(res){
        if(res.encontrado){
            $('#nombre_vecino').val(res.nombre);
            $('#apellido_vecino').val(res.apellido);
        } else {
            mostrarPopup();
        }
    }, 'json').fail(function() {
        console.error('Error en la solicitud AJAX');
    });
});
// ---- Hora fin según inicio ----
$('#hora_inicio').on('change', function(){
    let h = parseInt($(this).val());
    let finSelect = $('#hora_fin');
    finSelect.empty().append('<option value="">Seleccione hora fin</option>');

    if(!isNaN(h)){
        for(let i=1;i<=2;i++){
            if(h+i<=18){
                finSelect.append('<option value="'+(h+i)+'">'+(h+i)+':00</option>');
            }
        }
        // Si inicio = 13 -> bloquear fin 14 y 15
        if(h === 13){
            finSelect.find('option[value="14"]').prop('disabled', true);
            finSelect.find('option[value="15"]').prop('disabled', true);
        }
    }
    actualizarCupos();
});

// ---- Bloqueos base ----
function aplicarBloqueosBase(fechaStr){
    $('#hora_inicio option').prop('disabled', false);
    $('#hora_fin option').prop('disabled', false);
    $('#cupos_disp').text('');

    if(!fechaStr) return;

    const d = new Date(fechaStr + 'T00:00:00');
    const dow = d.getDay(); // 0=Domingo, 6=Sábado

    // Fines de semana -> todo bloqueado
    if(dow === 0 || dow === 6){
        $('#hora_inicio option, #hora_fin option').prop('disabled', true);
        $('#cupos_disp').text('No se permiten reservas fines de semana');
        $('#hora_inicio, #hora_fin').val('');
        return;
    }

    // Bloquear hora de colación 14:00 siempre (L-V)
    $('#hora_inicio option[value="14"]').prop('disabled', true);

    // Bloquear 15:00 si es HOY y todavía no son las 15:00
    const now = new Date();
    const sameDay = d.toDateString() === now.toDateString();
    if(sameDay && now.getHours() < 15){
        $('#hora_inicio option[value="15"]').prop('disabled', true);
    }

    // Limpiar si quedó una selección inválida
    if($('#hora_inicio option:selected').prop('disabled')){
        $('#hora_inicio').val('');
        $('#hora_fin').empty().append('<option value="">Seleccione hora fin</option>');
    }
}

// ---- Bloqueos BD + cupos ----
function actualizarCupos(){
    let cowork_id = $('#cowork_select').val();
    let fecha = $('#fecha').val();
    let hora_inicio = $('#hora_inicio').val();
    let hora_fin = $('#hora_fin').val();

    if(!cowork_id || !fecha) return;

    aplicarBloqueosBase(fecha);

    // Si es finde, ya no sigue
    const dow = new Date(fecha + 'T00:00:00').getDay();
    if(dow === 0 || dow === 6) return;

    // Bloqueos desde BD
    $.ajax({
        url:'php/flujo_cowork/ver_bloqueos.php',
        method:'POST',
        data:{ cowork_id, fecha },
        dataType:'json',
        success:function(res){
            $('#hora_inicio option').each(function(){
                const h = parseInt($(this).val());
                if(isNaN(h)) return;
                const bloqueado = res.some(b => h >= b.hora_inicio && h < b.hora_fin);
                if(bloqueado) $(this).prop('disabled', true);
            });

            if($('#hora_inicio option:selected').prop('disabled')){
                $('#hora_inicio').val('');
                $('#hora_fin').empty().append('<option value="">Seleccione hora fin</option>');
            }
        }
    });

    // Cupos si ya hay rango elegido
    if(hora_inicio && hora_fin){
        $.ajax({
            url: 'php/flujo_cowork/verificar_cupos.php',
            method: 'POST',
            data: { cowork_id, fecha, hora_inicio, hora_fin },
            dataType: 'json',
            success: function(res){
                if(res.disponible >= 1){
                    $('#cupos_disp').text(`Cupos disponibles: ${res.disponible}`);
                    let max_personas = Math.min(res.disponible,5);
                    $('#cantidad_personas').attr('max', max_personas);
                } else {
                    $('#cupos_disp').text('Cowork lleno para ese horario');
                    $('#cantidad_personas').attr('max',0);
                }
            }
        });
    }
}

// ---- Limpieza RUT al enviar ----
$('#reservaForm').on('submit', function(){
    let rut = $('#rut').val().replace(/\./g,'').replace(/-/g,'');
    $('#rut').val(rut);
});

// ---- Triggers ----
$('#cowork_select,#fecha,#hora_inicio,#hora_fin').on('change', actualizarCupos);

$(document).ready(function(){
    actualizarCupos();
});
