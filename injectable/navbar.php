<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<title>Navbar estilo pill - Scroll horizontal</title>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
     background-image: url("images/image.png"); /* Cambia por tu imagen */
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed; /* opcional: efecto parallax */

  }

  

  /* Contenedor que centra el navbar */
  .navbar-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 8vh; /* 👈 misma altura que tenías */
  }

  .custom-navbar {
    background: #fffffff0;
    border-radius: 50px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    padding: 0 10px;
    max-height: 80px;
    overflow-x: auto; /* permite scroll horizontal */
    overflow-y: hidden;
    white-space: nowrap;
    scrollbar-width: none;
  }

  .custom-navbar::-webkit-scrollbar {
    display: none;
  }

  .custom-navbar-logo {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    padding: 18px 32px;
  }

  .custom-navbar-logo img {
    height: 100%;
    max-height: 135px;
    margin-right: 12px;
  }

  .custom-navbar-link {
    color: #000;
    font-weight: 600;
    text-decoration: none;
    padding: 18px 32px;
    border-right: 1px solid #ddd;
    transition: all 0.3s ease;
    white-space: nowrap;
    font-size: 1.1rem;
  }

  .custom-navbar-link:last-child {
    border-right: none;
  }

  .custom-navbar-link:hover {
    background: #f0f0f0;
  }

  @media (max-width: 768px) {
    .custom-navbar {
      max-height: 70px;
      padding: 10px;
    }
    .custom-navbar-logo img {
      max-height: 100px;
    }
    .custom-navbar-link, .custom-navbar-logo {
      padding: 10px 16px;
      font-size: 1rem;
    }
  }
</style>
</head>
<body>

<!-- Contenedor que centra la navbar arriba -->
<div class="navbar-wrapper">
  <nav class="custom-navbar">
    <a class="custom-navbar-logo">
      <img src="https://fomentolaflorida.cl/sistema_reservas2/Sistema_reservas-/img/Logo%20CE%20negro-02.png" alt="logo">
    </a>
    <a href="index.php" class="custom-navbar-link">Home</a>
    <a href="https://www.fomentolf.cl/casa.php" class="custom-navbar-link">Conócenos</a>
    <a href="pages/administrador/login.php" class="custom-navbar-link">
      <i class="bi bi-person"></i>
    </a>
  </nav>
</div>

</body>
</html>
