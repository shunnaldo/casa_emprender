<?php
session_start();
require '../../php/db.php'; // Conexión $connCasa

$error = ''; // Inicializamos variable de error

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Prepared statement para seguridad
    $stmt = $connCasa->prepare("SELECT id, nombre, apellido, correo, contrasena, rol FROM tbl_usuarios WHERE correo=? LIMIT 1");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if(password_verify($contrasena, $user['contrasena'])) {
            // Guardar datos en sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_apellido'] = $user['apellido'];
            $_SESSION['user_correo'] = $user['correo'];
            $_SESSION['user_rol'] = $user['rol'];

            // Redirigir según rol
            switch($user['rol']) {
                case 'admin':
                    header("Location: dashboard.php");
                    break;
                case 'staff':
                    header("Location: dashboard.php");
                    break;
                case 'proyecto':
                    header("Location: dashboard.php"); 
                    break;
                default:
                    session_destroy();
                    $error = "Rol de usuario no válido.";
            }
            exit;
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo | Casa Emprender</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/login.css">
    <!-- Favicon personalizado -->
    <link rel="icon" type="image/png" href="https://fomentolaflorida.cl/sistema_reservas/Sistema_reservas-/img/logoEmprender.png">
</head>
<body>
    <div class="login-container">
        <div class="welcome-section">
            <div class="welcome-content">
                <div class="banner-logo text-center mb-4">
                    <img src="https://fomentolaflorida.cl/sistema_reservas/Sistema_reservas-/img/logoEmprender2.png" 
                         alt="Logo Casa Emprender" 
                         style="width:100%; max-width:500px; height:auto; object-fit:contain; border-radius:12px; padding:16px;">
                </div>
                <p class="welcome-text">Espacio dedicado para la administracion de casa emprender.</p>
                <br><br><br>
                <a href="../../index.php" class="btn btn-secondary mt-3">
                    <i class="bi bi-arrow-left-circle me-2"></i>Volver menu
                </a>
            </div>
        </div>
        
        <div class="form-section">
            <h3 class="form-title">Acceso al Sistema</h3>
            
            <?php if(!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="correo" name="correo" required placeholder="usuario@ejemplo.com">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="contrasena" class="form-label">Contraseña de administración</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required placeholder="Ingrese su contraseña">
                        <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('contrasena');
        const eyeIcon = document.getElementById('eyeIcon');
            togglePassword.addEventListener('click', function () {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>    
</body>
</html>