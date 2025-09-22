<?php
session_start();
include '../../php/db.php'; // $connCasa

// Verificar sesión y rol (solo admin puede editar)
if(!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin'){
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['user_nombre'] ?? 'Usuario';
$apellido = $_SESSION['user_apellido'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'usuario';

// Verificar que se reciba un ID válido
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("ID de usuario no válido.");
}

$id = intval($_GET['id']);

// Obtener datos actuales del usuario
$res = $connCasa->query("SELECT * FROM tbl_usuarios WHERE id=$id");
if($res->num_rows === 0){
    die("Usuario no encontrado.");
}

$usuario = $res->fetch_assoc();

// Procesar el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = $connCasa->real_escape_string($_POST['nombre']);
    $apellido = $connCasa->real_escape_string($_POST['apellido']);
    $correo = $connCasa->real_escape_string($_POST['correo']);
    $rol = $connCasa->real_escape_string($_POST['rol']);
    $contrasena = $_POST['contrasena'] ?? '';

    if(!in_array($rol, ['admin','staff'])){
        die("Rol no permitido.");
    }

    $pass_sql = '';
    if(!empty($contrasena)){
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $pass_sql = ", contrasena='$hash'";
    }

    $sql = "UPDATE tbl_usuarios 
            SET nombre='$nombre', apellido='$apellido', correo='$correo', rol='$rol' $pass_sql
            WHERE id=$id";

    if($connCasa->query($sql)){
        header("Location: usuarios_list.php");
        exit;
    } else {
        $error = "Error al actualizar: " . $connCasa->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Usuario</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body >
    <?php include '../../injectable/sidebar.php'; ?>

<h2>Editar Usuario</h2>



<?php if(!empty($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="apellido" class="form-label">Apellido:</label>
        <input type="text" id="apellido" name="apellido" class="form-control" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="correo" class="form-label">Correo:</label>
        <input type="email" id="correo" name="correo" class="form-control" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="rol" class="form-label">Rol:</label>
        <select id="rol" name="rol" class="form-select" required>
            <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            <option value="staff" <?= $usuario['rol'] === 'staff' ? 'selected' : '' ?>>Staff</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="contrasena" class="form-label">Contraseña (dejar vacío para no cambiar):</label>
        <input type="password" id="contrasena" name="contrasena" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="usuarios_list.php" class="btn btn-secondary">Cancelar</a>
</form>

</body>
</html>
