<?php
require '../../php/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = $connCasa->real_escape_string($_POST['nombre']);
    $capacidad = intval($_POST['capacidad']);
    $estado = $connCasa->real_escape_string($_POST['estado']);

    $sql = "INSERT INTO tbl_Cowork (nombre, capacidad, estado) 
            VALUES ('$nombre', $capacidad, '$estado')";

    if($connCasa->query($sql)){
        header("Location: ../administrador/indexCowork.php");
        exit;
    } else {
        echo "Error: " . $connCasa->error;
    }
}
