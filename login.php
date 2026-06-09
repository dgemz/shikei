<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - SHIKEI</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin-top: 5rem;">
        <h2>Acceso Administrador</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
            <input type="text" name="usuario" placeholder="Usuario" required style="padding: 10px;">
            <input type="password" name="password" placeholder="Contraseña" required style="padding: 10px;">
            <button type="submit" class="btn">Ingresar</button>
        </form>
        <p style="margin-top: 15px;"><a href="index.php">Volver al inicio</a></p>
    </div>
</body>
</html>