<?php
session_start();

// Verificar si el usuario es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Obtener el ID del proyecto
if (!isset($_GET['proyecto_id'])) {
    header('Location: dashboard.php');
    exit();
}

$proyectoId = $_GET['proyecto_id'];

// Rutas de los archivos JSON
$archivoProyectos = '../json/proyectos.json';
$archivoTareas = '../json/tareas.json';
$archivoUsuarios = '../json/usuarios.json';

// Leer los archivos JSON
$proyectos = json_decode(file_get_contents($archivoProyectos), true);
$tareas = json_decode(file_get_contents($archivoTareas), true);
$usuarios = json_decode(file_get_contents($archivoUsuarios), true);

// Verificar si los datos están correctamente cargados
if (!is_array($proyectos) || !is_array($tareas) || !is_array($usuarios)) {
    die("Error cargando datos.");
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevaTarea = [
        'id' => count($tareas) + 1, // Generar un nuevo ID
        'proyecto_id' => $proyectoId, // Asociar la tarea con el proyecto actual
        'nombre' => $_POST['nombre'],
        'descripcion' => $_POST['descripcion'],
        'asignada_a' => $_POST['asignada_a'], // Usuario al que se le asigna la tarea
        'fecha_limite' => $_POST['fecha_limite'],
        'estado' => 'pendiente' // Estado inicial de la tarea
    ];

    // Añadir la nueva tarea al array de tareas
    $tareas[] = $nuevaTarea;

    // Guardar los cambios en el archivo JSON
    file_put_contents($archivoTareas, json_encode($tareas, JSON_PRETTY_PRINT));

    // Redirigir al dashboard
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tareas al Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">
    <h2 class="mt-4">Agregar Tareas al Proyecto</h2>

    <form action="agregar_tareas.php?proyecto_id=<?php echo $proyectoId; ?>" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Tarea:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="asignada_a" class="form-label">Asignar a:</label>
            <select name="asignada_a" id="asignada_a" class="form-select" required>
                <option value="">-- Selecciona un usuario --</option>
                <?php foreach ($usuarios as $usuario) : ?>
                    <option value="<?php echo htmlspecialchars($usuario['username']); ?>">
                        <?php echo htmlspecialchars($usuario['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_limite" class="form-label">Fecha Límite:</label>
            <input type="date" name="fecha_limite" id="fecha_limite" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Agregar Tarea</button>
        <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
    </form>
</body>
</html>

