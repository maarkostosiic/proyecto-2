<?php
session_start();

// Verificar si el usuario es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Ruta de los archivos JSON
$archivoProyectos = '../json/proyectos.json';
$archivoUsuarios = '../json/usuarios.json';

// Leer los proyectos y usuarios existentes
$proyectos = json_decode(file_get_contents($archivoProyectos), true);
$usuarios = json_decode(file_get_contents($archivoUsuarios), true);

// Verificar si los datos de proyectos y usuarios están cargados
if (!is_array($proyectos)) {
    $proyectos = [];
}
if (!is_array($usuarios)) {
    die("Error cargando usuarios.");
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoProyecto = [
        'id' => count($proyectos) + 1, // Generar un nuevo ID
        'nombre' => $_POST['nombre'],
        'asignado_a' => $_POST['asignado_a'] // Usuario al que se le asigna el proyecto
    ];

    // Añadir el nuevo proyecto al array
    $proyectos[] = $nuevoProyecto;

    // Guardar los cambios en el archivo JSON
    file_put_contents($archivoProyectos, json_encode($proyectos, JSON_PRETTY_PRINT));

    // Redirigir a la página de agregar tareas con el ID del nuevo proyecto
    header('Location: agregar_tareas.php?proyecto_id=' . $nuevoProyecto['id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">
    <h2 class="mt-4">Crear Nuevo Proyecto</h2>

    <form action="crear_proyecto.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Proyecto:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="asignado_a" class="form-label">Asignar a:</label>
            <select name="asignado_a" id="asignado_a" class="form-control" required>
                <option value="">-- Selecciona un usuario --</option>
                <?php foreach ($usuarios as $usuario) : ?>
                    <option value="<?php echo htmlspecialchars($usuario['username']); ?>">
                        <?php echo htmlspecialchars($usuario['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Crear Proyecto</button>
        <a href="dashboard.php" class="btn btn-secondary">Volver</a>
    </form>
</body>
</html>
