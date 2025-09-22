<?php
require '../db.php'; // $connTarjeta

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $rut = preg_replace("/[^0-9]/", "", $_POST['rut']); // solo números
    $len = strlen($rut);

    if($len < 5){
        echo json_encode(['encontrado'=>false, 'msg'=>'Ingresa al menos 5 dígitos']);
        exit;
    }

    // Primero intentamos buscar por el RUT completo
    $sql = "SELECT ctrtec_nombre, ctrtec_apepate, ctrtec_rut 
            FROM ctrtecnicos 
            WHERE ctrtec_rut = '$rut'";
    $res = $connTarjeta->query($sql);

    if($res && $res->num_rows == 1){
        $row = $res->fetch_assoc();
        echo json_encode([
            'encontrado'=>true,
            'nombre'=>$row['ctrtec_nombre'],
            'apellido'=>$row['ctrtec_apepate']
        ]);
        exit;
    }

    // Si no se encuentra, hacemos fallback con los primeros 7 dígitos
    $primeros7 = substr($rut, 0, 7);
    $sql2 = "SELECT ctrtec_nombre, ctrtec_apepate, ctrtec_rut 
            FROM ctrtecnicos 
            WHERE ctrtec_rut LIKE '$primeros7%'";

    $res2 = $connTarjeta->query($sql2);

    if($res2 && $res2->num_rows == 1){
        $row = $res2->fetch_assoc();
        echo json_encode([
            'encontrado'=>true,
            'nombre'=>$row['ctrtec_nombre'],
            'apellido'=>$row['ctrtec_apepate']
        ]);
    } elseif($res2 && $res2->num_rows > 1){
        echo json_encode(['encontrado'=>false, 'msg'=>'RUT ambiguo, ingresa más dígitos']);
    } else {
        echo json_encode(['encontrado'=>false, 'msg'=>'No encontrado']);
    }

}
