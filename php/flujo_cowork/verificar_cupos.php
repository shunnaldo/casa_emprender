<?php
require '../db.php';

$cowork_id = intval($_POST['cowork_id']);
$fecha = $_POST['fecha'];
$hora_inicio = intval($_POST['hora_inicio']);
$hora_fin = intval($_POST['hora_fin']);

// Capacidad total
$res_cowork = $connCasa->query("SELECT capacidad FROM tbl_Cowork WHERE id=$cowork_id");
$capacidad = $res_cowork->fetch_assoc()['capacidad'] ?? 0;

// Ocupación por reservas existentes
$sql = "SELECT SUM(cantidad_personas) as ocupadas FROM tbl_Reserva
        WHERE cowork_id=$cowork_id
        AND estado IN ('pendiente','lista')
        AND fecha_hora_inicio < '$fecha $hora_fin:00:00'
        AND fecha_hora_fin > '$fecha $hora_inicio:00:00'";
$res = $connCasa->query($sql);
$ocupadas = $res->fetch_assoc()['ocupadas'] ?? 0;

// Ocupación por bloqueos
$sqlB = "SELECT SUM(hora_fin-hora_inicio) as bloqueadas FROM tbl_Bloqueos
         WHERE cowork_id=$cowork_id AND fecha='$fecha'
         AND hora_inicio < $hora_fin AND hora_fin > $hora_inicio";
$resB = $connCasa->query($sqlB);
$bloqueadas = $resB->fetch_assoc()['bloqueadas'] ?? 0;

$disponible = $capacidad - $ocupadas - $bloqueadas;
if($disponible<0) $disponible=0;

echo json_encode(['disponible'=>$disponible]);
