<?php
session_start();

// Leer el archivo JSON de usuarios
$usuarios = json_decode(file_get_contents('../json/usuarios.json'), true);

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Buscar el usuario en el array
    foreach ($usuarios as $usuario) {
        if ($usuario['username'] === $username && password_verify($password, $usuario['password'])) {
            // Guardar el usuario en la sesión
            $_SESSION['usuario'] = [
                'username' => $usuario['username'],
                'role' => $usuario['role']
            ];

            // Redirigir al dashboard
            header('Location: dashboard.php');
            exit();
        }
    }

    echo "Nombre de usuario o contraseña incorrectos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos para el video de fondo */
        body, html {
            height: 100%;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        video.background-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1; /* Asegúrate de que el video esté detrás del contenido */
        }

        /* Estilo para centrar el formulario */
        .container {
            position: relative;
            z-index: 1; /* Asegura que el formulario esté delante del video */
            color: white; /* Cambiar color de texto para mejor legibilidad */
            text-align: center; /* Centrar el contenido */
            top: 50%; /* Centramos verticalmente */
            transform: translateY(-50%); /* Ajustamos verticalmente para centrar */
            max-width: 400px; /* Ancho máximo del formulario */
            margin: 0 auto; /* Centramos horizontalmente */
            padding: 20px; /* Añadimos un poco de padding */
            background: rgba(0, 0, 0, 0.5); /* Fondo semitransparente para mejorar la legibilidad */
            border-radius: 10px; /* Bordes redondeados */
        }
    </style>
</head>
<body>
    <video autoplay muted loop class="background-video">
        <!-- Reemplaza la URL del video aquí -->
        <source src="https://videos.pexels.com/video-files/3129671/3129671-uhd_2560_1440_30fps.mp4" type="video/mp4">
        Tu navegador no soporta video.
    </video>

    <div class="container">
        <h2 class="mt-4">Inicio de Sesión</h2>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de usuario:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>

        <!-- Nuevo botón de registrarse -->
        <div class="mt-3">
            <a href="register.php" class="btn btn-secondary">Registrarse</a>
        </div>
    </div>
</body>
</html>
