<?php
require '../db.php';

$cowork_id = intval($_POST['cowork_id']);
$fecha = $_POST['fecha'];

$res = $connCasa->query("SELECT hora_inicio,hora_fin FROM tbl_Bloqueos 
                         WHERE cowork_id=$cowork_id AND fecha='$fecha'");
$bloqueos = [];
while($row=$res->fetch_assoc()){
    $bloqueos[] = ['hora_inicio'=>intval($row['hora_inicio']),'hora_fin'=>intval($row['hora_fin'])];
}
echo json_encode($bloqueos);
