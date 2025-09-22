<?php
require '../db.php'; // $connCasa

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cowork_id = intval($_POST['cowork_id']);
    $fecha = $_POST['fecha'];
    $hora_inicio = intval($_POST['hora_inicio']);
    $hora_fin = intval($_POST['hora_fin']);
    $descripcion = $connCasa->real_escape_string($_POST['descripcion'] ?? 'Bloqueo');

    // Validaciones
    if($hora_inicio >= $hora_fin) die(json_encode(['success'=>false,'msg'=>'Hora inicio debe ser menor que hora fin']));
    if($hora_inicio < 9 || $hora_fin > 18) die(json_encode(['success'=>false,'msg'=>'Horas deben estar entre 9 y 18']));
    
    // Insertar bloqueo
    $sql = "INSERT INTO tbl_Bloqueos (cowork_id, fecha, hora_inicio, hora_fin, descripcion)
            VALUES ($cowork_id, '$fecha', $hora_inicio, $hora_fin, '$descripcion')";
    if($connCasa->query($sql)){
        echo json_encode(['success'=>true,'msg'=>'Bloqueo registrado']);
    } else {
        echo json_encode(['success'=>false,'msg'=>$connCasa->error]);
    }
    exit;
}
?>
