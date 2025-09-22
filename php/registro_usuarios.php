<?php
require 'db.php'; // conexión $connCasa

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = $connCasa->real_escape_string($_POST['nombre']);
    $apellido   = $connCasa->real_escape_string($_POST['apellido']);
    $correo     = $connCasa->real_escape_string($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $rol        = $connCasa->real_escape_string($_POST['rol']);

    // Validar rol
    if (!in_array($rol, ['admin', 'staff', 'proyecto'])) {
        die("Rol no permitido");
    }

    // Validar si el correo ya existe
    $check = $connCasa->query("SELECT id FROM tbl_usuarios WHERE correo='$correo'");
    if ($check && $check->num_rows > 0) {
        die("El correo ya está registrado.");
    }

    // Encriptar contraseña
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar usuario
    $sql = "INSERT INTO tbl_usuarios (nombre, apellido, correo, contrasena, rol, fecha_creacion) 
            VALUES ('$nombre', '$apellido', '$correo', '$hash', '$rol', NOW())";

    if ($connCasa->query($sql)) {
        echo "✅ Usuario registrado correctamente.";
    } else {
        echo "❌ Error al registrar: " . $connCasa->error;
    }
}
?>
