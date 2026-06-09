<?php 
include 'config/db.php';
$stmt = $pdo->query("SELECT * FROM productos ORDER BY categoria, nombre");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categorias = array();
foreach($productos as $prod) {
    $categorias[$prod['categoria']][] = $prod;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - SHIKEY</title>
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
                <li><a href="menu.php">Menú</a></li>
                <li><a href="carrito.php">Carrito (<span id="cart-count">0</span>)</a></li>
            </ul>
        </nav>
    </header>

    <section class="menu-header">
        <h2>MENÚ</h2>
        <p>Flavor that falls love with a chicken</p>
    </section>

    <div class="container">
        <?php foreach($categorias as $categoria => $productos_categoria): ?>
        <div class="categoria-seccion">
            <h3 class="categoria-titulo"><?php echo strtoupper($categoria); ?></h3>
            <div class="menu-grid">
                <?php foreach ($productos_categoria as $prod): ?>
                <div class="card">
                    <img src="<?php echo $prod['imagen']; ?>" alt="<?php echo $prod['nombre']; ?>">
                    <div class="card-body">
                        <h3><?php echo $prod['nombre']; ?></h3>
                        <p class="descripcion"><?php echo $prod['descripcion']; ?></p>
                        <p class="precio"><strong>$<?php echo number_format($prod['precio'], 0, ',', '.'); ?></strong></p>
                        <button class="btn" onclick="agregarAlCarrito(<?php echo $prod['id']; ?>, '<?php echo $prod['nombre']; ?>', <?php echo $prod['precio']; ?>)">Agregar al Pedido</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="js/main.js"></script>
</body>
</html>