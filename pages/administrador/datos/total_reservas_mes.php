<?php
// Obtener reservas agrupadas por mes
$sql = "SELECT MONTH(fecha_creacion) AS mes, COUNT(*) AS total
        FROM tbl_Reserva
        GROUP BY MONTH(fecha_creacion)
        ORDER BY mes ASC";

$result = $connCasa->query($sql);

$meses = [];
$totales = [];

// Traducción meses en español
$nombresMeses = [
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
    5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
    9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
];

while ($row = $result->fetch_assoc()) {
    $meses[] = $nombresMeses[$row['mes']];
    $totales[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservas por Mes</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
  
    .rsv-card {
      background-color: white;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.09);
      padding: 30px;
      width: 100%;
      max-width: 640px;
      transition: transform 0.3s ease;
    }
    
    .rsv-card:hover {
      transform: translateY(-5px);
    }
    
    .rsv-header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eaedf0;
    }
    
    .rsv-title {
      color: #2c3e50;
      font-size: 28px;
      margin: 0 0 10px 0;
      font-weight: 700;
    }
    
    .rsv-subtitle {
      color: #7f8c8d;
      margin: 0;
      font-size: 16px;
      font-weight: 400;
    }
    
    .rsv-chart-wrapper {
      position: relative;
      margin: 0 auto;
      height: 320px;
      width: 100%;
    }
    
    .rsv-stats {
      display: flex;
      justify-content: center;
      margin-top: 25px;
      gap: 25px;
      flex-wrap: wrap;
    }
    
    .rsv-stat {
      text-align: center;
      padding: 15px 20px;
      background-color: #f8fafc;
      border-radius: 12px;
      min-width: 120px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }
    
    .rsv-stat:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
    }
    
    .rsv-value {
      font-size: 26px;
      font-weight: 800;
      color: #3498db;
      margin-bottom: 8px;
    }
    
    .rsv-label {
      font-size: 14px;
      color: #7f8c8d;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    @media (max-width: 600px) {
      .rsv-card {
        padding: 20px;
      }
      
      .rsv-title {
        font-size: 24px;
      }
      
      .rsv-stats {
        gap: 15px;
      }
      
      .rsv-stat {
        min-width: 100px;
        padding: 12px 15px;
      }
      
      .rsv-value {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>

<div class="rsv-container-main">
  <div class="rsv-card">
    <div class="rsv-header">
      <h1 class="rsv-title">Reservas por Mes</h1>
      <p class="rsv-subtitle">Distribución anual de reservas</p>
    </div>
    
    <div class="rsv-chart-wrapper">
      <canvas id="reservasPie"></canvas>
    </div>
    
    <div class="rsv-stats">
      <div class="rsv-stat">
        <div class="rsv-value"><?php echo array_sum($totales); ?></div>
        <div class="rsv-label">Total Reservas</div>
      </div>
      <div class="rsv-stat">
        <div class="rsv-value"><?php echo count($meses); ?></div>
        <div class="rsv-label">Meses Activos</div>
      </div>
      <div class="rsv-stat">
        <div class="rsv-value"><?php echo round(array_sum($totales) / max(1, count($meses))); ?></div>
        <div class="rsv-label">Promedio</div>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('reservasPie').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($meses) ?>,
        datasets: [{
            label: 'Reservas',
            data: <?= json_encode($totales) ?>,
            backgroundColor: [
                '#3498db', '#1abc9c', '#9b59b6', '#f1c40f',
                '#e67e22', '#e74c3c', '#2ecc71', '#16a085',
                '#2980b9', '#8e44ad', '#f39c12', '#d35400'
            ],
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 25,
                    font: {
                        size: 13,
                        family: "'Segoe UI', system-ui, sans-serif"
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.raw || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} reservas (${percentage}%)`;
                    }
                },
                padding: 12,
                backgroundColor: 'rgba(44, 62, 80, 0.9)',
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                }
            }
        }
    }
});
</script>

</body>
</html>