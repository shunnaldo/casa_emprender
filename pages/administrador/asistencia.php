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

// --- Configurar zona horaria de Santiago en PHP ---
date_default_timezone_set('America/Santiago');
$now = date('Y-m-d H:i:s');

// --- Configurar hora en MySQL usando desplazamiento (-04:00 para Santiago) ---
$connCasa->query("SET time_zone = '-04:00'");

// --- Actualizar estados automáticamente usando la hora de Santiago ---
$sql_update = "
    UPDATE tbl_Reserva
    SET estado = CASE
        -- Si ya está finalizada, no la cambiamos
        WHEN estado = 'finalizada' THEN 'finalizada'
        
        -- Pendiente: la reserva aún no comienza
        WHEN fecha_hora_inicio > '$now' AND estado != 'pendiente' THEN 'pendiente'
        
        -- Lista: está en curso
        WHEN fecha_hora_inicio <= '$now' AND fecha_hora_fin >= '$now' AND estado != 'lista' THEN 'lista'
        
        -- Finalizada: ya terminó
        WHEN fecha_hora_fin < '$now' AND estado != 'finalizada' THEN 'finalizada'
        
        -- Si no cumple nada, queda igual
        ELSE estado
    END
";
$connCasa->query($sql_update);

// --- Filtrado por RUT ---
$rut_filtro = '';
$where_conditions = [];
if(isset($_GET['rut']) && $_GET['rut'] !== ''){
    $rut_filtro = $connCasa->real_escape_string($_GET['rut']);
    $where_conditions[] = "r.rut LIKE '%$rut_filtro%'";
}

// --- Filtrado por Fecha ---
$fecha_filtro = '';
if(isset($_GET['fecha']) && $_GET['fecha'] !== ''){
    $fecha_filtro = $connCasa->real_escape_string($_GET['fecha']);
    $where_conditions[] = "DATE(r.fecha_hora_inicio) = '$fecha_filtro'";
}


// Construir la cláusula WHERE
$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// --- Paginación ---
$por_pagina = 10; // reservas por página
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $por_pagina;

// --- Traer reservas con límite para paginación y filtro ---
$sql = "
    SELECT r.*, c.nombre AS cowork_nombre 
    FROM tbl_Reserva r
    JOIN tbl_Cowork c ON r.cowork_id = c.id
    $where_clause
    ORDER BY 
        CASE 
            WHEN r.estado = 'lista' THEN 1
            WHEN r.estado = 'pendiente' THEN 2
            WHEN r.estado = 'finalizada' THEN 3
            ELSE 4
        END,
        r.fecha_hora_inicio DESC
    LIMIT $offset, $por_pagina
";

$reservas = $connCasa->query($sql);

// --- Calcular total de páginas (respetando filtro) ---
$sql_total = "
    SELECT COUNT(*) as total 
    FROM tbl_Reserva r
    $where_clause
";
$res_total = $connCasa->query($sql_total);
$total_reservas = $res_total->fetch_assoc()['total'];
$total_paginas = ceil($total_reservas / $por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencias Cowork</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
<link rel="stylesheet" href="../../css/asistencia.css">
</head>
<body>
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <span class="material-icons me-2" style="font-size: 2rem;">event_available</span>
                <h1 class="h3 mb-0">Asistencias Cowork</h1>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Card de filtros -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title d-flex align-items-center mb-4">
                    <span class="material-icons me-2">filter_list</span>
                    Filtros de búsqueda
                </h5>
                <form method="get" action="asistencia.php" class="filter-form">
                    <div class="form-group">
                        <label for="rut">Buscar por RUT</label>
                        <input type="text" class="form-control" id="rut" name="rut" 
                               placeholder="Ej: 12345678-9" value="<?= htmlspecialchars($rut_filtro) ?>">
                    </div>
                    
                        <div class="form-group">
                            <label for="fecha">Filtrar por fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" 
                                value="<?= htmlspecialchars($fecha_filtro) ?>" placeholder="Selecciona una fecha">
                        </div>

                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-filter w-100">
                            <span class="material-icons me-1">search</span>
                            Aplicar filtros
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card de resultados -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title d-flex align-items-center">
                        <span class="material-icons me-2">list_alt</span>
                        Lista de reservas
                        <?php if ($fecha_filtro): ?>
                            <span class="badge bg-info ms-2">Fecha: <?= $fecha_filtro ?></span>
                        <?php endif; ?>
                    </h5>
                    <span class="text-muted">Total: <?= $total_reservas ?> reservas</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>RUT</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Cowork</th>
                                <th>Personas</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Estado</th>
                                <th>Asistencia</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($reservas->num_rows > 0): ?>
                                <?php while($row = $reservas->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['rut']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_vecino'] . ' ' . $row['apellido_vecino']) ?></td>
                                    <td><?= htmlspecialchars($row['correo_vecino']) ?></td>
                                    <td><?= htmlspecialchars($row['numero_telefono']) ?></td>
                                    <td><?= htmlspecialchars($row['cowork_nombre']) ?></td>
                                    <td class="text-center"><?= $row['cantidad_personas'] ?></td>
                                    <td><?= date('Y-m-d', strtotime($row['fecha_hora_inicio'])) ?></td>
                                    <td>
                                        <?= date('H:i', strtotime($row['fecha_hora_inicio'])) ?> - 
                                        <?= date('H:i', strtotime($row['fecha_hora_fin'])) ?>
                                    </td>
                                    <td>
                                        <?php
                                            $estado = $row['estado'];
                                            $badgeClass = '';
                                            $estadoTexto = '';
                                            
                                            switch($estado) {
                                                case 'lista':
                                                    $badgeClass = 'status-active';
                                                    $estadoTexto = 'En curso';
                                                    break;
                                                case 'pendiente':
                                                    $badgeClass = 'status-pending';
                                                    $estadoTexto = 'Pendiente';
                                                    break;
                                                case 'finalizada':
                                                    $badgeClass = 'status-finished';
                                                    $estadoTexto = 'Finalizada';
                                                    break;
                                                default:
                                                    $badgeClass = 'status-pending';
                                                    $estadoTexto = ucfirst($estado);
                                            }
                                            echo "<span class='status-badge $badgeClass'>$estadoTexto</span>";
                                        ?>
                                    </td>                
                                    <td><?= $row['asistencia'] ? 'Sí' : 'No' ?></td>
                                    <td>
                                        <?php if($row['asistencia'] == 0): ?>
                                            <button class="btn btn-sm btn-primary toggle-asistencia" data-id="<?= $row['id'] ?>">
                                                <span class="material-icons icon-wrapper">how_to_reg</span>
                                                Asistencia
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-success" disabled>
                                                <span class="material-icons icon-wrapper">check</span>
                                                Asistió
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <span class="material-icons" style="font-size: 3rem; color: #ccc;">search_off</span>
                                        <p class="mt-2 text-muted">No se encontraron reservas con los filtros aplicados</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                <nav aria-label="Paginación" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for($i=1; $i<=$total_paginas; $i++): ?>
                        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $i ?>&rut=<?= urlencode($rut_filtro) ?>&fecha=<?= urlencode($fecha_filtro) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
    $(document).ready(function(){
        $('.toggle-asistencia').click(function(){
            let reservaId = $(this).data('id');
            $.post('../../php/toggle_asistencia.php', {id: reservaId}, function(res){
                if(res.success){
                    // Recargar la página para reflejar el cambio
                    location.reload();
                } else {
                    alert(res.msg || 'Error al actualizar asistencia');
                }
            }, 'json');
        });
        
      
    });
    </script>
</body>
</html>