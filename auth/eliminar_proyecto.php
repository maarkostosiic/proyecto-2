<?php
session_start();

// Leer los proyectos y tareas desde los archivos JSON
$proyectos = json_decode(file_get_contents('../json/proyectos.json'), true);
$tareas = json_decode(file_get_contents('../json/tareas.json'), true);

// Verificar si se ha pasado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de proyecto no especificado o no válido.");
}

$id = (int)$_GET['id']; // Asegúrate de que el ID sea un entero

// Filtrar los proyectos para eliminar el que corresponde al ID
$proyectosFiltrados = array_filter($proyectos, function($proj) use ($id) {
    return (int)$proj['id'] !== $id; // Asegúrate de comparar como enteros
});

// Si no se eliminó ningún proyecto, eso indica que el ID no estaba en la lista
if (count($proyectos) === count($proyectosFiltrados)) {
    die("El proyecto con ID $id no existe o no se pudo eliminar.");
}

// Guardar los cambios en el archivo JSON de proyectos
if (file_put_contents('../json/proyectos.json', json_encode(array_values($proyectosFiltrados), JSON_PRETTY_PRINT)) === false) {
    die("Error al guardar cambios en proyectos.");
}

// Eliminar todas las tareas asociadas con este proyecto
$tareasFiltradas = array_filter($tareas, function($tarea) use ($id) {
    return (int)$tarea['proyecto_id'] !== $id; // Asegúrate de comparar como enteros
});

// Guardar los cambios en el archivo JSON de tareas
if (file_put_contents('../json/tareas.json', json_encode(array_values($tareasFiltradas), JSON_PRETTY_PRINT)) === false) {
    die("Error al guardar cambios en tareas.");
}

// Redirigir al dashboard después de eliminar el proyecto
header('Location: dashboard.php');
exit();
?>
