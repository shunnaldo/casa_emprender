<?php
include 'php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserva_id = intval($_POST['reserva_id']);
    
    // Verificar que la reserva exista y esté activa
    $res = $connCasa->query("SELECT * FROM tbl_Reserva WHERE id=$reserva_id AND estado IN ('pendiente','lista')");
    if ($res->num_rows === 0) {
        echo json_encode(['success' => false, 'msg' => 'Reserva no encontrada o ya finalizada.']);
        exit;
    }

    // Cambiar estado a cancelada
    $connCasa->query("UPDATE tbl_Reserva SET estado='cancelada' WHERE id=$reserva_id");
    echo json_encode(['success' => true, 'msg' => 'Reserva cancelada correctamente.']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar Reserva Cowork</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
<link rel="stylesheet" href="css/cancelar_asistencia.css">
</head>
<body>
    <br>
    <?php include 'injectable/navbar.php'; ?>

    <br><br>

<div class="container mt-3 mb-5 d-flex justify-content-center">
    <div class="card shadow-sm w-100" style="max-width: 600px;">
        <div class="card-header d-flex align-items-center">
            Cancelar Reserva Cowork
        </div>
        <div class="card-body">
            <div class="search-highlight">
                <p class="mb-2 d-flex align-items-center">
                    <span class="material-icons me-2 text-warning">info</span>
                    <strong>Busque reservas por RUT o correo electrónico</strong>
                </p>
                <p class="mb-0 small">
                    Solo se mostrarán reservas con estado "pendiente" que pueden ser canceladas.
                </p>
            </div>

            <form id="buscarReserva">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label d-flex align-items-center">
                            RUT o correo electrónico:
                        </label>
                        <input type="text" id="buscar" name="buscar" class="form-control" required 
                               placeholder="Ej: 12.345.678-K o correo@gmail.com">
                    </div>
                    <div class="col-12 mb-3">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center w-100">
                            <span class="material-icons me-1">search</span>
                            Buscar reservas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    <div id="listaReservas" class="w-100" style="max-width: 600px;"></div>
</div>

    </div>
<br><br><br><br><br>

        <?php include 'injectable/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
    $(document).ready(function(){
        $('#buscarReserva').on('submit', function(e){
            e.preventDefault();
            let buscar = $('#buscar').val().trim();
            
            if(buscar.length < 3) {
                $('#listaReservas').html(`
                    <div class="alert alert-warning d-flex align-items-center">
                        <span class="material-icons me-2">warning</span>
                        Por favor, ingrese al menos 3 caracteres para buscar.
                    </div>
                `);
                return;
            }

            $('#listaReservas').html(`
                <div class="d-flex justify-content-center my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Buscando...</span>
                    </div>
                    <span class="ms-2">Buscando reservas...</span>
                </div>
            `);

            $.ajax({
                url:'pages/administrador/listar_reservas.php',
                method:'POST',
                data:{buscar:buscar},
                dataType:'json',
                success:function(res){
                    if(res.length===0){
                        $('#listaReservas').html(`
                            <div class="alert alert-info d-flex align-items-center">
                                <span class="material-icons me-2">info</span>
                                No se encontraron reservas activas para el criterio de búsqueda.
                            </div>
                        `);
                        return;
                    }
                    
                    let html = `
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="material-icons me-2">event_available</span>
                                    Reservas encontradas
                                </div>
                                <span class="badge bg-primary">${res.length} reserva(s)</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="d-none d-md-table-header-group">
                                            <tr>
                                                <th><span class="material-icons icon-wrapper">business</span> Cowork</th>
                                                <th><span class="material-icons icon-wrapper">event</span> Fecha</th>
                                                <th><span class="material-icons icon-wrapper">schedule</span> Horario</th>
                                                <th><span class="material-icons icon-wrapper">group</span> Personas</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                    `;
                    
                    res.forEach(r=>{
                        html += `
                            <tr>
                                <td data-label="Cowork">${r.cowork}</td>
                                <td data-label="Fecha">${r.fecha}</td>
                                <td data-label="Horario">
                                    <span class="time-badge">${r.hora_inicio}</span>
                                    <span class="mx-1">-</span>
                                    <span class="time-badge">${r.hora_fin}</span>
                                </td>
                                <td data-label="Personas" class="text-center">${r.cantidad_personas}</td>
                                <td data-label="">
                                    <button class="btn btn-sm btn-danger btn-cancelar d-flex align-items-center" data-id="${r.id}">
                                        <span class="material-icons me-1" style="font-size: 16px;">cancel</span>
                                        Cancelar
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#listaReservas').html(html);
                },
                error: function() {
                    $('#listaReservas').html(`
                        <div class="alert alert-danger d-flex align-items-center">
                            <span class="material-icons me-2">error</span>
                            Error al buscar reservas. Por favor, intente nuevamente.
                        </div>
                    `);
                }
            });
        });

        $(document).on('click','.btn-cancelar', function(){
            if(!confirm('¿Está seguro que desea cancelar esta reserva?')) return;
            
            let id = $(this).data('id');
            let button = $(this);
            
            button.html(`
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                Cancelando...
            `);
            button.prop('disabled', true);
            
            $.post('php/flujo_cowork/cancelar_reservas.php', {reserva_id:id}, function(res){
                if(res.success) {
                    $('#listaReservas').prepend(`
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="material-icons me-2">check_circle</span>
                            <div>${res.msg}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                    
                    $('#buscarReserva').submit();
                } else {
                    alert(res.msg);
                    button.html(`
                        <span class="material-icons me-1" style="font-size: 16px;">cancel</span>
                        Cancelar
                    `);
                    button.prop('disabled', false);
                }
            },'json');
        });
    });
    </script>
</body>
</html>