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

// Manejar eliminación de bloqueo
if(isset($_GET['eliminar_id'])){
    $id = intval($_GET['eliminar_id']);
    $connCasa->query("DELETE FROM tbl_Bloqueos WHERE id=$id");
    header("Location: bloqueos_cowork.php");
    exit;
}

// Manejar agregado de bloqueo
$msg = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cowork_id'])){
    $cowork_id = intval($_POST['cowork_id']);
    $fecha = $_POST['fecha'];
    $hora_inicio = intval($_POST['hora_inicio']);
    $hora_fin = intval($_POST['hora_fin']);
    $descripcion = $connCasa->real_escape_string($_POST['descripcion']);

    if($hora_fin <= $hora_inicio){
        $msg = "La hora fin debe ser mayor que la hora inicio.";
    } else {
        if($cowork_id === 0){
            // Bloquear todos los coworks
            $todosCoworks = $connCasa->query("SELECT id FROM tbl_Cowork WHERE estado='activo'");
            while($c = $todosCoworks->fetch_assoc()){
                $idCowork = intval($c['id']);
                $connCasa->query("INSERT INTO tbl_Bloqueos (cowork_id, fecha, hora_inicio, hora_fin, descripcion)
                                  VALUES ($idCowork, '$fecha', $hora_inicio, $hora_fin, '$descripcion')");
            }
            $msg = "Bloqueo agregado correctamente para todos los coworks.";
        } else {
            // Bloquear solo el cowork seleccionado
            $connCasa->query("INSERT INTO tbl_Bloqueos (cowork_id, fecha, hora_inicio, hora_fin, descripcion)
                              VALUES ($cowork_id, '$fecha', $hora_inicio, $hora_fin, '$descripcion')");
            $msg = "Bloqueo agregado correctamente.";
        }
    }
}

// Traer bloqueos existentes
$bloqueos = $connCasa->query("
    SELECT b.*, c.nombre AS cowork_nombre 
    FROM tbl_Bloqueos b
    JOIN tbl_Cowork c ON c.id = b.cowork_id
    ORDER BY fecha DESC, hora_inicio ASC
");

// Traer coworks activos
$coworks = $connCasa->query("SELECT * FROM tbl_Cowork WHERE estado='activo' ORDER BY nombre");
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloqueos Cowork</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" href="../../css/bloqueos_cowork.css">
</head>
<body>
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <h1 class="h3 mb-0">Administrar Bloqueos Cowork</h1>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <?php if($msg): ?>
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-icons me-2">info</span>
            <div><?= $msg ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Card para agregar bloqueos -->
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="material-icons me-2">add_circle</span>
                Agregar nuevo bloqueo
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-flex align-items-center">
                                Cowork:
                            </label>
                            <select name="cowork_id" class="form-select" required>
                                <option value="">Seleccione Cowork</option>
                                <?php while($c = $coworks->fetch_assoc()): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                                <?php endwhile; ?>
                                 <option value="0">Todos</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-flex align-items-center">
                                Fecha:
                            </label>
                            <input type="date" name="fecha" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-flex align-items-center">
                                Hora inicio:
                            </label>
                            <select name="hora_inicio" class="form-select" required>
                                <?php for($h=9;$h<=17;$h++): ?>
                                    <option value="<?= $h ?>"><?= $h ?>:00</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-flex align-items-center">
                                Hora fin:
                            </label>
                            <select name="hora_fin" class="form-select" required>
                                <?php for($h=10;$h<=18;$h++): ?>
                                    <option value="<?= $h ?>"><?= $h ?>:00</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-flex align-items-center">
                            Descripción:
                        </label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Motivo del bloqueo">
                    </div>

                    <button type="submit" class="btn btn-danger d-flex align-items-center">
                        Bloquear horas
                    </button>
                </form>
            </div>
        </div>

        <!-- Card para bloqueos existentes -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <span class="material-icons me-2">list</span>
                    Bloqueos existentes
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cowork</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($b = $bloqueos->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['cowork_nombre']) ?></td>
                                <td><?= $b['fecha'] ?></td>
                                <td>
                                    <span class="time-badge"><?= $b['hora_inicio'] ?>:00</span>
                                    <span class="mx-1">-</span>
                                    <span class="time-badge"><?= $b['hora_fin'] ?>:00</span>
                                </td>
                                <td><?= htmlspecialchars($b['descripcion']) ?></td>
                                <td>
                                    <a href="bloqueos_cowork.php?eliminar_id=<?= $b['id'] ?>" class="btn btn-sm btn-warning d-flex align-items-center" onclick="return confirm('¿Está seguro de eliminar este bloqueo?')">
                                        <span class="material-icons me-1" style="font-size: 16px;">delete</span>
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</body>
</html>
