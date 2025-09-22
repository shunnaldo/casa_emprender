<?php
require '../db.php'; // conexión $connCasa
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'msg' => 'Método no permitido.']);
    exit;
}

$reserva_id = intval($_POST['reserva_id'] ?? 0);

// Verificar el estado actual de la reserva
$stmt = $connCasa->prepare("SELECT estado FROM tbl_Reserva WHERE id=?");
$stmt->bind_param("i", $reserva_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'msg' => 'Reserva no encontrada.']);
    exit;
}

$row = $res->fetch_assoc();
$estado = $row['estado'];

// Solo permitir si está pendiente
if ($estado !== 'pendiente') {
    if ($estado === 'en curso') {
        echo json_encode(['success' => false, 'msg' => 'No se puede cancelar una reserva que está en curso.']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'No se puede cancelar una reserva que está en curso.']);
    }
    exit;
}

// Actualizar a finalizada
$update = $connCasa->prepare("UPDATE tbl_Reserva SET estado='finalizada' WHERE id=?");
$update->bind_param("i", $reserva_id);
$update->execute();

if ($update->affected_rows > 0) {
    echo json_encode(['success' => true, 'msg' => 'Reserva finalizada correctamente.']);
} else {
    echo json_encode(['success' => false, 'msg' => 'Error al finalizar la reserva.']);
}
