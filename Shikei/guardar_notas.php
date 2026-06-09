<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['notas'])) {
    $pedido_id = $_POST['pedido_id'];
    $notas = $_POST['notas'];
    
    $stmt = $pdo->prepare("UPDATE pedidos SET notas = ? WHERE id = ?");
    $stmt->execute([$notas, $pedido_id]);
    
    header("Location: admin.php?mensaje=notas_guardadas");
    exit;
} else {
    header("Location: admin.php");
    exit;
}
?>