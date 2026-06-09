<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config/db.php';

$mensaje = '';
if (isset($_GET['mensaje'])) {
    if ($_GET['mensaje'] === 'eliminado') {
        $mensaje = 'Pedido eliminado correctamente';
    } elseif ($_GET['mensaje'] === 'notas_guardadas') {
        $mensaje = 'Notas guardadas correctamente';
    } elseif ($_GET['mensaje'] === 'testimonio_eliminado') {
        $mensaje = 'Testimonio eliminado correctamente';
    } elseif ($_GET['mensaje'] === 'aprobacion_cambiada') {
        $mensaje = 'Estado de aprobación cambiado';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar_testimonio'])) {
        $stmt = $pdo->prepare("DELETE FROM testimonios WHERE id = ?");
        $stmt->execute([$_POST['eliminar_testimonio']]);
        header("Location: admin.php?mensaje=testimonio_eliminado");
        exit;
    }
    
    if (isset($_POST['toggle_aprobacion'])) {
        $stmt = $pdo->prepare("UPDATE testimonios SET aprobado = NOT aprobado WHERE id = ?");
        $stmt->execute([$_POST['toggle_aprobacion']]);
        header("Location: admin.php?mensaje=aprobacion_cambiada");
        exit;
    }

    if (isset($_POST['pedido_id'], $_POST['estado'])) {
        $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        $stmt->execute([$_POST['estado'], $_POST['pedido_id']]);
        header("Location: admin.php");
        exit;
    }
}

$stmt = $pdo->query("SELECT * FROM pedidos ORDER BY fecha DESC");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$detalles = [];
$stmt_detalles = $pdo->query("SELECT dp.pedido_id, dp.cantidad, dp.precio_unitario, p.nombre FROM detalle_pedido dp JOIN productos p ON dp.producto_id = p.id");
foreach ($stmt_detalles->fetchAll(PDO::FETCH_ASSOC) as $detalle) {
    $detalles[$detalle['pedido_id']][] = $detalle;
}

$stmt_testimonios = $pdo->query("SELECT t.*, p.mesa, p.cliente_nombre as cliente_pedido FROM testimonios t LEFT JOIN pedidos p ON t.pedido_id = p.id ORDER BY t.aprobado ASC, t.fecha DESC");
$testimonios_admin = $stmt_testimonios->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - SHIKEY</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="img/logo.png" alt="SHIKEY Logo" class="logo">
            </div>
            <h1>SHIKEY - Panel Admin</h1>
        </div>
        <nav>
            <ul>
                <li><a href="admin.php">Pedidos</a></li>
                <li><a href="index.php">Cerrar Sesion</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <?php if ($mensaje): ?>
        <div class="alerta-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <h2>Control de Pedidos</h2>
        
        <?php if (empty($pedidos)): ?>
        <p class="sin-pedidos">No hay pedidos registrados</p>
        <?php else: ?>
        
        <div class="pedidos-grid">
            <?php foreach ($pedidos as $p): ?>
            <div class="pedido-card">
                <div class="pedido-header">
                    <h3>Pedido #<?php echo $p['id']; ?></h3>
                    <span class="badge <?php echo $p['estado']; ?>"><?php echo strtoupper(str_replace('_', ' ', $p['estado'])); ?></span>
                </div>
                
                <div class="pedido-info">
                    <p><strong>Cliente:</strong> <?php echo $p['cliente_nombre']; ?></p>
                    <p><strong>Mesa:</strong> <?php echo $p['mesa']; ?></p>
                    <p><strong>Total:</strong> $<?php echo number_format($p['total'], 0, ',', '.'); ?></p>
                    <p><strong>Fecha:</strong> <?php echo $p['fecha']; ?></p>
                </div>

                <div class="pedido-detalles">
                    <h4>Detalle del Pedido:</h4>
                    <ul class="lista-detalles">
                        <?php if (isset($detalles[$p['id']])): ?>
                            <?php foreach ($detalles[$p['id']] as $item): ?>
                                <li>
                                    <span class="detalle-cantidad"><?php echo $item['cantidad']; ?>x</span>
                                    <span class="detalle-nombre"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                    <span class="detalle-precio">$<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 0, ',', '.'); ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Sin detalles registrados</li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="pedido-notas">
                    <form method="POST" action="guardar_notas.php">
                        <input type="hidden" name="pedido_id" value="<?php echo $p['id']; ?>">
                        <label for="notas_<?php echo $p['id']; ?>">Notas / Observaciones:</label>
                        <textarea name="notas" id="notas_<?php echo $p['id']; ?>" rows="3" placeholder="Agregar notas sobre este pedido..."><?php echo $p['notas']; ?></textarea>
                        <button type="submit" class="btn-notas">Guardar Notas</button>
                    </form>
                </div>
                
                <div class="pedido-acciones">
                    <form method="POST" class="form-estado">
                        <input type="hidden" name="pedido_id" value="<?php echo $p['id']; ?>">
                        <select name="estado" onchange="this.form.submit()">
                            <option value="pendiente" <?php echo $p['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="en_espera" <?php echo $p['estado'] == 'en_espera' ? 'selected' : ''; ?>>En Espera</option>
                            <option value="entregado" <?php echo $p['estado'] == 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                            <option value="cancelado" <?php echo $p['estado'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </form>
                    
                    <form method="POST" action="eliminar_pedido.php" class="form-eliminar" onsubmit="return confirm('¿Estas seguro de eliminar este pedido? Esta accion no se puede deshacer.');">
                        <input type="hidden" name="pedido_id" value="<?php echo $p['id']; ?>">
                        <button type="submit" class="btn-eliminar">Eliminar Pedido</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>

        <h2 style="margin-top: 3rem; color: var(--color-accent);">Moderación de Testimonios</h2>
        
        <?php if (empty($testimonios_admin)): ?>
        <p class="sin-pedidos">No hay testimonios registrados</p>
        <?php else: ?>
        
        <div class="testimonios-admin-grid">
            <?php foreach ($testimonios_admin as $testimonio): ?>
            <div class="testimonio-admin-card <?php echo $testimonio['aprobado'] ? 'aprobado' : 'pendiente'; ?>">
                <div class="testimonio-admin-header">
                    <div>
                        <h4><?php echo htmlspecialchars($testimonio['cliente_nombre']); ?></h4>
                        <?php if ($testimonio['cliente_pedido']): ?>
                        <small>Mesa <?php echo $testimonio['mesa']; ?> - Pedido #<?php echo $testimonio['pedido_id']; ?></small>
                        <?php endif; ?>
                    </div>
                    <span class="badge-estado <?php echo $testimonio['aprobado'] ? 'aprobado' : 'pendiente'; ?>">
                        <?php echo $testimonio['aprobado'] ? 'Aprobado' : 'Pendiente'; ?>
                    </span>
                </div>
                
                <div class="estrellas-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="<?php echo $i <= $testimonio['calificacion'] ? 'estrella-llena' : 'estrella-vacia'; ?>">★</span>
                    <?php endfor; ?>
                </div>
                
                <?php if ($testimonio['comentario']): ?>
                <p class="comentario-testimonio-admin">"<?php echo htmlspecialchars($testimonio['comentario']); ?>"</p>
                <?php endif; ?>
                
                <small class="fecha-testimonio-admin"><?php echo $testimonio['fecha']; ?></small>
                
                <div class="testimonio-admin-acciones">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="toggle_aprobacion" value="<?php echo $testimonio['id']; ?>">
                        <button type="submit" class="btn-toggle-aprobacion">
                            <?php echo $testimonio['aprobado'] ? 'Desaprobar' : 'Aprobar'; ?>
                        </button>
                    </form>
                    
                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este testimonio?');">
                        <input type="hidden" name="eliminar_testimonio" value="<?php echo $testimonio['id']; ?>">
                        <button type="submit" class="btn-eliminar-testimonio">Eliminar</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
    </div>
</body>
</html>