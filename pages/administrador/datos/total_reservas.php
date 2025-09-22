<?php

// Total de reservas
$sql = "SELECT COUNT(*) AS total_reservas FROM tbl_Reserva";
$result = $connCasa->query($sql);
$data = $result->fetch_assoc();
$totalReservas = $data['total_reservas'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Reservas</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #e74c3c;
      --light-color: #ecf0f1;
      --success-color: #2ecc71;
    }
    
    .dashboard-card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
      color: white;
      overflow: hidden;
      position: relative;
    }
    
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    }
    
    .dashboard-card::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      transform: rotate(30deg);
    }
    
    .card-icon {
      font-size: 3.5rem;
      opacity: 0.9;
      margin-bottom: 1rem;
      text-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .card-title {
      font-size: 1.1rem;
      font-weight: 400;
      margin-bottom: 0.5rem;
      opacity: 0.9;
    }
    
    .card-value {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .card-subtitle {
      font-size: 0.9rem;
      opacity: 0.8;
      margin-top: 0.5rem;
    }
    
    .stats-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(255,255,255,0.2);
      border-radius: 20px;
      padding: 0.25rem 0.75rem;
      font-size: 0.8rem;
      backdrop-filter: blur(10px);
    }
    
    @media (max-width: 768px) {
      .dashboard-card {
        margin-bottom: 1.5rem;
      }
      
      .card-value {
        font-size: 2rem;
      }
      
      .card-icon {
        font-size: 2.5rem;
      }
    }
  </style>
</head>
<body >

<div class="card text-white bg-primary shadow dashboard-card h-100 w-100">
  <div class="card-body d-flex flex-column justify-content-center align-items-center">
<i class="bi bi-calendar-check card-icon"></i>
    <h3>Total de Reservas</h3>
    <br>
    <h2><?php echo $totalReservas; ?></h2>
  </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>