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

// Traer coworks existentes
$result = $connCasa->query("SELECT * FROM tbl_Cowork ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Coworks</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" href="../../css/coworkindex.css">
</head>
<body>
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <span class="material-icons me-2" style="font-size: 2rem;">business_center</span>
                <h1 class="h3 mb-0">Gestión de Coworks</h1>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Card para agregar cowork -->
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="material-icons me-2">add</span>
                Agregar nuevo Cowork
            </div>
            <div class="card-body">
                <form action="../cowork/guardarCowork.php" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label d-flex align-items-center">
                                <span class="material-icons me-1" style="font-size: 1.2rem;">badge</span>
                                Nombre del Cowork:
                            </label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Sala de Reuniones A" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label d-flex align-items-center">
                                <span class="material-icons me-1" style="font-size: 1.2rem;">groups</span>
                                Capacidad:
                            </label>
                            <input type="number" name="capacidad" class="form-control" value="10" min="1" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label d-flex align-items-center">
                                <span class="material-icons me-1" style="font-size: 1.2rem;">toggle_on</span>
                                Estado:
                            </label>
                            <select name="estado" class="form-select">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <span class="material-icons me-1">save</span>
                                Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card para lista de coworks -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <span class="material-icons me-2">list</span>
                    Lista de Coworks
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><span class="material-icons icon-wrapper">numbers</span> ID</th>
                                <th><span class="material-icons icon-wrapper">badge</span> Nombre</th>
                                <th><span class="material-icons icon-wrapper">groups</span> Capacidad</th>
                                <th><span class="material-icons icon-wrapper">toggle_on</span> Estado</th>
                                <th><span class="material-icons icon-wrapper">settings</span> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): 
                                $estadoClass = $row['estado'] === 'activo' ? 'badge-active' : 'badge-inactive';
                            ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td><?= $row['capacidad'] ?></td>
                                <td>
                                    <span class="badge-status <?= $estadoClass ?>">
                                        <?= ucfirst($row['estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="editarCowork.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-2 d-flex align-items-center">
                                            <span class="material-icons me-1" style="font-size: 16px;">edit</span>
                                            Editar
                                        </a>
                                        <a href="../cowork/eliminarCowork.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger d-flex align-items-center" onclick="return confirm('¿Está seguro de eliminar este cowork?')">
                                            <span class="material-icons me-1" style="font-size: 16px;">delete</span>
                                            Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>