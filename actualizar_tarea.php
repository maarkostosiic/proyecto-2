<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Leer las tareas desde el archivo JSON
$tareas = json_decode(file_get_contents('../json/tareas.json'), true);

// Verificar si se ha pasado un ID válido de tarea
if (!isset($_GET['id'])) {
    die("ID de tarea no especificado.");
}

$id = $_GET['id'];

// Buscar la tarea por ID
$tarea = null;
foreach ($tareas as $index => $t) {
    if ($t['id'] == $id) {
        $tarea = &$tareas[$index];  // Usamos referencia para modificar la tarea más adelante
        break;
    }
}

if (!$tarea) {
    die("Tarea no encontrada.");
}

// Verificar si el formulario de actualización fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar el estado de la tarea
    $nuevoEstado = $_POST['estado'];
    $tarea['estado'] = $nuevoEstado;
    
    // Guardar los cambios en el archivo JSON de tareas
    file_put_contents('../json/tareas.json', json_encode($tareas, JSON_PRETTY_PRINT));
    
    // Redirigir al dashboard después de actualizar la tarea
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">
    <h2 class="mt-4">Actualizar Tarea: <?php echo htmlspecialchars($tarea['descripcion']); ?></h2>

    <!-- Formulario para actualizar el estado de la tarea -->
    <form action="actualizar_tarea.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-3">
            <label for="estado" class="form-label">Estado de la Tarea:</label>
            <select name="estado" id="estado" class="form-select" required>
                <option value="pendiente" <?php echo $tarea['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="completada" <?php echo $tarea['estado'] === 'completada' ? 'selected' : ''; ?>>Completada</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
