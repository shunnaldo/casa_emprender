<?php
session_start();
include 'db.php'; // $connCasa

// Verificar sesión y rol (opcional)
if(!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin'){
    header("Location: ../pages/administrador/login.php");
    exit;
}

// Verificar que se reciba un ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("ID de usuario no válido.");
}

$id = intval($_GET['id']);

// Evitar que un admin se elimine a sí mismo
if($id === $_SESSION['user_id']){
    die("No puedes eliminar tu propio usuario.");
}

// Eliminar usuario
$sql = "DELETE FROM tbl_usuarios WHERE id = $id";
if($connCasa->query($sql)){
    // Redirigir de vuelta a la lista
    header("Location: ../pages/administrador/usuarios_list.php");
    exit;
} else {
    die("Error al eliminar usuario: " . $connCasa->error);
}
?>
