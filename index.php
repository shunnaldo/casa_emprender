<?php
require 'php/db.php'; 

$coworks = $connCasa->query("SELECT * FROM tbl_Cowork WHERE estado='activo' ORDER BY nombre");
$horas = range(9,18); 

// Verificar si hay mensajes de éxito o error
$mensaje = '';
$tipoMensaje = ''; // 'success' o 'danger'

if(isset($_GET['success'])) {
    $mensaje = $_GET['success'];
    $tipoMensaje = 'success';
} elseif(isset($_GET['error'])) {
    $mensaje = $_GET['error'];
    $tipoMensaje = 'danger';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma de Hora Cowork</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" type="image/png" href="https://fomentolaflorida.cl/sistema_reservas/Sistema_reservas-/img/imagelogonegro.png" sizes="128x128">
</head>
<body>
    <div class="overlay">
        <?php include 'injectable/navbar.php'; ?>
        
       
        
        <div class="container mt-4 mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-md-11">
                    <div class="card">
                        <div class="card-header text-center">
                            <br>
                            <h3 class="mb-0">Reserva de Espacio Cowork</h3>
                            <br>
                        </div>
                        <div class="card-body">
                             <!-- Mostrar mensajes de éxito/error -->
        <?php if(!empty($mensaje)): ?>
        <div class="container mt-4">
            <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php endif; ?>
                            <form id="reservaForm" method="POST" action="php/flujo_cowork/guardar_reserva.php">
                                <!-- Sección Información Personal -->
                                <div class="form-section">
                                    <h5 class="section-title">Información Personal</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rut" class="form-label"><i class="bi bi-person-badge me-1 icon-color"></i> RUT</label>
                                            <input type="text" id="rut" name="rut" required class="form-control" maxlength="12" placeholder="12.345.678-K" value="<?= isset($_POST['rut']) ? htmlspecialchars($_POST['rut']) : '' ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="numero_telefono" class="form-label"><i class="bi bi-telephone me-1 icon-color"></i> Teléfono</label>
                                            <input type="text" name="numero_telefono" class="form-control" placeholder="+56 9 1234 5678" value="<?= isset($_POST['numero_telefono']) ? htmlspecialchars($_POST['numero_telefono']) : '' ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nombre_vecino" class="form-label"><i class="bi bi-person me-1 icon-color"></i> Nombre</label>
                                            <input type="text" id="nombre_vecino" name="nombre_vecino" readonly required class="form-control" value="<?= isset($_POST['nombre_vecino']) ? htmlspecialchars($_POST['nombre_vecino']) : '' ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="apellido_vecino" class="form-label"><i class="bi bi-person me-1 icon-color"></i> Apellido</label>
                                            <input type="text" id="apellido_vecino" name="apellido_vecino" readonly required class="form-control" value="<?= isset($_POST['apellido_vecino']) ? htmlspecialchars($_POST['apellido_vecino']) : '' ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="">
                                        <div class="">
                                            <label for="correo_vecino" class="form-label"><i class="bi bi-envelope me-1 icon-color"></i> Correo Electrónico</label>
                                            <input type="email" name="correo_vecino" required class="form-control" placeholder="ejemplo@correo.com" value="<?= isset($_POST['correo_vecino']) ? htmlspecialchars($_POST['correo_vecino']) : '' ?>">
                                        </div>
                                    </div>

                                </div>

                                <br><br>
                                
                                <!-- Sección Reserva -->
                                <div class="form-section">
                                    <h5 class="section-title">Detalles de la Reserva</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cowork_select" class="form-label"><i class="bi bi-building me-1 icon-color"></i> Espacio Cowork</label>
                                            <select id="cowork_select" name="cowork_id" required class="form-select">
                                                <option value="">Seleccione un espacio</option>
                                                <?php while($c = $coworks->fetch_assoc()): ?>
                                                    <option value="<?= $c['id'] ?>" data-capacidad="<?= $c['capacidad'] ?>" <?= (isset($_POST['cowork_id']) && $_POST['cowork_id'] == $c['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($c['nombre']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label for="cantidad_personas" class="form-label"><i class="bi bi-people me-1 icon-color"></i> Cantidad de personas</label>
                                            <input type="number" id="cantidad_personas" name="cantidad_personas" value="<?= isset($_POST['cantidad_personas']) ? htmlspecialchars($_POST['cantidad_personas']) : '1' ?>" min="1" max="3" class="form-control">
                                            <div id="cupos_disp" class="fw-bold mt-1"></div>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-md-4 mb-3">
                                            <label for="fecha" class="form-label"><i class="bi bi-calendar-event me-1 icon-color"></i> Fecha</label>
                                            <?php
                                                $today = date('Y-m-d');
                                                $maxDate = date('Y-m-d', strtotime('+7 days'));
                                            ?>
                                            <input type="date" id="fecha" name="fecha" required class="form-control"
                                                min="<?= $today ?>" max="<?= $maxDate ?>" value="<?= isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : '' ?>">
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="hora_inicio" class="form-label"><i class="bi bi-clock me-1 icon-color"></i> Hora de inicio</label>
                                            <select id="hora_inicio" name="hora_inicio" required class="form-select">
                                                <option value="">Seleccione hora</option>
                                                <?php foreach($horas as $h): ?>
                                                    <?php if($h == 13) continue; // Bloquear hora 13:00 como inicio ?>
                                                    <option value="<?= $h ?>" <?= (isset($_POST['hora_inicio']) && $_POST['hora_inicio'] == $h) ? 'selected' : '' ?>><?= $h ?>:00</option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted">(Cerrado de 14:00 a 15:00)</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="hora_fin" class="form-label"><i class="bi bi-clock-fill me-1 icon-color"></i> Hora de fin</label>
                                            <select id="hora_fin" name="hora_fin" required class="form-select">
                                                <option value="">Seleccione hora fin</option>
                                                <?php if(isset($_POST['hora_fin'])): ?>
                                                    <option value="<?= $_POST['hora_fin'] ?>" selected><?= $_POST['hora_fin'] ?>:00</option>
                                                <?php endif; ?>
                                            </select>
                                            <small class="text-muted">(máximo 2 horas después de inicio)</small>
                                        </div>
                                    </div>
                                                                     
                                </div>
                                
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn" style="background-color: #6c757d; color: #fff;">Confirmar Reserva</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'injectable/footer.php'; ?>
    
    <!-- Popup para RUT no encontrado -->
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup">
            <div class="popup-header">
                <h3>Información Importante</h3>
                <button class="close-btn" id="closePopup">&times;</button>
            </div>
            <div class="popup-body">
                <p>No tienes tarjeta vecina. ¿Deseas obtener más información?</p>
                <div class="popup-buttons">
                    <button class="popup-btn info" id="moreInfo">Más Información</button>
                    <button class="popup-btn close" id="closeBtn">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/index.js"></script>
    
    <script>
    // Preservar selecciones después de un envío fallido
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(isset($_POST['cowork_id'])): ?>
            document.getElementById('cowork_select').value = "<?= $_POST['cowork_id'] ?>";
        <?php endif; ?>
        
        <?php if(isset($_POST['hora_inicio'])): ?>
            document.getElementById('hora_inicio').value = "<?= $_POST['hora_inicio'] ?>";
            // Disparar evento change para actualizar horas fin
            var event = new Event('change');
            document.getElementById('hora_inicio').dispatchEvent(event);
        <?php endif; ?>
        
        <?php if(isset($_POST['hora_fin'])): ?>
            // Esperar un momento para que se carguen las opciones de hora_fin
            setTimeout(function() {
                document.getElementById('hora_fin').value = "<?= $_POST['hora_fin'] ?>";
            }, 100);
        <?php endif; ?>
        
        <?php if(isset($_POST['cantidad_personas'])): ?>
            document.getElementById('cantidad_personas').value = "<?= $_POST['cantidad_personas'] ?>";
        <?php endif; ?>
        
        <?php if(isset($_POST['fecha'])): ?>
            document.getElementById('fecha').value = "<?= $_POST['fecha'] ?>";
        <?php endif; ?>
    });
    </script>
</body>
</html>