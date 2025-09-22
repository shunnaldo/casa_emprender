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

// Obtener parámetros de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$where_fecha = '';

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio = $connCasa->real_escape_string($fecha_inicio);
    $fecha_fin = $connCasa->real_escape_string($fecha_fin);
    $where_fecha = "WHERE DATE(t.fecha_creacion) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
} elseif (!empty($fecha_inicio)) {
    $fecha_inicio = $connCasa->real_escape_string($fecha_inicio);
    $where_fecha = "WHERE DATE(t.fecha_creacion) >= '$fecha_inicio'";
} elseif (!empty($fecha_fin)) {
    $fecha_fin = $connCasa->real_escape_string($fecha_fin);
    $where_fecha = "WHERE DATE(t.fecha_creacion) <= '$fecha_fin'";
}

// Traer talleres con inscripciones
$sql_talleres = "SELECT t.id, t.nombre, t.fecha_creacion, t.activado, COUNT(a.id) as total_inscritos
                 FROM tbl_Taller t
                 LEFT JOIN tbl_Asistencia a ON t.id = a.id_taller
                 $where_fecha
                 GROUP BY t.id
                 ORDER BY t.fecha_creacion DESC";
$result_talleres = $connCasa->query($sql_talleres);

// Obtener fechas únicas para el filtro
$sql_fechas = "SELECT DISTINCT DATE(fecha_creacion) as fecha FROM tbl_Taller ORDER BY fecha_creacion DESC";
$result_fechas = $connCasa->query($sql_fechas);
$fechas = [];
while ($fila = $result_fechas->fetch_assoc()) {
    $fechas[] = $fila['fecha'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Talleres</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
<link rel="stylesheet" href="../../css/lista_taller.css">
</head>
<body class="system-body">
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="system-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="material-icons me-2" style="font-size: 2rem;">groups</span>
                    <h1 class="h3 mb-0">Listado de Talleres e Inscritos</h1>
                </div>
                <span class="badge bg-white text-primary">
                    <?= $result_talleres->num_rows ?> taller(es)
                </span>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Filtro por rango de fechas -->
        <div class="section-filter">
            <h5 class="d-flex align-items-center mb-3">
                <span class="material-icons me-2">filter_list</span>
                Filtrar por rango de fechas
            </h5>
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="fecha_inicio" class="form-label">Fecha inicio:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= htmlspecialchars($fecha_inicio) ?>">
                </div>
                <div class="col-md-5">
                    <label for="fecha_fin" class="form-label">Fecha fin:</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                           value="<?= htmlspecialchars($fecha_fin) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary d-flex align-items-center flex-grow-1 justify-content-center">
                            <span class="material-icons me-1">search</span>
                            Filtrar
                        </button>
                        <?php if (!empty($fecha_inicio) || !empty($fecha_fin)): ?>
                            <a href="?" class="btn btn-outline-secondary d-flex align-items-center">
                                <span class="material-icons">clear</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($fecha_inicio) || !empty($fecha_fin)): ?>
                <div class="filter-range mt-3">
                    <h6 class="d-flex align-items-center mb-2">
                        <span class="material-icons me-2">date_range</span>
                        Rango de fechas aplicado:
                    </h6>
                    <div class="d-flex align-items-center flex-wrap">
                        <span class="text-filter-active me-2">
                            <?= !empty($fecha_inicio) ? date('d/m/Y', strtotime($fecha_inicio)) : 'Desde inicio' ?>
                        </span>
                        <span class="material-icons mx-2">arrow_forward</span>
                        <span class="text-filter-active">
                            <?= !empty($fecha_fin) ? date('d/m/Y', strtotime($fecha_fin)) : 'Hasta hoy' ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if($result_talleres->num_rows > 0): ?>
                <?php while($taller = $result_talleres->fetch_assoc()): 
                    $estadoClass = $taller['activado'] ? 'badge-status-active' : 'badge-status-inactive';
                    $estadoTexto = $taller['activado'] ? 'Activo' : 'Inactivo';
                ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <!-- Card del taller -->
                        <div class="card-custom card-taller">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($taller['nombre']) ?></h5>
                                    <span class="badge-custom <?= $estadoClass ?>">
                                        <?= $estadoTexto ?>
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">
                                        <span class="material-icons" style="font-size: 1rem;">event</span>
                                        Creado: <?= date('d/m/Y', strtotime($taller['fecha_creacion'])) ?>
                                    </span>
                                    <span class="badge-custom badge-inscritos">
                                        <span class="material-icons" style="font-size: 1rem;">people</span>
                                        <?= $taller['total_inscritos'] ?> inscrito(s)
                                    </span>
                                </div>
                                
                                <button class="btn btn-view-inscritos w-100 d-flex align-items-center justify-content-center collapsed" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#inscritos<?= $taller['id'] ?>"
                                        aria-expanded="false"
                                        aria-controls="inscritos<?= $taller['id'] ?>">
                                    <span class="material-icons me-2 icon-toggle">expand_more</span>
                                    <span class="view-text">Ver inscritos</span>
                                </button>
                            </div>
                        </div>

                        <!-- Lista de inscritos agrupados por día -->
                        <div class="collapse collapse-inscritos" id="inscritos<?= $taller['id'] ?>">
                            <div class="p-3">
                                <?php
                                // Traer inscritos y agrupar por fecha_registro
                                $sql_inscritos = "SELECT * FROM tbl_Asistencia WHERE id_taller = {$taller['id']} ORDER BY fecha_registro DESC";
                                $res_inscritos = $connCasa->query($sql_inscritos);

                                if($res_inscritos->num_rows > 0):
                                    $agrupados = [];
                                    while($p = $res_inscritos->fetch_assoc()){
                                        $fecha = date("d/m/Y", strtotime($p['fecha_registro']));
                                        $agrupados[$fecha][] = $p;
                                    }

                                    foreach($agrupados as $fecha => $personas): ?>
                                        <div class="header-fecha mb-3">
                                            <h6 class="mb-0 d-flex align-items-center">
                                                <span class="material-icons me-2">calendar_today</span>
                                                Inscritos el <?= $fecha ?>
                                                <span class="badge bg-white text-primary ms-2">
                                                    <?= count($personas) ?>
                                                </span>
                                            </h6>
                                        </div>
                                        
                                        <?php foreach($personas as $p): 
                                            $iniciales = substr($p['nombre'], 0, 1) . substr($p['apellido'], 0, 1);
                                        ?>
                                            <div class="card-inscrito p-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar-user me-3">
                                                        <?= strtoupper($iniciales) ?>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?></h6>
                                                        <div class="grid-info">
                                                            <div class="item-info">
                                                                <span class="material-icons" style="font-size: 1rem;">badge</span>
                                                                RUT: <?= htmlspecialchars($p['rut']) ?>
                                                            </div>
                                                            <div class="item-info">
                                                                <span class="material-icons" style="font-size: 1rem;">email</span>
                                                                <?= htmlspecialchars($p['correo']) ?>
                                                            </div>
                                                            <div class="item-info">
                                                                <span class="material-icons" style="font-size: 1rem;">cake</span>
                                                                Nac: <?= $p['fecha_nac'] ?>
                                                            </div>
                                                            <div class="item-info">
                                                                <span class="material-icons" style="font-size: 1rem;">schedule</span>
                                                                <?= date('H:i', strtotime($p['fecha_registro'])) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endforeach;

                                else: ?>
                                    <div class="state-empty">
                                        <span class="material-icons">people_outline</span>
                                        <h6>No hay inscritos</h6>
                                        <p class="small">Nadie se ha inscrito en este taller aún.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="state-empty">
                        <span class="material-icons">construction</span>
                        <h4>
                            <?= (empty($fecha_inicio) && empty($fecha_fin)) ? 'No hay talleres registrados' : 'No hay talleres para el rango seleccionado' ?>
                        </h4>
                        <p>
                            <?= (empty($fecha_inicio) && empty($fecha_fin)) ? 'No se han creado talleres en el sistema.' : 'Intente con otro rango de fechas o quite el filtro.' ?>
                        </p>
                        <?php if (!empty($fecha_inicio) || !empty($fecha_fin)): ?>
                            <a href="?" class="btn btn-primary mt-2">
                                <span class="material-icons me-1">clear</span>
                                Quitar filtro
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.btn-view-inscritos');
        
        // Configurar eventos para los botones de ver/ocultar
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target');
                const targetCollapse = document.querySelector(targetId);
                const isShowing = targetCollapse.classList.contains('show');
                
                // Actualizar texto del botón
                const viewText = this.querySelector('.view-text');
                const toggleIcon = this.querySelector('.icon-toggle');
                
                if (isShowing) {
                    viewText.textContent = 'Ver inscritos';
                    toggleIcon.style.transform = 'rotate(0deg)';
                } else {
                    viewText.textContent = 'Ocultar inscritos';
                    toggleIcon.style.transform = 'rotate(180deg)';
                    
                    // Cerrar otros acordeones abiertos
                    const openCollapses = document.querySelectorAll('.collapse-inscritos.show');
                    openCollapses.forEach(openCollapse => {
                        if (openCollapse !== targetCollapse) {
                            const bsCollapse = new bootstrap.Collapse(openCollapse);
                            bsCollapse.hide();
                            
                            // Actualizar botones de otros acordeones
                            const otherButton = document.querySelector(`[data-bs-target="#${openCollapse.id}"]`);
                            if (otherButton) {
                                otherButton.querySelector('.view-text').textContent = 'Ver inscritos';
                                otherButton.querySelector('.icon-toggle').style.transform = 'rotate(0deg)';
                                otherButton.classList.add('collapsed');
                            }
                        }
                    });
                }
            });
        });
        
        // Cerrar acordeones cuando se hace clic fuera
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.card-taller') && !event.target.closest('.collapse-inscritos')) {
                const openCollapses = document.querySelectorAll('.collapse-inscritos.show');
                openCollapses.forEach(collapse => {
                    const bsCollapse = new bootstrap.Collapse(collapse);
                    bsCollapse.hide();
                    
                    // Actualizar botones
                    const button = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                    if (button) {
                        button.querySelector('.view-text').textContent = 'Ver inscritos';
                        button.querySelector('.icon-toggle').style.transform = 'rotate(0deg)';
                        button.classList.add('collapsed');
                    }
                });
            }
        });

        // Validación de fechas en el formulario
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;
            
            if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                e.preventDefault();
                alert('La fecha de inicio no puede ser mayor que la fecha de fin.');
            }
        });
    });
    </script>
</body>
</html>