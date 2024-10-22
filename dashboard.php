<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Leer los archivos JSON de proyectos y tareas
$proyectos = json_decode(file_get_contents('../json/proyectos.json'), true);
$tareas = json_decode(file_get_contents('../json/tareas.json'), true);

// Verificar si los datos están correctamente cargados
if (!is_array($proyectos)) {
    die("Error cargando proyectos.");
}

if (!is_array($tareas)) {
    die("Error cargando tareas.");
}

// Obtener el rol del usuario
$rol = $_SESSION['usuario']['role'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Última versión de Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fuente moderna de Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9; /* Fondo blanco roto */
        }

        h2, h3 {
            font-weight: 600;
            color: #333;
        }

        .container {
            margin-top: 40px;
        }

        /* Estilo para las tarjetas de lista */
        .list-group-item {
            border: none;
            background-color: #ffffff; /* Fondo blanco */
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .list-group-item:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.15);
        }

        /* Botones con nuevos colores vibrantes */
        .btn {
            border-radius: 25px;
            font-weight: 500;
            padding: 10px 20px;
            transition: background-color 0.3s ease;
        }

        .btn-success {
            background-color: #00bcd4; /* Azul vibrante */
            color: white;
        }

        .btn-success:hover {
            background-color: #008c9e; /* Azul oscuro */
        }

        .btn-warning {
            background-color: #ff9800; /* Naranja vibrante */
            color: white;
        }

        .btn-warning:hover {
            background-color: #e65100; /* Naranja oscuro */
        }

        .btn-danger {
            background-color: #f44336; /* Rojo brillante */
            color: white;
        }

        .btn-danger:hover {
            background-color: #b71c1c; /* Rojo oscuro */
        }

        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
            background-color: #673ab7; /* Morado */
            color: white;
        }

        /* Estilo para los headers de cada proyecto */
        h4 {
            color: #ff7043; /* Naranja */
            font-weight: 500;
            margin-top: 20px;
        }

        /* Estilo para el contenedor de tareas */
        ul.list-group.mb-3 {
            background-color: #f3f4f6; /* Fondo gris claro */
            padding: 15px;
            border-radius: 8px;
        }
        .card-container {
    background-color: #ffffff; /* Fondo blanco */
    border-radius: 15px; /* Esquinas redondeadas */
    padding: 20px; /* Espaciado interno */
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1); /* Sombra sutil */
    margin-bottom: 20px; /* Espaciado entre secciones */
}

  
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['username']); ?></h2>

        <!-- Sección para crear proyecto (solo visible para administradores) -->
        <?php if ($rol === 'admin'): ?>
        <div class="text-center mb-4">
            <a href="crear_proyecto.php" class="btn btn-success shadow">Crear Proyecto</a>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Sección de Proyectos (Izquierda) -->
            <div class="col-md-6">
                <div class="card-container">
                    <h3 class="text-center mb-3">Proyectos</h3>
                        <ul class="list-group">
                            <?php foreach ($proyectos as $proyecto) : ?>
                            <li class="list-group-item">
                            <strong><?php echo htmlspecialchars($proyecto['nombre']); ?></strong>
                            <?php if ($rol === 'admin') : ?>
                            <a href="editar_proyecto.php?id=<?php echo $proyecto['id']; ?>"
                                class="btn btn-warning btn-sm float-end">Editar</a>
                            <a href="eliminar_proyecto.php?id=<?php echo $proyecto['id']; ?>"
                            class="btn btn-danger btn-sm float-end me-2">Eliminar</a>
                            <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
          </div>

            <!-- Sección de Tareas (Derecha) -->
            <div class="col-md-6">
                <div class="card-container">
                <h3 class="text-center mb-3">Tareas</h3>

                <!-- Mostrar tareas organizadas por proyecto -->
                <?php foreach ($proyectos as $proyecto) : ?>
                <h4><?php echo htmlspecialchars($proyecto['nombre']); ?></h4>
                <ul class="list-group mb-3">
                    <?php
                    // Filtrar las tareas que pertenecen a este proyecto
                    $tareasProyecto = array_filter($tareas, function($tarea) use ($proyecto) {
                        return $tarea['proyecto_id'] == $proyecto['id'];
                    });

                    // Verificar si hay tareas para este proyecto
                    if (empty($tareasProyecto)) {
                        echo "<li class='list-group-item text-center'>No hay tareas para este proyecto.</li>";
                    } else {
                        foreach ($tareasProyecto as $tarea) :
                            // Mostrar tareas solo si están asignadas al usuario actual o si es admin
                            if ($tarea['asignada_a'] === $_SESSION['usuario']['username'] || $rol === 'admin') :
                    ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($tarea['descripcion']); ?></strong>
                        <span class="badge float-end"><?php echo htmlspecialchars($tarea['estado']); ?></span>
                        <span class="float-end me-2"><?php echo htmlspecialchars($tarea['fecha_limite']); ?></span>
                        <a href="actualizar_tarea.php?id=<?php echo $tarea['id']; ?>"
                            class="btn btn-secondary btn-sm float-end me-1">Actualizar</a>
                    </li>
                    <?php
                            endif;
                        endforeach;
                    }
                    ?>
                </ul>
                <?php endforeach; ?>
            </div>
        </div>
            </div>

        <div class="text-center">
            <a href="logout.php" class="btn btn-danger mt-4 shadow">Cerrar Sesión</a>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
