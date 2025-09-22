<?php
$host = '15.235.114.116';
$usuario = 'fomentol_practica';
$contrasena = 'CASAEMPRENDER2025**';

// Conexión a Casa Emprender
$connCasa = new mysqli($host, $usuario, $contrasena, 'fomentol_Casa_emprender');
if ($connCasa->connect_error) {
    die("Conexión Casa Emprender fallida: " . $connCasa->connect_error);
}
$connCasa->set_charset("utf8mb4");

// Conexión a Tarjeta Vecino
$connTarjeta = new mysqli($host, $usuario, $contrasena, 'fomentol_tarjetavecino');
if ($connTarjeta->connect_error) {
    die("Conexión Tarjeta Vecino fallida: " . $connTarjeta->connect_error);
}
$connTarjeta->set_charset("utf8mb4");

// Función opcional para cerrar conexiones al final
function cerrarConexiones() {
    global $connCasa, $connTarjeta;
    if ($connCasa) $connCasa->close();
    if ($connTarjeta) $connTarjeta->close();
}
?>
