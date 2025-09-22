<?php
// Obtener años disponibles en la tabla
$sqlAnios = "SELECT DISTINCT YEAR(fecha_creacion) AS anio FROM tbl_Reserva ORDER BY anio DESC";
$resultAnios = $connCasa->query($sqlAnios);

// Año seleccionado (por defecto: actual)
$anioSeleccionado = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

// Traer reservas agrupadas por mes del año seleccionado
$sql = "
    SELECT MONTH(fecha_hora_inicio) AS mes, COUNT(*) AS total
    FROM tbl_Reserva
    WHERE YEAR(fecha_hora_inicio) = $anioSeleccionado
    GROUP BY MONTH(fecha_hora_inicio)
    ORDER BY mes ASC
";

$result = $connCasa->query($sql);

$meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
          7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];

$labels = [];
$values = [];

// Inicializamos todos los meses en 0
foreach ($meses as $num => $nombre) {
    $labels[] = $nombre;
    $values[$num] = 0;
}

// Llenamos con datos
while ($row = $result->fetch_assoc()) {
    $values[intval($row['mes'])] = intval($row['total']);
}
?>

<style>
.dashboard-card-reservas {
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    background: #ffffff;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.dashboard-card-reservas:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
}

.card-body-reservas {
    padding: 1.5rem;
}

.card-title-reservas {
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0;
    font-size: 1.1rem;
}

.filter-container-reservas {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.year-select-reservas {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
    color: #4a5568;
    background-color: #f7fafc;
    transition: all 0.2s ease;
    cursor: pointer;
}

.year-select-reservas:focus {
    outline: none;
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
}

.chart-container-reservas {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>

<div class="dashboard-card-reservas">
    <div class="card-body-reservas">
        <div class="filter-container-reservas">
            <h6 class="card-title-reservas">Aumento Mensual de Reservas</h6>
            <form method="GET" class="d-flex">
                <select name="anio" class="year-select-reservas" onchange="this.form.submit()">
                    <?php 
                    // Reiniciar el puntero del resultado para poder iterar nuevamente
                    $resultAnios->data_seek(0);
                    while($row = $resultAnios->fetch_assoc()): ?>
                        <option value="<?= $row['anio'] ?>" <?= $row['anio'] == $anioSeleccionado ? 'selected' : '' ?>>
                            <?= $row['anio'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
        <div class="chart-container-reservas">
            <canvas id="lineChartReservas"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxLine = document.getElementById('lineChartReservas').getContext('2d');

// Crear gradient para el área del gráfico
const gradient = ctxLine.createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, 'rgba(66, 153, 225, 0.3)');
gradient.addColorStop(1, 'rgba(66, 153, 225, 0.05)');

new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_values($meses)) ?>,
        datasets: [{
            label: 'Reservas <?= $anioSeleccionado ?>',
            data: <?= json_encode(array_values($values)) ?>,
            borderColor: '#4299e1',
            backgroundColor: gradient,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#4299e1',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: '#4299e1',
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                display: false 
            },
            tooltip: {
                backgroundColor: 'rgba(45, 55, 72, 0.95)',
                titleColor: '#f7fafc',
                bodyColor: '#e2e8f0',
                borderColor: '#4299e1',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return ` ${context.parsed.y} reservas`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(226, 232, 240, 0.6)'
                },
                ticks: {
                    color: '#718096',
                    stepSize: 1
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#718096'
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        animations: {
            tension: {
                duration: 1000,
                easing: 'linear'
            }
        }
    }
});
</script>