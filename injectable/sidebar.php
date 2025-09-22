<?php
// Obtener el nombre del archivo actual
$current_page = basename($_SERVER['PHP_SELF']);

// Definir las páginas y sus títulos
$pages = [
    'dashboard.php' => 'Dashboard',
    'asistencia.php' => 'Reservas',
    'cancelar_reserva.php' => 'Asistencia',
    'añadir_taller.php' => 'Gestionar Clases',
    'lista_taller.php' => 'Ver Lista',
    'usuarios_list.php' => 'Gestion de usuarios'
    
];
 

// Función para verificar si una página está activa
function isActive($page_name, $current_page) {
    return $page_name === $current_page ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo | Casa Emprender - <?php echo $pages[$current_page] ?? 'Dashboard'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="../../css/sidebar.css">
</head>
<body>
    <div class="main-container">
        <!-- Overlay para móviles -->
        <div class="overlay" id="overlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="bi bi-buildings logo-icon"></i>
                    <span class="logo-text">Casa Emprender</span>
                </div>
                <button class="toggle-btn" id="toggle-sidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-title">Principal</div>
                <a href="dashboard.php" class="menu-item <?php echo isActive('dashboard.php', $current_page); ?>">
                    <i class="bi bi-speedometer2 menu-icon"></i>
                    <span class="menu-text">Dashboard</span>
                </a>

                <?php if ($rol === 'admin' || $rol === 'staff'): ?>         
                <div class="menu-title">Casa Emprender</div>


                    <a href="asistencia.php" class="menu-item <?php echo isActive('asistencia.php', $current_page); ?>">
                        <i class="bi bi-people menu-icon"></i>
                        <span class="menu-text">Reservas</span>
                    </a>
                    
                    <a href="cancelar_reserva.php" class="menu-item <?php echo isActive('cancelar_reserva.php', $current_page); ?>">
                        <i class="bi bi-calendar-event menu-icon"></i>
                        <span class="menu-text">Cancelar asistencia</span>
                    </a>
                    
                    <a href="#" class="menu-item" id="menu-espacios">
                        <i class="bi bi-door-open menu-icon"></i>
                        <span class="menu-text">Espacios</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>

                    <?php
                    // Determinar si el submenu debe abrirse
                    $submenu_open = '';
                    if (in_array($current_page, ['salas.php','oficinas.php','areas.php'])) {
                        $submenu_open = 'open';
                    }
                    ?>

                    <div class="submenu <?= $submenu_open ?>" id="submenu-espacios" style="margin-bottom: 1rem;">
                        <a href="indexCowork.php" class="submenu-item">Gestionar coworks</a>
                        <a href="bloqueos_cowork.php" class="submenu-item">Cancelar espacios</a>
                    </div>

                <?php endif; ?>

                
                <div class="menu-title">Proyectos</div>
                <a href="añadir_taller.php" class="menu-item <?php echo isActive('añadir_taller.php', $current_page); ?>">
                    <i class="bi bi-journal-plus menu-icon"></i>
                    <span class="menu-text">Gestionar Clases</span>
                </a>
                
                <a href="lista_taller.php" class="menu-item <?php echo isActive('lista_taller.php', $current_page); ?>">
                    <i class="bi bi-file-text menu-icon"></i>
                    <span class="menu-text">Ver Lista</span>
                </a>
                
                <div class="menu-title">Configuracion</div>


                <?php if ($rol === 'admin'): ?>
                    <a href="usuarios_list.php" class="menu-item <?php echo isActive('usuarios_list.php', $current_page); ?>">
                        <i class="bi bi-credit-card menu-icon"></i>
                        <span class="menu-text">Gestión de usuarios</span>
                    </a>
                <?php endif; ?>

                <a href="../../php/logout.php" class="menu-item">
                    <i class="bi bi-box-arrow-right menu-icon"></i>
                    <span class="menu-text">Cerrar sesion</span>
                </a>
            </div>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($nombre, 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name">
                            <?php echo $nombre . " " . $apellido; ?>
                        </div>
                        <div class="user-role">
                            <?php 
                            if ($rol === 'admin') {
                                echo "Administrador";
                            } elseif ($rol === 'staff') {
                                echo "Staff";
                            } elseif ($rol === 'proyecto') {
                                echo "proyecto";    
                            } else {
                                echo "Usuario";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Contenido Principal -->
        <div class="main-content" id="main-content">
            <div class="topbar">
                <button class="toggle-btn" id="mobile-toggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="search-bar">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Buscar...">
                </div>
                
                <div class="topbar-actions">
                    <a href="#" class="action-item">
                        <i class="bi bi-bell"></i>
                    </a>
                    <a href="../../" class="action-item">
                        <i class="bi bi-house"></i>
                    </a>
                </div>
            </div>
            
          
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleSidebar = document.getElementById('toggle-sidebar');
            const mobileToggle = document.getElementById('mobile-toggle');
            const overlay = document.getElementById('overlay');
            const menuEspacios = document.getElementById('menu-espacios');
            const submenuEspacios = document.getElementById('submenu-espacios');
            
            // Toggle sidebar en desktop
            toggleSidebar.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
            });
            
            // Toggle sidebar en móviles
            mobileToggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-open');
            });
            
            // Cerrar sidebar al hacer clic en el overlay
            overlay.addEventListener('click', function() {
                document.body.classList.remove('sidebar-open');
            });
            
            // Toggle submenús
            menuEspacios.addEventListener('click', function(e) {
                e.preventDefault();
                submenuEspacios.classList.toggle('open');
            });
            
            // Cerrar sidebar al redimensionar (si es necesario)
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    document.body.classList.remove('sidebar-open');
                }
            });
        });
    </script>
</body>
</html>