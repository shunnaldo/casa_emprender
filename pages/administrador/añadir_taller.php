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

// Mensajes
$mensaje = '';

// Cambiar estado
if(isset($_GET['cambiar_estado_id'])){
    $id = intval($_GET['cambiar_estado_id']);
    // Traer estado actual
    $res = $connCasa->query("SELECT activado FROM tbl_Taller WHERE id=$id");
    if($res->num_rows){
        $estado = $res->fetch_assoc()['activado'];
        $nuevo_estado = $estado ? 0 : 1;
        $connCasa->query("UPDATE tbl_Taller SET activado=$nuevo_estado WHERE id=$id");
        $mensaje = "Estado del taller actualizado.";
    }
}

// Eliminar taller
if(isset($_GET['eliminar_id'])){
    $id_eliminar = intval($_GET['eliminar_id']);
    $connCasa->query("DELETE FROM tbl_Taller WHERE id=$id_eliminar");
    $mensaje = "Taller eliminado correctamente.";
}

// Agregar taller
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])){
    $nombre_taller = $connCasa->real_escape_string($_POST['nombre']);
    $activado = isset($_POST['activado']) ? 1 : 0;

    $sql_insert = "INSERT INTO tbl_Taller (nombre, activado) VALUES ('$nombre_taller', $activado)";
    if($connCasa->query($sql_insert)){
        $mensaje = "Taller agregado correctamente.";
    } else {
        $mensaje = "Error: " . $connCasa->error;
    }
}

// BUSCAR
$buscar = '';
$where = '';
if(isset($_GET['buscar']) && trim($_GET['buscar']) !== ''){
    $buscar = $connCasa->real_escape_string($_GET['buscar']);
    $where = "WHERE nombre LIKE '%$buscar%'";
}

// PAGINACIÓN
$por_pagina = 6;
$pagina = isset($_GET['pagina']) ? max(1,intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $por_pagina;

// Contar total
$total_result = $connCasa->query("SELECT COUNT(*) as total FROM tbl_Taller $where");
$total_talleres = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_talleres / $por_pagina);

// Traer talleres paginados
$sql_list = "SELECT * FROM tbl_Taller $where ORDER BY fecha_creacion DESC LIMIT $inicio, $por_pagina";
$result = $connCasa->query($sql_list);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Talleres</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-header {
            color: #2c3e50;
            padding: 1.5rem 0;


        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border: none;
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border: none;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
        
        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }
        
        .badge-status {
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-size: 0.75em;
            font-weight: 600;
        }
        
        .badge-active {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--success-color);
        }
        
        .badge-inactive {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }
        
        .badge-search {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--secondary-color);
        }
        
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
        }
        
        .form-control:focus, .form-select:focus, .form-check-input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-link {
            color: var(--primary-color);
        }
        
        .search-highlight {
            background-color: #e9f7fe;
            border-left: 4px solid var(--secondary-color);
        }
        
        .highlight-text {
            background-color: #fff3cd;
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <span class="material-icons me-2" style="font-size: 2rem;">construction</span>
                <h1 class="h3 mb-0">Gestión de Talleres</h1>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Mostrar mensaje -->
        <?php if($mensaje): ?>
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-icons me-2">info</span>
            <div><?= $mensaje ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Card para agregar taller -->
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="material-icons me-2">add</span>
                Agregar Nuevo Taller
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label d-flex align-items-center">
                                <span class="material-icons me-1" style="font-size: 1.2rem;">badge</span>
                                Nombre del Taller:
                            </label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required 
                                   placeholder="Ingrese el nombre del taller">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label d-flex align-items-center">
                                <span class="material-icons me-1" style="font-size: 1.2rem;">toggle_on</span>
                                Estado:
                            </label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="activado" name="activado" checked style="width: 3em; height: 1.5em;">
                                <label class="form-check-label" for="activado">Activado</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <span class="material-icons me-1">add_circle</span>
                        Agregar Taller
                    </button>
                </form>
            </div>
        </div>

        <!-- Card para búsqueda y listado de talleres -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <span class="material-icons me-2">search</span>
                    Buscar y Gestionar Talleres
                </div>
            </div>
            <div class="card-body">
                <!-- Formulario de búsqueda -->
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" 
                               class="form-control" placeholder="Buscar taller por nombre...">
                        <button class="btn btn-primary d-flex align-items-center" type="submit">
                            <span class="material-icons me-1">search</span>
                            Buscar
                        </button>
                        <?php if(!empty($buscar)): ?>
                        <a href="?" class="btn btn-outline-secondary d-flex align-items-center">
                            <span class="material-icons me-1">clear</span>
                            Limpiar
                        </a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Mostrar información de búsqueda -->
                <?php if(!empty($buscar)): ?>
                <div class="alert search-highlight d-flex align-items-center mb-4">
                    <span class="material-icons me-2">search</span>
                    <div>
                        <strong>Búsqueda activa:</strong> Mostrando resultados para "<strong><?= htmlspecialchars($buscar) ?></strong>"
                        <span class="badge-search ms-2"><?= $total_talleres ?> resultado(s)</span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Listado de talleres -->
                <?php if($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><span class="material-icons icon-wrapper">numbers</span> ID</th>
                                <th><span class="material-icons icon-wrapper">badge</span> Nombre</th>
                                <th><span class="material-icons icon-wrapper">event</span> Fecha Creación</th>
                                <th><span class="material-icons icon-wrapper">toggle_on</span> Estado</th>
                                <th><span class="material-icons icon-wrapper">settings</span> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): 
                                $estadoClass = $row['activado'] ? 'badge-active' : 'badge-inactive';
                                $estadoTexto = $row['activado'] ? 'Activo' : 'Inactivo';
                                
                                // Resaltar término de búsqueda en el nombre
                                $nombre_taller = htmlspecialchars($row['nombre']);
                                if (!empty($buscar)) {
                                    $nombre_taller = preg_replace(
                                        "/(" . preg_quote($buscar, '/') . ")/i", 
                                        "<span class='highlight-text'>$1</span>", 
                                        $nombre_taller
                                    );
                                }
                            ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $nombre_taller ?></td>
                                <td><?= date('d/m/Y', strtotime($row['fecha_creacion'])) ?></td>
                                <td>
                                    <span class="badge-status <?= $estadoClass ?>">
                                        <?= $estadoTexto ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="?cambiar_estado_id=<?= $row['id'] ?>&buscar=<?= urlencode($buscar) ?>&pagina=<?= $pagina ?>" 
                                           class="btn btn-sm btn-warning d-flex align-items-center">

                                            <?= $row['activado'] ? 'Desactivar' : 'Activar' ?>
                                        </a>
                                        <a href="?eliminar_id=<?= $row['id'] ?>&buscar=<?= urlencode($buscar) ?>&pagina=<?= $pagina ?>" 
                                           class="btn btn-sm btn-danger d-flex align-items-center" 
                                           onclick="return confirmarEliminar('<?= htmlspecialchars($row['nombre']) ?>')">
                                            Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINADOR -->
                <?php if($total_paginas > 1): ?>
                <nav aria-label="Navegación de talleres" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if($pagina > 1): ?>
                            <li class="page-item">
                                <a class="page-link d-flex align-items-center" href="?pagina=<?= $pagina-1 ?>&buscar=<?= urlencode($buscar) ?>">
                                    <span class="material-icons me-1" style="font-size: 18px;">chevron_left</span>
                                    Anterior
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for($i=1; $i<=$total_paginas; $i++): ?>
                            <li class="page-item <?= $i==$pagina?'active':'' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>&buscar=<?= urlencode($buscar) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if($pagina < $total_paginas): ?>
                            <li class="page-item">
                                <a class="page-link d-flex align-items-center" href="?pagina=<?= $pagina+1 ?>&buscar=<?= urlencode($buscar) ?>">
                                    Siguiente
                                    <span class="material-icons ms-1" style="font-size: 18px;">chevron_right</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                <div class="text-center py-4">
                    <span class="material-icons" style="font-size: 3rem; color: #6c757d;">construction</span>
                    <h5 class="mt-2">No hay talleres registrados</h5>
                    <p class="text-muted"><?= empty($buscar) ? 'Comience agregando un nuevo taller.' : 'No se encontraron resultados para su búsqueda.' ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function confirmarEliminar(nombre){
        return confirm("¿Estás seguro que deseas eliminar el taller '" + nombre + "'?");
    }
    </script>
</body>
</html>