<?php
session_start();

include '../../php/db.php';
// Si no está logueado, mándalo al login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['user_nombre'] ?? 'Usuario';
$apellido = $_SESSION['user_apellido'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'usuario';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link rel="icon" type="image/png" href="https://fomentolaflorida.cl/sistema_reservas/Sistema_reservas-/img/imagelogonegro.png" sizes="128x128">

        <style>
        .dashboard-header {
        display: flex;
        justify-content: center;
        }

        .dashboard-title {
        font-size: 2.4rem;
        font-weight: 700;
        color: #2d3748;
        font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .dashboard-icon {
        width: 75px;   /* 🔥 antes 55px, ahora más grande */
        height: 75px;
        }

        .dashboard-header .d-inline-flex {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-header .d-inline-flex:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }
        </style>

</head>
<body>
    
    <?php include '../../injectable/sidebar.php'; ?>


    <div class="dashboard-header text-center my-4">
    <div class="d-inline-flex align-items-center px-5 py-4 rounded shadow-sm bg-white">
        <img src="https://fomentolaflorida.cl/sistema_reservas/Sistema_reservas-/img/imagelogonegro.png" 
            alt="Icono Dashboard" 
            class="dashboard-icon me-3">
        <h1 class="dashboard-title mb-0">Dashboard Casa Emprender</h1>
    </div>
    </div>

    <div class="container mt-4">
    <div class="row g-3">
        
        <div class="col-3 ">
        <?php include 'datos/total_reservas.php'; ?>
        </div>

        <div class="col-3 ">
        <?php include 'datos/total_espacios.php'; ?>
        </div>

        

    </div>
    </div>


    <div class="row g-3 mt-3">
        <div class="col-12 col-md-6">
        <?php include 'datos/total_reservas_mes.php'; ?>
        </div>
        <div class="col-12 col-md-6">
        <?php include 'datos/aumento_mensual.php'; ?>
        </div>
        <div class="col-12 col-md-6">
        <?php include 'datos/top_horas.php'; ?>
        </div>
        <div class="col-12 col-md-6">
        <?php include 'datos/indice_asistencia.php'; ?>
        </div>

    </div>

</body>
</html>