<?php
session_start();

// Comprobar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si no hay sesión, redirigir a login
    header('Location: auth/login.php');
    exit();
}

// Si el usuario está autenticado, mostrar el contenido del index
echo "<h2>Bienvenido a la plataforma de gestión de proyectos</h2>";
echo "<a href='dashboard.php'>Ir al Dashboard</a><br>";
echo "<a href='auth/logout.php'>Cerrar sesión</a>";
?>

