<?php
include 'php/db.php';

$mensaje = "";

// Procesar el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $rut = $connCasa->real_escape_string($_POST['rut']);
    $nombre = $connCasa->real_escape_string($_POST['nombre']);
    $apellido = $connCasa->real_escape_string($_POST['apellido']);
    $correo = $connCasa->real_escape_string($_POST['correo']);
    $fecha_nac = $connCasa->real_escape_string($_POST['fecha_nac']);
    $id_taller = (int)$_POST['id_taller'];

    $sql_insert = "INSERT INTO tbl_Asistencia (rut, nombre, apellido, correo, fecha_nac, id_taller) 
                   VALUES ('$rut', '$nombre', '$apellido', '$correo', '$fecha_nac', $id_taller)";
    if($connCasa->query($sql_insert)){
        $mensaje = "✅ Asistencia registrada correctamente.";
    } else {
        $mensaje = "❌ Error: " . $connCasa->error;
    }
}

// Traer talleres disponibles
$talleres = $connCasa->query("SELECT id, nombre FROM tbl_Taller WHERE activado = 1 ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #000000, #1a1a1a, #2d2d2d);
            background-attachment: fixed;
            min-height: 100vh;
            padding-top: 20px;
        }
        
        .card {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            color: #000;
        }
        
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(0, 0, 0, 0.2);
            color: #000;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.9);
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            color: #000;
        }
        
        .form-label {
            color: #000;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #0a58ca);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #0a58ca, #084298);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .icon-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, #0d6efd, #0a58ca);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .icon-circle i {
            font-size: 2.5rem;
            color: white;
        }
        
        .alert {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            color: #000;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            display: inline-block;
            color: #000;
        }
        
        .navbar {
            background-color: rgba(0, 0, 0, 0.8) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <br><br>
    <?php include 'injectable/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card p-4 p-md-5">

                    
                    <h2 class="text-center mb-4">
                        <i class="bi bi-pencil-square me-2"></i>Registro de Asistencia
                    </h2>

                    <?php if($mensaje): ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle-fill me-2"></i><?= $mensaje ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-person-vcard me-1"></i> RUT
                            </label>
                            <input type="text" id="rut" name="rut" class="form-control" required placeholder="12.345.678-K" maxlength="12">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-envelope me-1"></i> Correo
                            </label>
                            <input type="email" name="correo" class="form-control" required placeholder="ejemplo@correo.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-person me-1"></i> Nombre
                            </label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-person me-1"></i> Apellido
                            </label>
                            <input type="text" name="apellido" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-calendar-event me-1"></i> Fecha de Nacimiento
                            </label>
                            <input type="date" name="fecha_nac" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-mortarboard me-1"></i> Taller
                            </label>
                            <select name="id_taller" class="form-select" required>
                                <option value="">Seleccione un taller</option>
                                <?php while($t = $talleres->fetch_assoc()): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Registrar Asistencia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<br><br><br>
    <?php include 'injectable/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/talleres.js"></script>
</body>
</html>