<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MAGUILOP</title>

    </head>
    <body class="antialiased">
        
        </body>
            <!doctype html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>The7 Free HTML5 Responsive Template | Template Stock</title>
    </head>  
        <!-- CSS -->
        <link rel="stylesheet" href="/fonts/css/reset.css">
        <link rel="stylesheet" href="/fonts/css/simplegrid.css">
        <link rel="stylesheet" href="/fonts/css/icomoon.css">
        <link rel="stylesheet" href="/fonts/css/lightcase.css">
        <link rel="stylesheet" href="/fonts/js/owl-carousel/owl.carousel.css" />
        <link rel="stylesheet" href="/fonts/js/owl-carousel/owl.theme.css" />
        <link rel="stylesheet" href="/fonts/js/owl-carousel/owl.transitions.css" />  
        <link rel="stylesheet" href="/fonts/style.css">

        <!-- Google Fonts -->
        <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
           <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
           <!--[if lt IE 9]>
             <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
             <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
           <![endif]-->
        <body id="home">
            <!-- Header -->
            <header id="top-header" class="header-home">
    <div class="grid">
        <div class="col-1-1">
            <div class="content">
                <div class="logo-wrap">
                    <a href="#0" class="logo">MAGUILOP</a>
                </div>
                <nav class="navigation">
                    <input type="checkbox" id="nav-button">
                    <label for="nav-button"></label>
                    <ul class="nav-container">
                        <li><a href="#home" class="current">Inicio</a></li>
                        <li><a href="#services">Servicios</a></li>
                        <li><a href="#work">A La Venta</a></li>
                        <li><a href="#blog">Nosotros</a></li>
                        <li><a href="#pricing">Revisiones</a></li>
                        <li>
  <a href="https://wa.me/50495020203?text=Hola%20Maguilop%2C%20quisiera%20agendar%20una%20revisión%20para%20mi%20lavadora." target="_blank">
    Contáctanos
  </a>
</li>


                        @if (Route::has('login'))
                            @auth
                                <li class="custom-section">
                                    <a href="{{ url('/dashboard') }}"
                                        class="inline-block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] rounded-sm">
                                        Dashboard
                                    </a>
                                </li>
                            @else
                                <li class="custom-section">
                                    <a href="{{ route('login') }}"
                                        class="inline-block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm">
                                        Login
                                    </a>
                                </li>

                                @if (Route::has('register'))
                                    <li class="custom-section">
                                        <a href="{{ route('register') }}"
                                            class="inline-block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] rounded-sm">
                                            Register
                                        </a>
                                    </li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

            <!-- End Header -->

            <!-- Parallax Section -->
            <div class="parallax-section ">
                <div class="grid grid-pad">
                    <div class="col-1-1">
                         <div class="content content-header" >
                            <h2>Soluciones Rapidas Y Confiables Para Tu Hogar</h2>
                            <p>Somos la empresa lider en el mantenimiento y reparacion de electrodomesticos mas reconocida en Honduras. 
                              <br>  Visitenos en Barrio Guadalupe Ave. Replica de Chile, Tegucigalpa, Honduras. </p>
                            <a target="_blank" class="btn btn-ghost" href="#">Find Out More</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Parallax Section -->

            <!-- CurveUp -->
            <svg class="curveUpColor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
            </svg>

            <!-- Services Section -->
            <div class="wrap services-wrap" id="services">
                <section class="grid grid-pad services">
                    <h2>Nuestros Servicios</h2>
                    <div class="col-1-4 service-box service-1" >
                        <div class="content">
                            <div class="service-icon">
                            <img src="/fonts/images/aire.jpeg" alt="Aire" style="width: 100%; height: auto; margin-bottom: 10px;">
                            </div>
                                <h3>Aire Acondicionado</h3>
                                <p>¿Tu aire acondicionado dejó de enfriar? ¿Hace ruidos extraños o tiene fugas?
En Maguilop te ofrecemos un servicio rápido, confiable y garantizado.
                                <br></p>
                                <br><br><br><br><a class="btn read-more" href="#0">Ver más</a>
                        </div>
                    </div>
                    <div class="col-1-4 service-box service-2" >
                        <div class="content">
                            <div class="service-icon">
                            <img src="/fonts/images/lavadora.jpeg" alt="Aire" style="width: 190px; height: 190px; margin-bottom: 10px;">
                            </div>
                                <h3>Lavadoras</h3>
                                <p>En Maguilop ofrecemos diagnóstico y reparación rápida para que vuelvas a lavar sin preocupaciones.
                                <br>Trabajamos con todas las marcas y modelos.

✅ Fallas eléctricas y mecánicas
✅ Cambio de piezas y motores
✅ Limpieza de tambor y filtros
✅ Servicio a domicilio.
                                </p>
                                <br><a class="btn read-more" href="#0">Ver más</a>
                        </div>
                    </div>
                    <div class="col-1-4 service-box service-3">
                        <div class="content">
                            <div class="service-icon">
                            <img src="/fonts/images/reparacionhorno.jpeg" alt="Pádel" style="width: 190px; height: 190px; margin-bottom: 10px;">
                            </div>
                                <h3>Reparación de Hornos</h3>
                                <p>En Maguilop entendemos lo importante que es contar con un horno en buen estado para tu cocina. Si tu horno presenta problemas como falta de calor, encendido intermitente o ruidos extraños, nosotros te ayudamos a solucionarlo.</p>
                                <br><a class="btn read-more" href="#0">Ver más</a>
                        </div>
                    </div>
                    <div class="service-box service-4" >
                        <div class="content">
                            <div class="service-icon">
                            <img src="/fonts/images/refrigerador.jpeg" alt="Pádel" style="width: 190px; height: 190px; margin-bottom: 10px;">
                            </div>
                                <h3>Refrigeradores</h3>
                                <p>Maguilop ofrecemos un servicio técnico confiable, rápido y con garantía para que tu equipo vuelva a funcionar como el primer día.</p>
                                <br><a class="btn read-more" href="#0">Ver más</a>
                        </div>
                    </div>
                </section>
            </div>
            <!-- End Services Section -->
            
            <!-- CurveDown -->
            <svg class="curveDownColor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 0 C 50 100 80 100 100 0 Z"></path>
            </svg>

            <!-- Work Section -->
            <!-- Work Section -->
<div class="wrap grey recent-wrap" id="work">
    <section class="grid grid-pad">
        <h2>Ventas de Electrodomésticos</h2>

        <!-- Start of Filter section -->
        <div class="col-1-1 mix">
            <ul class="filters">
                <li class="filter active" data-filter="all">Todas</li>
                <li class="filter" data-filter=".illustration">Aire Acondicionado</li>
                <li class="filter" data-filter=".web-design">Lavadora</li>
                <li class="filter" data-filter=".photography">Hornos</li>
            </ul>
        </div>
        <!-- End of Filter section -->

        <div class="portfolio-items">

            

            <!-- Lavadoras (web-design) -->
            <div class="col-1-3 mix web-design">
                <div class="content">
                    <div class="recent-work">
                        <img src="/fonts/images/work/2-lavadora.jpeg" alt="">
                        <div class="overlay">
                            <span>Lavadora</span>
                            <h2><a class="img-wrap" href="#">Lavadora Automática</a></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-1-3 mix web-design">
                <div class="content">
                    <div class="recent-work">
                        <img src="/fonts/images/work/lavadora.jpeg" alt="">
                        <div class="overlay">
                            <span>Lavadora</span>
                            <h2><a class="img-wrap" href="#">Lavadora Compacta</a></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hornos (photography) -->
            <div class="col-1-3 mix photography">
                <div class="content">
                    <div class="recent-work">
                        <img src="/fonts/images/work/2-horno.jpeg" alt="">
                        <div class="overlay">
                            <span>Horno</span>
                            <h2><a class="img-wrap" href="#">Horno Eléctrico</a></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-1-3 mix photography">
                <div class="content">
                    <div class="recent-work">
                        <img src="/fonts/images/work/reparacionhorno.jpeg" alt="">
                        <div class="overlay">
                            <span>Horno</span>
                            <h2><a class="img-wrap" href="#">Reparación de Horno</a></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opcional: Refrigerador -->
            <div class="col-1-3 mix photography">
                <div class="content">
                    <div class="recent-work">
                        <img src="/fonts/images/work/2-horno.jpeg" alt="">
                        <div class="overlay">
                            <span>Refrigerador</span>
                            <h2><a class="img-wrap" href="#">Refrigerador</a></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opcional: Aire Acondicionado -->
            <div class="col-1-3 mix illustration">
                <div class="content">
                    <div class="recent-work">
                        <img src="/fonts/images/work/aire.jpeg" alt="">
                        <div class="overlay">
                            <span>Aire Acondicionado</span>
                            <h2><a class="img-wrap" href="#">Aire Split</a></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-1-3 mix illustration">
                <div class="content">
                    <div class="recent-work">
                        <img src="/fonts/images/work/2-aire.jpeg" alt="">
                        <div class="overlay">
                            <span>Aire Acondicionado</span>
                            <h2><a class="img-wrap" href="#">Aire Portátil</a></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón -->
            <div class="col-1-1">
                <a class="btn" href="#0">Ver Más</a>
            </div>

        </div>
    </section>
</div>
<!-- End Work Section -->

            <!-- End Work Section -->

            <!-- CurveUp -->
            <svg class="curveUpColor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
            </svg>


             <!-- CurveDown -->
            <svg class="curveDownColor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 0 C 50 100 80 100 100 0 Z"></path>
            </svg>

            <!-- Blog Section -->
          <!-- Sección Nosotros -->
<section id="blog" style="padding: 50px 20px; background-color: #e99547c7; color: #fff;">
    <div style="max-width: 1200px; margin: 0 auto;">

        <header style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 36px; color: #f83b00;">Nosotros</h2>
            <p style="font-size: 18px;">Conoce más sobre Maguilop</p>
        </header>

        <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">

            <!-- Historia -->
            <div>
                <h3 style="color: #f83b00;">Nuestra Historia</h3>
                <p>
                    Maguilop es una empresa líder en la reparación y mantenimiento de electrodomésticos en Honduras,
                    con más de 53 años de experiencia. Fundada por Manuel Aguirre López como Tecni-Servicios, con el
                    tiempo evolucionó hasta convertirse en Maguilop, en honor a su fundador.
                    Hoy, es un referente en Tegucigalpa por su compromiso, calidad y atención a domicilio y en taller.
                    Estamos ubicados en Avenida República de Chile, 11101, Francisco Morazán.
                </p>
            </div>

            <!-- Misión -->
            <div>
                <h3 style="color: #f83b00;">Nuestra Misión</h3>
                <p>
                    Brindar un servicio de reparación personalizado, seguro, confiable y garantizado que supere las
                    expectativas del cliente. Nos enfocamos en la excelencia mediante procesos creativos e innovadores
                    para asegurar la satisfacción y preferencia continua de nuestros clientes.
                </p>
            </div>

            <!-- Visión -->
            <div>
                <h3 style="color: #f83b00;">Nuestra Visión</h3>
                <p>
                    Ser una empresa de vanguardia donde se trabaje en EQUIPO, para lograr las 
                    metas y los objetivos de TODOS, en un ambiente de trabajo favorable, en las 
                    instalaciones adecuadas, un personal de excelencia y donde los clientes Internos y 
                    externos se sientan seguros y confiados en nuestro trabajo. 
                    Ser una empresa iluminada por DIOS, en donde el éxito obtenido, nos beneficia a 
                    todos 
                </p>
            </div>
</p>
        </div>
    </div>
    
</section>

<!-- End Nosotros Section -->


                 
<!-- End Nosotros Section -->


            <!-- CurveUp -->
            <svg class="curveUpColor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
            </svg>
            
            <!-- Pricing Section -->
            <style>
    .revision-section {
        background-image: url('https://i.imgur.com/qNtbZDi.jpg'); /* Puedes cambiar por otra imagen relacionada */
        background-size: cover;
        background-position: center;
        padding: 60px 20px;
        color: #fff;
        text-align: center;
    }

    .revision-section h2 {
        font-size: 36px;
        margin-bottom: 30px;
        font-weight: bold;
    }

    .revision-cards {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;
    }

    .revision-card {
        background: rgba(0, 0, 0, 0.7);
        border-radius: 15px;
        padding: 30px;
        width: 300px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease;
    }

    .revision-card:hover {
        transform: scale(1.05);
    }

    .revision-card h3 {
        color: #ffa500;
        margin-bottom: 15px;
    }

    .revision-card p {
        font-size: 16px;
    }

    .btn-revision {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #ffa500;
        color: #000;
        text-decoration: none;
        border-radius: 10px;
        font-weight: bold;
    }

    .btn-revision:hover {
        background-color: #ff8800;
    }
</style>

<section class="revision-section" id="pricing">
    <h2>Servicios de Revisión Técnica</h2>
    <div class="revision-cards">
        <div class="revision-card">
            <h3>Revisión Básica</h3>
            <p>Diagnóstico inicial para pequeños electrodomésticos. Ideal para problemas simples.</p>
            <a href="#solicitar" class="btn-revision">Solicitar</a>
        </div>
        <div class="revision-card">
            <h3>Revisión Completa</h3>
            <p>Evaluación detallada de fallas, revisión de circuitos, pruebas eléctricas.</p>
            <a href="#solicitar" class="btn-revision">Solicitar</a>
        </div>
        <div class="revision-card">
            <h3>Revisión Premium</h3>
            <p>Incluye diagnóstico profesional + informe técnico + prioridad en reparaciones.</p>
            <a href="#solicitar" class="btn-revision">Solicitar</a>
        </div>
    </div>
</section>

                <!-- End Pricing Section -->

                <!-- CurveDown -->
                <svg class="curveDownColor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 0 C 50 100 80 100 100 0 Z"></path>
                </svg>

                <!-- Parallax Section - Counter -->
                <div class="parallax-section parallax2">                    
                    <div class="wrap">
                        <section class="grid grid-pad callout">
                            <div class="col-1-3">
                                <div class="content" >
                                <div class="info-counter">
                                        <div class="info-counter-row">
                                        </div>
                                        <div class="info-counter-content">
                                            <h5 class="info-counter-number"   style="color: #ff3c00ff;">
                                            <span class="counter">100</span>
                                            <span class="info-counter-units">% T&Uacute;</span>
                                            </h5>
                                        <div class="info-counter-text"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1-3">
                                <div class="content" >
                                <div class="info-counter">
                                        <div class="info-counter-row">
                                        </div>
                                        <div class="info-counter-content">
                                            <h5 class="info-counter-number"   style="color: #ff3c00ff;">
                                            <span class="counter">100</span>
                                            <span class="info-counter-units">% MEJOR</span>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-1-3">
                                <div class="content" >
                                <div class="info-counter">
                                        <div class="info-counter-row">
                                        </div>
                                        <div class="info-counter-content">
                                            <h5 class="info-counter-number"   style="color: #ff3c00ff;">
                                            <span class="counter">100</span>
                                            <span class="info-counter-units">% OPCI&Oacute;N</span>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <!-- End Parallax Section -->

                <!-- CurveUp -->
                <svg class="curveUpColor curveGrey" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
                </svg>

                

                

                
                
                
                <!-- Parallax Section -->
                 <div class="parallax-section parallax2">                    
                    <div class="wrap">
                        <section class="grid grid-pad callout">
                             </div>
                        </section>
                    </div>
                </div>
                <!-- End Parallax Section -->

                <!-- CurveUp -->
                <svg class="curveUpColor curveMapDown" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
                </svg>

                


                <!-- CurveDown -->
<svg class="curveDownColor" xmlns="http://www.w3.org/2000/svg" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none">
    <path d="M0 0 C 50 100 80 100 100 0 Z"></path>
</svg>

<!-- Footer -->
<footer class="wrap" style="background-color: #111; color: white; padding: 40px 0;">
    <div class="grid grid-pad" style="display: flex; flex-wrap: wrap; justify-content: space-around;">

        <!-- Redes Sociales -->
        <div class="col-1-4" style="min-width: 250px; margin-bottom: 30px;">
            <div class="footer-widget">
                <h3 style="color: #ffa500;">Redes Sociales</h3>
                <ul style="list-style: none; padding-left: 0;">
                    <li><a href="#0" style="color: white; text-decoration: none;">📸 Instagram: <strong>@MAGUILOP_HN</strong></a></li>
                    <li><a href="#0" style="color: white; text-decoration: none;">📘 Facebook: <strong>@MAGUILOP_HN</strong></a></li>
                    <li><a href="#0" style="color: white; text-decoration: none;">🎵 TikTok: <strong>@MAGUILOP_HN</strong></a></li>
                </ul>
            </div>
        </div>

        <!-- Más Información -->
        <div class="col-1-4" style="min-width: 250px; margin-bottom: 30px;">
            <div class="footer-widget">
                <h3 style="color: #ffa500;">Más Información</h3>
                <p>Visita nuestras plataformas web para conocer todos los servicios que ofrecemos.</p>
                <p>También puedes seguirnos en nuestras redes para promociones exclusivas.</p>
            </div>
        </div>

        <!-- Contacto WhatsApp (opcional) -->
        <div class="col-1-4" style="min-width: 250px; margin-bottom: 30px;">
            <div class="footer-widget">
                <h3 style="color: #ffa500;">Contáctanos</h3>
                <p>¿Tienes dudas? ¡Escríbenos por WhatsApp!</p>
                <a href="https://wa.me/50495020203?text=Hola%20Maguilop%2C%20quisiera%20más%20información." 
                   target="_blank" 
                   style="display: inline-block; margin-top: 10px; background-color: #25D366; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                    📱 Chatear por WhatsApp
                </a>
            </div>
        </div>

    </div>

    <hr style="border-color: #444;">
    <p class="source-org copyright" style="text-align: center; color: #aaa;">
        © 2025 | Todos los derechos reservados - Maguilop
    </p>
</footer>

<!-- Loader (opcional) -->
<div class="loader-overlay">
    <div class="loader">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
</div>

                <!-- JS -->
                <script src="/fonts/js/jquery.js"></script>
                <script src="/fonts/js/main.js"></script>
                <script src="/fonts/js/mixitup.js"></script>
                <script src="/fonts/js/smoothscroll.js"></script>
                <script src="/fonts/js/jquery.nav.js"></script>
                <script src="/fonts/js/owl-carousel/owl.carousel.min.js"></script>
                <script src="https://maps.googleapis.com/maps/api/js"></script>
                <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
                <script src="/fonts/js/jquery.counterup.min.js"></script>
                <script src="/fonts/js/lightcase.min.js"></script>
           
</html>