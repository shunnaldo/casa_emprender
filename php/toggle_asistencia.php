<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'msg' => 'No autorizado']);
    exit;
}

if(!isset($_POST['id'])){
    echo json_encode(['success' => false, 'msg' => 'Falta ID']);
    exit;
}

$id = (int)$_POST['id'];

// Solo cambiar asistencia si está en 0
$sql_check = "SELECT asistencia FROM tbl_Reserva WHERE id = $id LIMIT 1";
$res = $connCasa->query($sql_check);

if($res && $res->num_rows === 1){
    $row = $res->fetch_assoc();
    if($row['asistencia'] == 0){
        $sql_update = "UPDATE tbl_Reserva SET asistencia = 1 WHERE id = $id";
        if($connCasa->query($sql_update)){
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'msg' => 'Error al actualizar asistencia']);
        }
    } else {
        echo json_encode(['success' => false, 'msg' => 'La asistencia ya fue registrada']);
    }
} else {
    echo json_encode(['success' => false, 'msg' => 'Reserva no encontrada']);
}
