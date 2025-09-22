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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" href="../../css/registro.css">

</head>
<body class="system-body">
    <!-- Sidebar (se inyectará con PHP) -->
    <?php include '../../injectable/sidebar.php'; ?>

    <!-- Header -->
    <div class="system-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <span class="material-icons me-2" style="font-size: 2rem;">person_add</span>
                <h1 class="h3 mb-0">Registro de Usuario</h1>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <!-- Card del formulario -->
                <div class="card card-form">
                    <div class="card-header-custom d-flex align-items-center">
                        <span class="material-icons me-2">how_to_reg</span>
                        Crear Nueva Cuenta de Usuario
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="../../php/registro_usuarios.php">
                            <!-- Nombre -->
                            <div class="mb-4">
                                <label for="nombre" class="form-label-custom">
                                    <span class="material-icons icon-form">badge</span>
                                    Nombre:
                                </label>
                                <input type="text" id="nombre" name="nombre" class="form-control form-control-custom" 
                                       required placeholder="Ingrese el nombre del usuario">
                            </div>
                            
                            <!-- Apellido -->
                            <div class="mb-4">
                                <label for="apellido" class="form-label-custom">
                                    <span class="material-icons icon-form">badge</span>
                                    Apellido:
                                </label>
                                <input type="text" id="apellido" name="apellido" class="form-control form-control-custom" 
                                       required placeholder="Ingrese el apellido del usuario">
                            </div>
                            
                            <!-- Correo -->
                            <div class="mb-4">
                                <label for="correo" class="form-label-custom">
                                    <span class="material-icons icon-form">email</span>
                                    Correo Electrónico:
                                </label>
                                <input type="email" id="correo" name="correo" class="form-control form-control-custom" 
                                       required placeholder="usuario@ejemplo.com">
                            </div>
                            
                            <!-- Contraseña -->
                            <div class="mb-4">
                                <label for="contrasena" class="form-label-custom">
                                    <span class="material-icons icon-form">lock</span>
                                    Contraseña:
                                </label>
                                <div class="password-container">
                                    <input type="password" id="contrasena" name="contrasena" class="form-control form-control-custom" 
                                           required placeholder="Ingrese una contraseña segura">
                                    <span class="material-icons password-toggle" onclick="togglePassword()">
                                        visibility
                                    </span>
                                </div>
                                <div class="form-text">Mínimo 8 caracteres, incluyendo números y letras</div>
                            </div>
                            
                            <!-- Rol -->
                            <div class="mb-4">
                                <label for="rol" class="form-label-custom">
                                    <span class="material-icons icon-form">admin_panel_settings</span>
                                    Rol de Usuario:
                                </label>
                                <select id="rol" name="rol" class="form-select form-select-custom" required>
                                    <option value="">Seleccione un rol...</option>
                                    <option value="admin">
                                        Administrador 
                                    </option>
                                    <option value="staff">
                                        Staff 
                                    </option>
                                    <option value="proyecto">
                                        Proyecto 
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Botón de registro -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-register d-flex align-items-center justify-content-center">
                                    <span class="material-icons me-2">person_add</span>
                                    Registrar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('contrasena');
        const toggleIcon = document.querySelector('.password-toggle');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.textContent = 'visibility_off';
        } else {
            passwordInput.type = 'password';
            toggleIcon.textContent = 'visibility';
        }
    }
    
    // Validación básica del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('contrasena').value;
        const email = document.getElementById('correo').value;
        
        // Validar email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Por favor, ingrese un correo electrónico válido.');
            return;
        }
        
        // Validar contraseña (mínimo 8 caracteres)
        if (password.length < 8) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 8 caracteres.');
            return;
        }
    });
    </script>
</body>
</html>