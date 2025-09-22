<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Responsive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-content {
            min-height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
a:focus {
    outline: none;
}

        .demo-text {
            margin-bottom: 30px;
            color: #333;
        }

        .demo-text h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #212121;
        }

        .demo-text p {
            font-size: 1.2rem;
            max-width: 800px;
            line-height: 1.6;
        }

        /* Estilos del footer responsivo */
        .custom-footer {
            background-color: #212121;
            color: #fff;
            padding: 30px 40px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            box-sizing: border-box;
        }

        .custom-footer-left, .custom-footer-right {
            width: 30%;
            text-align: start;
            padding: 10px 0;
        }

        .custom-footer-center {
            width: 30%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            gap: 6px;
            line-height: 1.5;
            font-size: 16px;
            margin-left: 20px;
        }

        .custom-footer-center h3, .custom-footer-center a {
            color: white;
        }

        .custom-footer-center a {
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .custom-footer-center a:hover {
            color: #FFC239;
        }

        .custom-footer-logo {
            width: 200px;
            max-width: 100%;
            margin-bottom: 15px;
        }

        .custom-footer-logo-2 {
            width: 300px;
            max-width: 100%;
        }

        .social-buttons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-btn {
            font-size: 24px;
            padding: 10px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #333;
        }

        .social-btn:hover {
            transform: scale(1.1);
            background-color: #FFC239;
        }

        .social-btn i {
            color: white;
        }

        /* Estilos para el texto de contacto */
        #contact {
            transition: color 0.3s ease;
            margin-bottom: 10px;
            display: inline-block;
        }

        #contact:hover {
            color: #FFB533;
        }

        /* Media Queries para responsividad */
        @media (max-width: 1200px) {
            .custom-footer {
                padding: 25px 30px;
            }
            
            .custom-footer-logo {
                width: 180px;
            }
            
            .custom-footer-logo-2 {
                width: 250px;
            }
        }

        @media (max-width: 992px) {
            .custom-footer {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 20px;
            }
            
            .custom-footer-left, .custom-footer-center, .custom-footer-right {
                width: 100%;
                text-align: center;
                margin-bottom: 25px;
                margin-left: 0;
            }
            
            .custom-footer-center {
                align-items: center;
            }
            
            .social-buttons {
                justify-content: center;
            }
            
            .custom-footer-logo-2 {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .custom-footer {
                padding: 20px 15px;
            }
            
            .custom-footer-logo {
                width: 150px;
            }
            
            .social-btn {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }
            
            .custom-footer-center a, .custom-footer-right p {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .custom-footer {
                padding: 15px 10px;
            }
            
            .custom-footer-left, .custom-footer-center, .custom-footer-right {
                margin-bottom: 20px;
            }
            
            .social-buttons {
                gap: 10px;
            }
            
            .social-btn {
                width: 40px;
                height: 40px;
                font-size: 18px;
                padding: 8px;
            }
            
            .custom-footer-logo {
                width: 130px;
            }
            
            .custom-footer-logo-2 {
                width: 180px;
            }
            
            h3 {
                font-size: 18px;
            }
        }

        /* Mejoras para visualización de ejemplo */
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .resize-handle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #FFC239;
            color: #212121;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>


    <footer class="custom-footer">
        <div class="custom-footer-left">
            <img src="https://i.imgur.com/uwvn3uw.png" alt="Logo" class="custom-footer-logo">
            <div class="social-buttons">
            <a href="https://www.instagram.com/casaemprenderlf/" target="_blank" class="social-btn instagram-btn" style="outline: none; box-shadow: none; text-decoration: none;">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.facebook.com/FomentolaFlorida" target="_blank" class="social-btn facebook-btn" style="outline: none; box-shadow: none; text-decoration: none;">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://www.fomentolf.cl/index.php" target="_blank" class="social-btn website-btn" style="outline: none; box-shadow: none; text-decoration: none;">
                <i class="fas fa-globe"></i>
            </a>
            </div>
        </div>
        
        <div class="custom-footer-center">
            <h3 id="contact">COFODEP</h3>
            <a href="https://www.fomentolf.cl/quienes-somos.php">Quiénes somos</a>
            <a href="https://www.fomentolf.cl/convenios.php">Convenios</a>
            <a href="https://www.fomentolf.cl/proyectos.php">Proyectos</a>
            <a href="https://www.fomentolf.cl/innovacion.php">Innovación</a>
        </div>
        
        <div class="custom-footer-right">
            <a href="https://www.fomentolf.cl/contactanos.php" style="text-decoration: none; color: white;">
                <h3 id="contact">Contáctanos</h3>
            </a>
            <p>Alonso de Ercilla 1380</p>
            <p>La Florida, Chile</p>
            <p>Parque Balneario</p>
        </div>
        
        <div class="custom-footer-left">
            <img src="https://i.imgur.com/bKhOnNO.png" alt="Logo" class="custom-footer-logo-2">
        </div>
    </footer>



</body>
</html>