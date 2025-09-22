<?php
session_start();
include '../../php/db.php'; // $connCasa

// Verificar sesión y rol (solo admin puede editar)
if(!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin'){
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['user_nombre'] ?? 'Usuario';
$apellido = $_SESSION['user_apellido'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'usuario';

// Traer todos los usuarios
$result = $connCasa->query("SELECT * FROM tbl_usuarios ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
<link rel="stylesheet" href="../../css/usuarios_list.css">
</head>
<body>
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="material-icons me-2" style="font-size: 2rem;">people</span>
                    <h1 class="h3 mb-0">Gestión de Usuarios</h1>
                </div>
                <a href="registro.php" class="btn btn-primary d-flex align-items-center">
                    <span class="material-icons me-1">person_add</span>
                    Añadir Usuario
                </a>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Card para lista de usuarios -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <span class="material-icons me-2">list</span>
                    Usuarios Registrados
                </div>
            </div>
            <div class="card-body">
                <?php if($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><span class="material-icons icon-wrapper">person</span> Usuario</th>
                                <th> Nombre</th>
                                <th> Correo</th>
                                <th> Rol</th>
                                <th> Fecha Creación</th>
                                <th> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
<?php while($row = $result->fetch_assoc()): 
    // Asignar clase según rol
    switch($row['rol']) {
        case 'admin':
            $rolClass = 'badge-admin'; // por ejemplo rojo
            break;
        case 'staff':
            $rolClass = 'badge-staff'; // por ejemplo azul
            break;
        case 'proyecto':
            $rolClass = 'badge-proyecto'; // morado
            break;
        default:
            $rolClass = 'badge-user'; // gris por defecto
    }

    $iniciales = substr($row['nombre'], 0, 1) . substr($row['apellido'], 0, 1);
?>

                            <tr>
                                <td>
                                    <div class="user-avatar">
                                        <?= strtoupper($iniciales) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['nombre']) ?></div>
                                    <div class="text-muted small"><?= htmlspecialchars($row['apellido']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($row['correo']) ?></td>
                                <td>
                                    <span class="badge-status <?= $rolClass ?>">
                                        <?= ucfirst($row['rol']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['fecha_creacion'])) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="editar_usuario.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning d-flex align-items-center">
                                            <span class="material-icons me-1" style="font-size: 16px;">edit</span>
                                            Editar
                                        </a>
                                        <a href="../../php/eliminar_usuario.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger d-flex align-items-center" onclick="return confirm('¿Está seguro de eliminar este usuario?')">
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
                <?php else: ?>
                <div class="text-center py-4">
                    <span class="material-icons" style="font-size: 3rem; color: #6c757d;">people_outline</span>
                    <h5 class="mt-2">No hay usuarios registrados</h5>
                    <p class="text-muted">Comience agregando un nuevo usuario.</p>
                    <a href="registro.php" class="btn btn-primary d-flex align-items-center mx-auto" style="width: fit-content;">
                        <span class="material-icons me-1">person_add</span>
                        Añadir primer usuario
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>