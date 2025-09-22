<?php
require '../db.php'; // $connCasa y $connTarjeta
require 'correo_confimarcion.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: ../index.php?error=Método no permitido");
    exit();
}

// Campos obligatorios
$campos = ['rut','nombre_vecino','apellido_vecino','correo_vecino','cowork_id','cantidad_personas','fecha','hora_inicio','hora_fin'];
foreach($campos as $campo){
    if(empty($_POST[$campo])){
        header("Location: ../index.php?error=Falta el campo obligatorio: $campo");
        exit();
    }
}

$rut = $connCasa->real_escape_string($_POST['rut']);
$nombre = $connCasa->real_escape_string($_POST['nombre_vecino']);
$apellido = $connCasa->real_escape_string($_POST['apellido_vecino']);
$correo = $connCasa->real_escape_string($_POST['correo_vecino']);
$telefono = $connCasa->real_escape_string($_POST['numero_telefono'] ?? '');
$cowork_id = intval($_POST['cowork_id']);
$cantidad = intval($_POST['cantidad_personas']);
$fecha = $_POST['fecha'];
$hora_inicio = intval($_POST['hora_inicio']);
$hora_fin = intval($_POST['hora_fin']);

// Validar cantidad
if($cantidad < 1 || $cantidad > 5){
    header("Location: ../../index.php?error=Cada reserva puede llevar máximo 4 invitados (5 personas en total).");
    exit();
}

// Validar horas
if($hora_fin <= $hora_inicio || $hora_fin - $hora_inicio > 2){
    header("Location: ../../index.php?error=La reserva puede ser de 1 o 2 horas máximo.");
    exit();
}

// Validar rango horario
if($hora_inicio < 9 || $hora_fin > 18){
    header("Location: ../../index.php?error=La reserva debe estar entre 09:00 y 18:00");
    exit();
}

// Bloqueos por día
$diaSemana = (new DateTime($fecha))->format('w'); // 0=domingo,6=sabado
if($diaSemana == 0 || $diaSemana == 6){
    header("Location: ../../index.php?error=No se permiten reservas fines de semana.");
    exit();
}

// Bloqueo hora de colación 14:00–15:00
if(($hora_inicio < 15 && $hora_fin > 14)){
    header("Location: ../../index.php?error=No se puede reservar durante la hora de colación (14:00–15:00).");
    exit();
}

$inicio = new DateTime("$fecha $hora_inicio:00:00");
$fin = new DateTime("$fecha $hora_fin:00:00");

// Validar reserva única por persona
$sql_exist = "SELECT COUNT(*) as cnt FROM tbl_Reserva 
              WHERE rut='$rut' AND estado IN ('pendiente','lista') 
              AND fecha_hora_fin > NOW()";
$res_exist = $connCasa->query($sql_exist);
if($res_exist->fetch_assoc()['cnt'] > 0){
    header("Location: ../../index.php?error=Ya tienes una reserva activa. No puedes tomar otra hasta finalizarla.");
    exit();
}

// Traer capacidad del cowork
$res_cowork = $connCasa->query("SELECT capacidad, nombre FROM tbl_Cowork WHERE id=$cowork_id");
if($res_cowork->num_rows === 0) {
    header("Location: ../../index.php?error=Cowork no encontrado");
    exit();
}
$cowork_data = $res_cowork->fetch_assoc();
$capacidad_total = $cowork_data['capacidad'];
$cowork_nombre = $cowork_data['nombre'];

// Verificar ocupación
$sql_ocupacion = "SELECT SUM(cantidad_personas) as ocupadas 
                  FROM tbl_Reserva 
                  WHERE cowork_id = $cowork_id
                  AND estado IN ('pendiente','lista')
                  AND fecha_hora_inicio < '{$fin->format('Y-m-d H:i:s')}'
                  AND fecha_hora_fin > '{$inicio->format('Y-m-d H:i:s')}'";
$res = $connCasa->query($sql_ocupacion);
$ocupadas = $res->fetch_assoc()['ocupadas'] ?? 0;

if($ocupadas + $cantidad > $capacidad_total){
    header("Location: ../../index.php?error=No hay espacio suficiente. Cowork lleno o no hay cupos para esa cantidad de personas.");
    exit();
}

// Insertar reserva
$sql_insert = "INSERT INTO tbl_Reserva 
               (rut, nombre_vecino, apellido_vecino, correo_vecino, fecha_hora_inicio, fecha_hora_fin, cowork_id, cantidad_personas, numero_telefono)
               VALUES 
               ('$rut', '$nombre', '$apellido', '$correo', '{$inicio->format('Y-m-d H:i:s')}', '{$fin->format('Y-m-d H:i:s')}', $cowork_id, $cantidad, '$telefono')";

if($connCasa->query($sql_insert)){
    // Enviar correo
    enviarCorreoConfirmacion($correo, $nombre, $apellido, $rut, $cowork_nombre, $fecha, $hora_inicio, $hora_fin);
    header("Location: ../../index.php?success=Reserva registrada correctamente");
    exit();
} else {
    header("Location: ../../index.php?error=Error al registrar la reserva: " . urlencode($connCasa->error));
    exit();
}
?>