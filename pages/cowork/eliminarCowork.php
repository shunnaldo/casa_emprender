<?php
require '../../php/db.php';

if(!isset($_GET['id'])){
    die("ID de cowork no especificado");
}

$id = intval($_GET['id']);

// Opcional: comprobar si hay reservas antes de eliminar
$reservas = $connCasa->query("SELECT COUNT(*) as total FROM tbl_Reserva WHERE cowork_id = $id");
$total = $reservas->fetch_assoc()['total'];

if($total > 0){
    die("No se puede eliminar este cowork porque tiene reservas activas.");
}

// Eliminar cowork
if($connCasa->query("DELETE FROM tbl_Cowork WHERE id = $id")){
    header("Location: ../administrador/indexCowork.php");
    exit;
} else {
    echo "Error al eliminar: " . $connCasa->error;
}
?>
