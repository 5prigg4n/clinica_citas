<?php
require_once "../../config/auth.php";
requireRole(['Administrador']);

require_once "../../config/database.php";
require_once "../../controllers/MedicoController.php";

$db = new Database();
$conn = $db->connect();
$controller = new MedicoController($conn);

$especialidades = $controller->obtenerEspecialidades();

$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Medico - Clinica</title>
</head>
<body>
    <div>
        <a href="listar.php">← Volver a Medicos</a>
        <a href="../../index.php">Inicio</a>
    </div>

    <h2>Registrar Nuevo Medico</h2>

    <hr>

    <?php if ($error == 1): ?>
        <div>Error al crear el medico. Verifique los datos e intente nuevamente.</div>
    <?php endif; ?>

    <fieldset>
    <legend>Datos del Medico</legend>

    <form method="POST" action="../../controllers/MedicoController.php">
        <input type="hidden" name="action" value="crear">
        
        <div>
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" name="nombre_completo" id="nombre_completo" required>
        </div>

        <div>
            <label for="nombre_usuario">Usuario:</label>
            <input type="text" name="nombre_usuario" id="nombre_usuario" required maxlength="8">
            <small>Maximo 8 caracteres</small>
        </div>

        <div>
            <label for="correo_electronico">Correo Electronico:</label>
            <input type="email" name="correo_electronico" id="correo_electronico" required>
        </div>

        <div>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div>
            <label for="tarjeta_profesional">Tarjeta Profesional:</label>
            <input type="text" name="tarjeta_profesional" id="tarjeta_profesional" required>
        </div>

        <div>
            <label for="universidad">Universidad:</label>
            <input type="text" name="universidad" id="universidad" required>
        </div>

        <div>
            <label>Especialidades:</label>
            <div>
                <?php foreach ($especialidades as $especialidad): ?>
                    <div>
                        <input type="checkbox" name="especialidades[]" 
                               value="<?= $especialidad['especialidad_id'] ?>" 
                               id="esp_<?= $especialidad['especialidad_id'] ?>">
                        <label for="esp_<?= $especialidad['especialidad_id'] ?>">
                            <?= $especialidad['descripcion'] ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit">Registrar Medico</button>
    </form>

    </fieldset>
</body>
</html>
