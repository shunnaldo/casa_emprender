<?php
// Año seleccionado (por defecto 2025 si es antes del actual)
$anioSeleccionado = isset($_GET['anio']) ? (int)$_GET['anio'] : date("Y");
if ($anioSeleccionado < 2025) {
    $anioSeleccionado = 2025;
}

// Consulta con filtro por año
$sqlHoras = "
    SELECT HOUR(fecha_hora_inicio) AS hora, COUNT(*) AS total
    FROM tbl_Reserva
    WHERE YEAR(fecha_hora_inicio) = $anioSeleccionado
    GROUP BY HOUR(fecha_hora_inicio)
    ORDER BY hora ASC
";
$resultHoras = $connCasa->query($sqlHoras);

// Inicializar arrays de 0 a 23
$labelsHoras = [];
$valuesHoras = [];
for ($h = 0; $h < 24; $h++) {
    $labelsHoras[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    $valuesHoras[$h] = 0;
}

// Rellenar con resultados
while ($row = $resultHoras->fetch_assoc()) {
    $valuesHoras[(int)$row['hora']] = (int)$row['total'];
}
?>

<style>
.horas-card-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}

.horas-card {
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    background: #ffffff;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.horas-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.horas-card-body {
    padding: 2rem;
}

.horas-title {
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    text-align: center;
}

.filter-container-horas {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.year-select-horas {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.6rem 1.2rem;
    font-size: 1rem;
    color: #4a5568;
    background-color: #f7fafc;
    transition: all 0.2s ease;
    cursor: pointer;
    min-width: 120px;
}

.year-select-horas:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.filter-btn-horas {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn-horas:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.chart-title-horas {
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1.5rem;
    text-align: center;
    font-size: 1.2rem;
}

.chart-container-horas {
    position: relative;
    height: 400px;
    width: 100%;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .horas-card-body {
        padding: 1.5rem;
    }
    
    .filter-container-horas {
        flex-direction: column;
    }
    
    .year-select-horas, .filter-btn-horas {
        width: 100%;
    }
}
</style>

<div class="horas-card-container">
    <div class="horas-card">
        <div class="horas-card-body">
            <h3 class="horas-title">Top de horas más usadas (Reservas)</h3>
            
            <!-- Filtro por año -->
            <form method="get" class="filter-container-horas">
                <select name="anio" class="year-select-horas">
                    <?php
                    $anioActual = date("Y");
                    for ($a = $anioActual; $a >= 2025; $a--) {
                        $sel = $a == $anioSeleccionado ? "selected" : "";
                        echo "<option value='$a' $sel>$a</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="filter-btn-horas">Filtrar</button>
            </form>

            <h5 class="chart-title-horas">Reservas por hora en <?php echo $anioSeleccionado; ?></h5>
            <div class="chart-container-horas">
                <canvas id="graficoHoras"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxHoras = document.getElementById('graficoHoras').getContext('2d');

// Crear gradient para las barras
const gradientBars = ctxHoras.createLinearGradient(0, 0, 0, 400);
gradientBars.addColorStop(0, 'rgba(102, 126, 234, 0.8)');
gradientBars.addColorStop(1, 'rgba(118, 75, 162, 0.6)');

// Crear gradient para hover
const gradientHover = ctxHoras.createLinearGradient(0, 0, 0, 400);
gradientHover.addColorStop(0, 'rgba(102, 126, 234, 1)');
gradientHover.addColorStop(1, 'rgba(118, 75, 162, 0.8)');

new Chart(ctxHoras, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labelsHoras); ?>,
        datasets: [{
            label: 'Reservas por hora',
            data: <?php echo json_encode(array_values($valuesHoras)); ?>,
            backgroundColor: gradientBars,
            borderColor: 'rgba(102, 126, 234, 0.3)',
            borderWidth: 1,
            borderRadius: 6,
            hoverBackgroundColor: gradientHover
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
                borderColor: '#667eea',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    title: function(tooltipItems) {
                        return tooltipItems[0].label;
                    },
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
                },
                title: {
                    display: true,
                    text: 'Número de reservas',
                    color: '#4a5568',
                    font: {
                        weight: '600'
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(226, 232, 240, 0.3)'
                },
                ticks: {
                    color: '#718096',
                    maxRotation: 45,
                    minRotation: 45
                },
                title: {
                    display: true,
                    text: 'Horas del día',
                    color: '#4a5568',
                    font: {
                        weight: '600'
                    }
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