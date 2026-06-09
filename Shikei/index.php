<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHIKEY - Restaurante</title>
    <link rel="stylesheet" href="../SHIKEI/css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="img/logo.png" alt="SHIKEY Logo" class="logo">
            </div>
            <h1>SHIKEY</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="calificar_pedido.php">Calificar Pedido</a></li>
                <li><a href="login.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h2>BIENVENIDO A SHIKEY</h2>
            <p>Sabor casero. Sabor Shikei.</p>
            <a href="menu.php" class="btn-primary">Ver Menu y Pedir</a>
        </div>
    </section>

    <section class="acerca-de-nosotros">
        <div class="container">
            <h2>ACERCA DE NOSOTROS</h2>
            <div class="acerca-content">
                <p>SHIKEY es una empresa familiar que inició como un emprendimiento en piedecuesta desde el año 2023. Ofrecemos diversos productos, el más característico son las alas picantes con variedad de salsas al estilo local, el cual mantiene su receta original y presentaciones grandiosas que hacen agua la boca y con los más altos estándares de calidad.</p>
                <p>Actualmente poseemos 1 puntos de venta en donde puedes disfrutar de un ambiente familiar diseñado para compartir en familia. Contamos con un portafolio de productos apanados inspirados en diversos gustos y necesidades de nuestros clientes, es por eso que además de las alas picantes con salsa al gusto, puedes encontrar diversidad en productos apanados como hamburguesas de pollo con milanesa de pollo, burritos con deditos de pollo y muchas más.</p>
            </div>
        </div>
    </section>

    <section class="contactenos">
        <div class="container">
            <h2>CONTACTENOS</h2>
            <p class="subtitulo-contacto">¡Vamos a conocernos!</p>
            
            <div class="contacto-grid">
                <div class="mapa-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.1234567890123!2d-73.05012345678901!3d7.001234567890123!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMDAnMDQuNCJOIDczwrAwMycwMC40Ilc!5e0!3m2!1ses!2sco!4v1234567890123!5m2!1ses!2sco" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <a href="https://maps.google.com/?q=Paseo+del+Puente+Piedecuesta" target="_blank" class="btn-abrir-maps">Abrir en Maps</a>
                </div>
                
                <div class="info-contacto">
                    <div class="info-item">
                        <span class="icono">📍</span>
                        <div>
                            <strong>Dirección</strong>
                            <p>CARR 2A #22-49 PASEO DEL PUENTE DOS<br>Piedecuesta, Santander</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <span class="icono"></span>
                        <div>
                            <strong>Horarios</strong>
                            <p>Lunes - Sábado: 05:00 PM - 11:00 PM<br>Domingo: 04:00 PM - 10:00 PM</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <span class="icono">📱</span>
                        <div>
                            <strong>Teléfonos</strong>
                            <p>314 379 2268<br>317 263 3304</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <span class="icono">✉️</span>
                        <div>
                            <strong>Correo Electrónico</strong>
                            <p>contacto@shikey.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="testimonios-destacados">
        <div class="container">
            <h2>Lo que dicen nuestros clientes</h2>
            <p class="subtitulo">Experiencias reales de quienes nos han visitado</p>
            
            <?php
            $stmt_testimonios = $pdo->query("SELECT * FROM testimonios WHERE aprobado = 1 ORDER BY fecha DESC LIMIT 3");
            $testimonios_destacados = $stmt_testimonios->fetchAll(PDO::FETCH_ASSOC);
            ?>
            
            <?php if (!empty($testimonios_destacados)): ?>
            <div class="testimonios-grid-index">
                <?php foreach ($testimonios_destacados as $testimonio): ?>
                <div class="testimonio-card-index">
                    <div class="estrellas-display-index">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="<?php echo $i <= $testimonio['calificacion'] ? 'estrella-llena' : 'estrella-vacia'; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <?php if ($testimonio['comentario']): ?>
                    <p class="comentario-corto">"<?php echo htmlspecialchars(substr($testimonio['comentario'], 0, 120)); ?><?php echo strlen($testimonio['comentario']) > 120 ? '...' : ''; ?>"</p>
                    <?php endif; ?>
                    <div class="cliente-nombre-index">
                        <strong><?php echo htmlspecialchars($testimonio['cliente_nombre']); ?></strong>
                        <span><?php echo date('M Y', strtotime($testimonio['fecha'])); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="calificar_pedido.php" class="btn-ver-mas">Ver más opiniones y calificar</a>
            </div>
            <?php else: ?>
            <p class="sin-testimonios">¡Sé el primero en dejar tu opinión!</p>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="calificar_pedido.php" class="btn-ver-mas">Calificar mi pedido</a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <footer>
        <p>&copy; 2026 SHIKEI - Diego Alejandro Garcia Lara y Keisi Sharith Guerrero Rojas</p>
    </footer>
</body>
</html>