<?php

    // Obtener coworks activos
    $sqlCoworks = "SELECT id, nombre FROM tbl_Cowork WHERE estado = 'activo'";
    $resultCoworks = $connCasa->query($sqlCoworks);

    // Si se selecciona un cowork
    $totalReservas = null;
    if (isset($_GET['cowork_id']) && !empty($_GET['cowork_id'])) {
        $cowork_id = intval($_GET['cowork_id']);
        $sqlReservas = "SELECT COUNT(*) AS total FROM tbl_Reserva WHERE cowork_id = $cowork_id";
        $resultReservas = $connCasa->query($sqlReservas);
        $row = $resultReservas->fetch_assoc();
        $totalReservas = $row['total'];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservas por Cowork</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>

    
    .reservas-container {
      background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      max-width: 800px;
      margin: 0 auto;
      color: white; /* Letras blancas */
    }
    
    .reservas-title {
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 25px;
      border-bottom: 2px solid #ffffff;
      padding-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .reservas-form {
      margin-bottom: 30px;
    }
    
    .reservas-card {
      background-color: #ffffffff; 
      color: #34495e;
      border: none;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .reservas-select {
      border-radius: 8px;
      border: 1px solid #ced4da;
      padding: 12px;
    }
    
    .reservas-label {
      color: #ffffff;
      font-weight: 500;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .icono-principal {
      font-size: 1.8rem;
    }
    
    .icono-card {
      font-size: 2.5rem;
    }
    
    .card-number {
      font-size: 2.2rem;
      font-weight: 700;
    }
    
    .form-control:focus {
      border-color: #86b7fe;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
  </style>
</head>
<body>

  <div class="reservas-container">
    <h4 class="reservas-title">
      <i class="fas fa-building icono-principal"></i> Reservas por Espacio
    </h4>

    <form method="GET" class="reservas-form">
      <label for="cowork_id" class="reservas-label">
        <i class="fas fa-list-alt"></i> Selecciona un cowork:
      </label>
      <select name="cowork_id" id="cowork_id" class="form-select reservas-select" onchange="this.form.submit()">
        <option value="">-- Selecciona --</option>
        <?php while ($cowork = $resultCoworks->fetch_assoc()): ?>
          <option value="<?= $cowork['id'] ?>" 
            <?= isset($_GET['cowork_id']) && $_GET['cowork_id'] == $cowork['id'] ? 'selected' : '' ?>>
            <?= $cowork['nombre'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>

    <?php if ($totalReservas !== null): ?>
      <div class="reservas-card">
        <div>
          <i class="fas fa-calendar-check icono-card"></i>
        </div>
        <div>
          <p class="mb-1">Total de reservas en este cowork:</p>
          <div class="card-number"><?= $totalReservas ?></div>
        </div>
      </div>
    <?php else: ?>
      <div class="reservas-card text-center">
        <div>
          <i class="fas fa-info-circle icono-card"></i>
        </div>
        <div>
          <p class="mb-0">Selecciona un espacio para ver el total de reservas</p>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>