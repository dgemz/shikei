<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = $_POST['cliente_nombre'];
    $mesa = $_POST['mesa'];
    $total = $_POST['total'];
    $productos_json = $_POST['productos'];

    try {
        $pdo->beginTransaction();
        
        $stmt_check = $pdo->prepare("SELECT id FROM pedidos WHERE mesa = ? AND estado IN ('pendiente', 'en_espera')");
        $stmt_check->execute([$mesa]);
        
        if ($stmt_check->fetch()) {
            echo "Error: La mesa $mesa ya tiene un pedido activo. Por favor verifique su numero de mesa.";
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_nombre, mesa, total, estado) VALUES (?, ?, ?, 'pendiente')");
        $stmt->execute([$cliente, $mesa, $total]);
        $pedido_id = $pdo->lastInsertId();

        $productos = json_decode($productos_json, true);
        $stmt_detalle = $pdo->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        
        foreach ($productos as $prod) {
            $stmt_detalle->execute([$pedido_id, $prod['id'], $prod['cantidad'], $prod['precio']]);
        }

        $pdo->commit();
        echo "Exito";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>