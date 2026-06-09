<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'])) {
    $pedido_id = $_POST['pedido_id'];
    
    try {
        $pdo->beginTransaction();
        
        $stmt_detalle = $pdo->prepare("DELETE FROM detalle_pedido WHERE pedido_id = ?");
        $stmt_detalle->execute([$pedido_id]);
        
        $stmt_pedido = $pdo->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmt_pedido->execute([$pedido_id]);
        
        $pdo->commit();
        header("Location: admin.php?mensaje=eliminado");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error al eliminar: " . $e->getMessage();
    }
} else {
    header("Location: admin.php");
    exit;
}
?>