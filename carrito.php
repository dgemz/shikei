<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - SHIKEY</title>
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
                <li><a href="carrito.php">Carrito (<span id="cart-count">0</span>)</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <h2 class="titulo-carrito">Resumen de su Pedido</h2>
    </div>
<div id="factura">
    <div id="tabla-factura" class="tabla-full-width">
        <table class="tabla-carrito">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="lista-carrito">
            </tbody>
        </table>
    </div>
</div>
    
    <div class="container">
        <br><br>
        <h3 id="total-carrito" class="total-carrito">Total: $0</h3>
        <br><br>
        
        <form id="formulario-pedido" onsubmit="procesarPedido(event)" class="formulario-pedido">
            <h3>Datos para el Pedido</h3>
            <div class="form-group">
                <label for="cliente_nombre">Nombre del Cliente:</label>
                <input type="text" id="cliente_nombre" name="cliente_nombre" required placeholder="Ingresa tu nombre">
            </div>
            
            <div class="form-group">
                <label for="mesa">Numero de Mesa:</label>
                <input type="number" id="mesa" name="mesa" required placeholder="Ej: 5" min="1" max="20">
            </div>
            
            <button type="submit" id="btn-enviar-pedido" class="btn-enviar-pedido">Confirmar Pedido</button>
        </form>

        <div class="calificar-caja">
            <p>¿Ya recibiste tu pedido?</p>
            <a href="calificar_pedido.php" class="btn-ver-mas">Calificar mi pedido</a>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script>
        function renderizarCarrito() {
            const contenedor = document.getElementById('lista-carrito');
            const totalElement = document.getElementById('total-carrito');
            contenedor.innerHTML = '';
            let total = 0;

            if (carrito.length === 0) {
                contenedor.innerHTML = '<tr><td colspan="5" class="carrito-vacio">El carrito está vacío. ¡Agrega productos del menú!</td></tr>';
            } else {
                carrito.forEach((item, index) => {
                    const subtotal = item.precio * item.cantidad;
                    total += subtotal;
                    contenedor.innerHTML += `
                        <tr>
                            <td>${item.nombre}</td>
                            <td>${item.cantidad}</td>
                            <td>$${item.precio.toLocaleString('es-CO')}</td>
                            <td>$${subtotal.toLocaleString('es-CO')}</td>
                            <td><button class="btn-eliminar" onclick="eliminarDelCarrito(${index})">Eliminar</button></td>
                        </tr>
                    `;
                });
            }
            totalElement.innerText = 'Total: $' + total.toLocaleString('es-CO');
        }
        
        function eliminarDelCarrito(index) {
            carrito.splice(index, 1);
            localStorage.setItem('shikei_carrito', JSON.stringify(carrito));
            actualizarContador();
            renderizarCarrito();
        }

        renderizarCarrito();
    </script>
</body>
</html>