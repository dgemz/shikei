<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido_id = trim($_POST['pedido_id'] ?? '');
    $cliente_nombre = trim($_POST['cliente_nombre'] ?? '');
    $calificacion = intval($_POST['calificacion'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');
    
    if ($pedido_id && $cliente_nombre && $calificacion >= 1 && $calificacion <= 5) {
        try {
            $stmt = $pdo->prepare("INSERT INTO testimonios (pedido_id, cliente_nombre, calificacion, comentario, aprobado) VALUES (?, ?, ?, ?, 1)");
            $resultado = $stmt->execute([$pedido_id, $cliente_nombre, $calificacion, $comentario]);
            
            if ($resultado) {
                $_SESSION['mensaje'] = '¡Gracias por tu calificación! Tu opinión es muy importante para nosotros.';
                $_SESSION['tipo_mensaje'] = 'exito';
            } else {
                $_SESSION['mensaje'] = 'No se pudo guardar la calificación.';
                $_SESSION['tipo_mensaje'] = 'error';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'error';
        }
    } else {
        $_SESSION['mensaje'] = 'Por favor completa todos los campos requeridos con valores válidos.';
        $_SESSION['tipo_mensaje'] = 'error';
    }
    
    header("Location: calificar_pedido.php");
    exit;
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

$stmt = $pdo->query("SELECT * FROM testimonios WHERE aprobado = 1 ORDER BY fecha DESC");
$testimonios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Califica tu Pedido - SHIKEY</title>
    <link rel="stylesheet" href="css/style.css">
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
            </ul>
        </nav>
    </header>

    <section class="calificacion-section">
        <div class="container">
            <h2>¿Cómo estuvo tu experiencia?</h2>
            <p class="subtitulo">Ayúdanos a mejorar calificando tu pedido</p>
            
            <?php if ($mensaje): ?>
            <div class="alerta alerta-<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
            <?php endif; ?>
            
            <div class="calificacion-form-container">
                <form method="POST" class="calificacion-form">
                    <div class="form-group">
                        <label for="pedido_id">Número de Pedido:</label>
                        <input type="number" id="pedido_id" name="pedido_id" required placeholder="Ej: 1" min="1">
                        <small>Ingresa el número de pedido que aparece en tu confirmación</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente_nombre">Tu Nombre:</label>
                        <input type="text" id="cliente_nombre" name="cliente_nombre" required placeholder="¿Cómo te llamas?">
                    </div>
                    
                    <div class="form-group">
                        <label>Calificación:</label>
                        <div class="estrellas-calificacion">
                            <input type="radio" name="calificacion" value="5" id="estrella5" required>
                            <label for="estrella5" title="5 estrellas">★</label>
                            <input type="radio" name="calificacion" value="4" id="estrella4">
                            <label for="estrella4" title="4 estrellas">★</label>
                            <input type="radio" name="calificacion" value="3" id="estrella3">
                            <label for="estrella3" title="3 estrellas">★</label>
                            <input type="radio" name="calificacion" value="2" id="estrella2">
                            <label for="estrella2" title="2 estrellas">★</label>
                            <input type="radio" name="calificacion" value="1" id="estrella1">
                            <label for="estrella1" title="1 estrella">★</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comentario">Tu Opinión (opcional):</label>
                        <textarea id="comentario" name="comentario" rows="4" placeholder="Cuéntanos qué te pareció la comida, el servicio, la atención..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-enviar-calificacion">Enviar Calificación</button>
                </form>
            </div>
        </div>
    </section>

    <section class="testimonios-section">
        <div class="container">
            <h2>Lo que dicen nuestros clientes</h2>
            <p class="subtitulo">Opiniones reales de quienes nos han visitado</p>
            
            <?php if (empty($testimonios)): ?>
            <p class="sin-testimonios">Sé el primero en dejar tu opinión</p>
            <?php else: ?>
            
            <div class="testimonios-grid">
                <?php foreach ($testimonios as $testimonio): ?>
                <div class="testimonio-card">
                    <div class="testimonio-header">
                        <div class="cliente-info">
                            <div class="cliente-avatar"><?php echo strtoupper(substr($testimonio['cliente_nombre'], 0, 1)); ?></div>
                            <div>
                                <h4><?php echo htmlspecialchars($testimonio['cliente_nombre']); ?></h4>
                                <div class="estrellas-display">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?php echo $i <= $testimonio['calificacion'] ? 'estrella-llena' : 'estrella-vacia'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <span class="fecha-testimonio"><?php echo date('d/m/Y', strtotime($testimonio['fecha'])); ?></span>
                    </div>
                    
                    <?php if ($testimonio['comentario']): ?>
                    <p class="comentario-testimonio">"<?php echo htmlspecialchars($testimonio['comentario']); ?>"</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container footer-content">
            <div class="footer-section">
                <h3>SHIKEY</h3>
                <p>El sabor que te enamora. Especialistas en pollo y alas picantes con salsas únicas.</p>
            </div>
            
            <div class="footer-section">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="menu.php">Menú</a></li>
                    <li><a href="calificar_pedido.php">Calificar Pedido</a></li>
                    <li><a href="login.php">Admin</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contacto</h3>
                <ul>
                    <li>📍 Carrera 2A #22-49 Paseo del Puente</li>
                    <li>📱 314 379 2268 - 317 263 3304</li>
                    <li>✉️ contacto@shikey.com</li>
                    <li>🕐 Lun-Sab: 5PM - 11PM | Dom: 4PM - 10PM</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 SHIKEY - Diego Alejandro Garcia Lara y Keisi Sharith Guerrero Rojas</p>
        </div>
    </footer>
</body>
</html>