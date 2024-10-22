<?php
session_start();

// Leer los proyectos y tareas desde los archivos JSON
$proyectos = json_decode(file_get_contents('../json/proyectos.json'), true);
$tareas = json_decode(file_get_contents('../json/tareas.json'), true);

// Verificar si se ha pasado un ID válido de proyecto
if (!isset($_GET['id'])) {
    die("ID de proyecto no especificado.");
}

$id = $_GET['id'];

// Buscar el proyecto por ID
$proyecto = null;
foreach ($proyectos as $p) {
    if ($p['id'] == $id) {
        $proyecto = $p;
        break;
    }
}

if (!$proyecto) {
    die("Proyecto no encontrado.");
}

// Verificar si el formulario de editar proyecto fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar el nombre del proyecto
    $nuevoNombre = trim($_POST['nombre']); // Limpiar el nombre del proyecto
    foreach ($proyectos as &$p) {
        if ($p['id'] == $id) {
            $p['nombre'] = $nuevoNombre; // Asignar el nuevo nombre al proyecto correspondiente
            break;
        }
    }
    
    // Guardar los cambios en el archivo JSON de proyectos
    file_put_contents('../json/proyectos.json', json_encode($proyectos, JSON_PRETTY_PRINT));
    
    // Manejo de agregar nuevas tareas solo si el campo no está vacío
    if (!empty(trim($_POST['nueva_tarea']))) {
        $nuevaTarea = [
            'id' => count($tareas) > 0 ? max(array_column($tareas, 'id')) + 1 : 1,  // Generar ID para la nueva tarea
            'descripcion' => trim($_POST['nueva_tarea']),
            'proyecto_id' => $id,
            'asignada_a' => $_POST['asignada_a'],
            'fecha_limite' => $_POST['fecha_limite'],
            'estado' => 'pendiente'
        ];
        $tareas[] = $nuevaTarea; // Agregar la nueva tarea al array de tareas
    }
    
    // Manejo de eliminar tareas existentes
    if (isset($_POST['eliminar_tarea'])) {
        $tareasAEliminar = $_POST['eliminar_tarea']; // Capturar la variable global $_POST en una variable local
        $tareas = array_filter($tareas, function($tarea) use ($tareasAEliminar) {
            return !in_array($tarea['id'], $tareasAEliminar); // Filtrar tareas a eliminar
        });
    }

    // Guardar los cambios en el archivo JSON de tareas
    file_put_contents('../json/tareas.json', json_encode(array_values($tareas), JSON_PRETTY_PRINT));
    
    // Redirigir al mismo formulario después de la actualización
    header("Location: editar_proyecto.php?id=$id");
    exit();
}

// Filtrar las tareas que pertenecen a este proyecto
$tareasProyecto = array_filter($tareas, function($tarea) use ($id) {
    return $tarea['proyecto_id'] == $id; // Obtener tareas que pertenecen al proyecto
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">
    <h2 class="mt-4">Editar Proyecto: <?php echo htmlspecialchars($proyecto['nombre']); ?></h2>

    <!-- Formulario para editar el nombre del proyecto y agregar tareas -->
    <form action="editar_proyecto.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Proyecto:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo htmlspecialchars($proyecto['nombre']); ?>" required>
        </div>

        <h3 class="mt-4">Tareas Asociadas</h3>
        <ul class="list-group mb-3">
            <?php foreach ($tareasProyecto as $tarea) : ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($tarea['descripcion']); ?> 
                    (Asignada a: <?php echo htmlspecialchars($tarea['asignada_a']); ?>, Fecha Límite: <?php echo htmlspecialchars($tarea['fecha_limite']); ?>)
                    <div class="float-end">
                        <input type="checkbox" name="eliminar_tarea[]" value="<?php echo $tarea['id']; ?>"> Eliminar
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Agregar nueva tarea -->
        <h3 class="mt-4">Agregar Nueva Tarea</h3>
        <div class="mb-3">
            <label for="nueva_tarea" class="form-label">Descripción de la Tarea:</label>
            <input type="text" name="nueva_tarea" id="nueva_tarea" class="form-control">
        </div>

        <div class="mb-3">
            <label for="asignada_a" class="form-label">Asignar a:</label>
            <select name="asignada_a" id="asignada_a" class="form-select">
                <option value="">-- Selecciona un usuario (opcional) --</option>
                <?php foreach (json_decode(file_get_contents('../json/usuarios.json'), true) as $usuario) : ?>
                    <option value="<?php echo htmlspecialchars($usuario['username']); ?>">
                        <?php echo htmlspecialchars($usuario['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_limite" class="form-label">Fecha Límite:</label>
            <input type="date" name="fecha_limite" id="fecha_limite" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="dashboard.php" class="btn btn-secondary">Volver</a>
    </form>
</body>
</html>
