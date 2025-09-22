<?php
session_start();
include '../../php/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['user_nombre'] ?? 'Usuario';
$apellido = $_SESSION['user_apellido'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'usuario';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $reserva_id = intval($_POST['reserva_id']);
    
    // Verificar que la reserva exista y esté activa
    $res = $connCasa->query("SELECT * FROM tbl_Reserva WHERE id=$reserva_id AND estado IN ('pendiente','lista')");
    if($res->num_rows === 0){
        echo json_encode(['success'=>false,'msg'=>'Reserva no encontrada o ya finalizada.']);
        exit;
    }

    // Cambiar estado a cancelada
    $connCasa->query("UPDATE tbl_Reserva SET estado='cancelada' WHERE id=$reserva_id");
    echo json_encode(['success'=>true,'msg'=>'Reserva cancelada correctamente.']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar Reserva Cowork</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" href="../../css/cancelar_recerva.css">
</head>
<body>
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <h1 class="h3 mb-0">Cancelar Reserva Cowork</h1>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Card de búsqueda -->
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="material-icons me-2">search</span>
                Buscar reservas activas
            </div>
            <div class="card-body">
                <div class="search-highlight">
                    <p class="mb-2 d-flex align-items-center">
                        <span class="material-icons me-2 text-warning">info</span>
                        <strong>Busque reservas por RUT o correo electrónico</strong>
                    </p>
                    <p class="mb-0 small">Solo se mostrarán reservas con estado "pendiente" que pueden ser canceladas.</p>
                </div>

                <form id="buscarReserva">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label d-flex align-items-center">
                                <span class="material-icons me-1" style="font-size: 1.2rem;">badge</span>
                                RUT o correo electrónico:
                            </label>
                            <input type="text" id="buscar" name="buscar" class="form-control" required 
                                   placeholder="Ej: 12.345.678-K o correo@example.com">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <span class="material-icons me-1">search</span>
                                Buscar reservas
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resultados de búsqueda -->
        <div id="listaReservas"></div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
    $(document).ready(function(){
        // Buscar reservas
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

            // Mostrar carga
            $('#listaReservas').html(`
                <div class="d-flex justify-content-center my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Buscando...</span>
                    </div>
                    <span class="ms-2">Buscando reservas...</span>
                </div>
            `);

            $.ajax({
                url:'listar_reservas.php',
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
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><span class="material-icons icon-wrapper">business</span> Cowork</th>
                                                <th><span class="material-icons icon-wrapper">event</span> Fecha</th>
                                                <th><span class="material-icons icon-wrapper">schedule</span> Horario</th>
                                                <th><span class="material-icons icon-wrapper">group</span> Personas</th>
                                                <th><span class="material-icons icon-wrapper">settings</span> Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                    `;
                    
                    res.forEach(r=>{
                        html += `
                            <tr>
                                <td>${r.cowork}</td>
                                <td>${r.fecha}</td>
                                <td>
                                    <span class="time-badge">${r.hora_inicio}</span>
                                    <span class="mx-1">-</span>
                                    <span class="time-badge">${r.hora_fin}</span>
                                </td>
                                <td class="text-center">${r.cantidad_personas}</td>
                                <td>
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

        // Cancelar reserva
        $(document).on('click','.btn-cancelar', function(){
            if(!confirm('¿Está seguro que desea cancelar esta reserva?')) return;
            
            let id = $(this).data('id');
            let button = $(this);
            
            // Cambiar a estado de carga
            button.html(`
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                Cancelando...
            `);
            button.prop('disabled', true);
            
            $.post('../../php/flujo_cowork/cancelar_reservas.php', {reserva_id:id}, function(res){
                if(res.success) {
                    // Mostrar mensaje de éxito
                    $('#listaReservas').prepend(`
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="material-icons me-2">check_circle</span>
                            <div>${res.msg}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                    
                    // Actualizar la lista
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