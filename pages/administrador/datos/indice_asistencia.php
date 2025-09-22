<?php
// Obtener mes seleccionado (1-12)
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n'); // por defecto el mes actual

// Contar presentes y ausentes filtrando por mes
$sqlAsistencia = "
    SELECT 
        SUM(CASE WHEN asistencia = 1 THEN 1 ELSE 0 END) AS presentes,
        SUM(CASE WHEN asistencia = 0 THEN 1 ELSE 0 END) AS ausentes
    FROM tbl_Reserva
    WHERE MONTH(fecha_creacion) = $mes
";
$resultAsistencia = $connCasa->query($sqlAsistencia);
$data = $resultAsistencia->fetch_assoc();

$presentes = $data['presentes'] ?? 0;
$ausentes = $data['ausentes'] ?? 0;
$total = $presentes + $ausentes;

// Nombres de meses para el select
$meses = [
    1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
    7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
];
?>

<div class="asistencia-container">
    <div class="asistencia-card">

        <div class="asistencia-header">
        <br>
            <h3 class="asistencia-title">Índice de Asistencia</h3>
        </div>

        <div class="asistencia-body">
            <!-- Selector de mes -->
            <form method="GET" class="asistencia-form">
                <select name="mes" class="asistencia-select" onchange="this.form.submit()">
                    <?php foreach($meses as $num => $nombreMes): ?>
                        <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>><?= $nombreMes ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="asistencia-stats">
                <div class="asistencia-total">
                    <span class="asistencia-total-number"><?= $total ?></span>
                    <span class="asistencia-total-label">Total de reservas</span>
                </div>
            </div>

            <div class="asistencia-chart-container">
                <canvas id="asistenciaChart"></canvas>
                <div class="asistencia-chart-overlay">
                    <span class="asistencia-percentage"><?= $total ? number_format(($presentes / $total) * 100, 1) : 0 ?>%</span>
                    <span class="asistencia-percentage-label">Asistencia</span>
                </div>
            </div>

            <div class="asistencia-details">
                <div class="asistencia-detail-item">
                    <span class="asistencia-detail-badge present"></span>
                    <div class="asistencia-detail-info">
                        <span class="asistencia-detail-value"><?= $presentes ?></span>
                        <span class="asistencia-detail-label">Presentes</span>
                    </div>
                </div>
                <div class="asistencia-detail-item">
                    <span class="asistencia-detail-badge absent"></span>
                    <div class="asistencia-detail-info">
                        <span class="asistencia-detail-value"><?= $ausentes ?></span>
                        <span class="asistencia-detail-label">Ausentes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxAsistencia = document.getElementById('asistenciaChart').getContext('2d');
new Chart(ctxAsistencia, {
    type: 'doughnut',
    data: {
        labels: ['Presentes', 'Ausentes'],
        datasets: [{
            data: [<?= $presentes ?>, <?= $ausentes ?>],
            backgroundColor: ['#4CAF50', '#F44336'],
            borderColor: '#ffffff',
            borderWidth: 3,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: { 
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let value = context.raw;
                        let porcentaje = <?= $total ?> ? ((value / <?= $total ?>) * 100).toFixed(1) : 0;
                        return `${context.label}: ${value} (${porcentaje}%)`;
                    }
                },
                padding: 12,
                backgroundColor: 'rgba(33, 33, 33, 0.9)',
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                }
            }
        }
    }
});
</script>

<style>
.asistencia-container {
    font-family: 'Segoe UI', 'Roboto', sans-serif;
    padding: 20px;
    display: flex;
    justify-content: center;
}

.asistencia-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    width: 100%;
    max-width: 400px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.asistencia-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.asistencia-header {
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    color: white;
    padding: 20px;
    text-align: center;
    position: relative;
}

.asistencia-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.asistencia-title {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.asistencia-body {
    padding: 25px;
}

.asistencia-form {
    margin-bottom: 20px;
}

.asistencia-select {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: #f8f9fa;
    font-size: 0.95rem;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
}

.asistencia-select:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
}

.asistencia-stats {
    text-align: center;
    margin-bottom: 25px;
}

.asistencia-total-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: #2196F3;
    margin-bottom: 5px;
}

.asistencia-total-label {
    font-size: 0.9rem;
    color: #757575;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.asistencia-chart-container {
    position: relative;
    height: 200px;
    margin: 20px 0;
}

.asistencia-chart-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.asistencia-percentage {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
}

.asistencia-percentage-label {
    font-size: 0.8rem;
    color: #757575;
}

.asistencia-details {
    display: flex;
    justify-content: space-around;
    margin-top: 20px;
}

.asistencia-detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.asistencia-detail-badge {
    width: 16px;
    height: 16px;
    border-radius: 50%;
}

.asistencia-detail-badge.present {
    background-color: #4CAF50;
}

.asistencia-detail-badge.absent {
    background-color: #F44336;
}

.asistencia-detail-info {
    display: flex;
    flex-direction: column;
}

.asistencia-detail-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.asistencia-detail-label {
    font-size: 0.8rem;
    color: #757575;
}

@media (max-width: 480px) {
    .asistencia-container {
        padding: 10px;
    }
    
    .asistencia-body {
        padding: 20px;
    }
    
    .asistencia-details {
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }
}
</style>