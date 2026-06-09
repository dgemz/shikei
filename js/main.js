let carrito = JSON.parse(localStorage.getItem('shikei_carrito')) || [];

function actualizarContador() {
    const contador = document.getElementById('cart-count');
    if (contador) {
        contador.innerText = carrito.reduce((acc, item) => acc + item.cantidad, 0);
    }
}

function agregarAlCarrito(id, nombre, precio) {
    const existe = carrito.find(item => item.id === id);
    if (existe) {
        existe.cantidad++;
    } else {
        carrito.push({ id, nombre, precio, cantidad: 1 });
    }
    localStorage.setItem('shikei_carrito', JSON.stringify(carrito));
    actualizarContador();
    alert(nombre + ' agregado al pedido.');
}

function validarPedido(nombre, mesa) {
    const mesaNum = parseInt(mesa);
    if (nombre.trim() === '') {
        alert('Por favor ingrese su nombre.');
        return false;
    }
    if (isNaN(mesaNum) || mesaNum < 1 || mesaNum > 20) {
        alert('Esa no es su mesa o esta llena. Por favor verifique su numero de mesa (1-20).');
        return false;
    }
    if (carrito.length === 0) {
        alert('El carrito esta vacio.');
        return false;
    }
    return true;
}

function procesarPedido(event) {
    event.preventDefault();
    const nombre = document.getElementById('cliente_nombre').value;
    const mesa = document.getElementById('mesa').value;

    if (!validarPedido(nombre, mesa)) {
        return;
    }

    const total = carrito.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
    
    const formData = new FormData();
    formData.append('cliente_nombre', nombre);
    formData.append('mesa', mesa);
    formData.append('total', total);
    formData.append('productos', JSON.stringify(carrito));

    fetch('procesar_pedido.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'Exito') {
            alert('Pedido enviado a cocina con exito!');
            localStorage.removeItem('shikei_carrito');
            carrito = [];
            actualizarContador();
            window.location.href = 'index.php';
        } else {
            alert('Error al procesar el pedido: ' + data);
        }
    })
    .catch(error => {
        alert('Error de conexion al procesar el pedido.');
    });
}

if (document.getElementById('cart-count')) {
    actualizarContador();
}