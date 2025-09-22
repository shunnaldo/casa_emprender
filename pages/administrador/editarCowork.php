<?php
require '../../php/db.php';

if(!isset($_GET['id'])){
    die("ID de cowork no especificado");
}

$id = intval($_GET['id']);

// Traer datos actuales
$result = $connCasa->query("SELECT * FROM tbl_Cowork WHERE id = $id");
if($result->num_rows === 0){
    die("Cowork no encontrado");
}

$cowork = $result->fetch_assoc();

// Procesar formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = $connCasa->real_escape_string($_POST['nombre']);
    $capacidad = intval($_POST['capacidad']);
    $estado = $connCasa->real_escape_string($_POST['estado']);

    $sql = "UPDATE tbl_Cowork 
            SET nombre='$nombre', capacidad=$capacidad, estado='$estado' 
            WHERE id=$id";

    if($connCasa->query($sql)){
        header("Location: indexCowork.php");
        exit;
    } else {
        $error_msg = "Error: " . $connCasa->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cowork</title>
    
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .page-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .main-container {
            flex: 1;
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
        
        .btn-outline-secondary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
        }
        
        .status-badge {
            padding: 0.35em 0.65em;
            border-radius: 50px;
            font-size: 0.75em;
            font-weight: 600;
        }
        
        .status-active {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--success-color);
        }
        
        .status-inactive {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <span class="material-icons me-2" style="font-size: 2rem;">business_center</span>
                <h1 class="h3 mb-0">Editar Cowork</h1>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="container mb-5">
            <!-- Mostrar mensaje de error si existe -->
            <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <span class="material-icons me-2">error</span>
                <div><?= $error_msg ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Card de edición -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="material-icons me-2">edit</span>
                    Editar información del Cowork
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-flex align-items-center">
                                    <span class="material-icons me-1" style="font-size: 1.2rem;">badge</span>
                                    Nombre del Cowork:
                                </label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= htmlspecialchars($cowork['nombre']) ?>" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label d-flex align-items-center">
                                    <span class="material-icons me-1" style="font-size: 1.2rem;">groups</span>
                                    Capacidad:
                                </label>
                                <input type="number" name="capacidad" class="form-control" 
                                       value="<?= $cowork['capacidad'] ?>" min="1" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label d-flex align-items-center">
                                    <span class="material-icons me-1" style="font-size: 1.2rem;">toggle_on</span>
                                    Estado:
                                </label>
                                <select name="estado" class="form-select">
                                    <option value="activo" <?= $cowork['estado']=='activo'?'selected':'' ?>>Activo</option>
                                    <option value="inactivo" <?= $cowork['estado']=='inactivo'?'selected':'' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado actual:</label>
                            <div>
                                <?php 
                                $estadoClass = $cowork['estado'] === 'activo' ? 'status-active' : 'status-inactive';
                                $estadoTexto = $cowork['estado'] === 'activo' ? 'Activo' : 'Inactivo';
                                ?>
                                <span class="status-badge <?= $estadoClass ?>">
                                    <?= $estadoTexto ?>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <span class="material-icons me-1">save</span>
                                Guardar cambios
                            </button>
                            <a href="indexCowork.php" class="btn btn-outline-secondary d-flex align-items-center">
                                <span class="material-icons me-1">arrow_back</span>
                                Volver al listado
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="material-icons me-2">info</span>
                    Información del Cowork
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <?= $cowork['id'] ?></p>
                            <p><strong>Nombre actual:</strong> <?= htmlspecialchars($cowork['nombre']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Capacidad actual:</strong> <?= $cowork['capacidad'] ?> personas</p>
                            <p><strong>Estado actual:</strong> 
                                <span class="status-badge <?= $estadoClass ?>">
                                    <?= $estadoTexto ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-0">Sistema de Gestión de Coworks &copy; <?= date('Y') ?></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>