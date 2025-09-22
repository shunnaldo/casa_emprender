<?php
require '../../php/db.php'; 

$buscar = $connCasa->real_escape_string($_POST['buscar'] ?? '');

$res = $connCasa->query("SELECT r.id,r.fecha_hora_inicio,r.fecha_hora_fin,r.cantidad_personas,c.nombre as cowork
                         FROM tbl_Reserva r
                         JOIN tbl_Cowork c ON r.cowork_id=c.id
                         WHERE r.estado IN ('pendiente','lista') 
                         AND (r.rut LIKE '%$buscar%' OR r.correo_vecino LIKE '%$buscar%')
                         ORDER BY r.fecha_hora_inicio ASC");

$result = [];
while($row = $res->fetch_assoc()){
    $result[] = [
        'id'=>$row['id'],
        'cowork'=>$row['cowork'],
        'fecha'=>date('Y-m-d', strtotime($row['fecha_hora_inicio'])),
        'hora_inicio'=>date('H:i', strtotime($row['fecha_hora_inicio'])),
        'hora_fin'=>date('H:i', strtotime($row['fecha_hora_fin'])),
        'cantidad_personas'=>$row['cantidad_personas']
    ];
}

echo json_encode($result);
