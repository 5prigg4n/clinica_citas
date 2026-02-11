<?php
require_once "../../config/auth.php";
requireRole(['Administrador']);

require_once "../../config/database.php";
require_once "../../controllers/MedicoController.php";

$db = new Database();
$conn = $db->connect();
$controller = new MedicoController($conn);

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: listar.php");
    exit;
}

$medico = $controller->obtenerPorId($id);
if (!$medico) {
    header("Location: listar.php?error=not_found");
    exit;
}

$especialidades = $controller->obtenerEspecialidades();
$especialidadesMedico = $controller->obtenerEspecialidadesMedico($id);
$especialidadesMedicoIds = array_map(function ($e) {
    return (string)$e['especialidad_id'];
}, $especialidadesMedico);

$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medico - Clinica</title>
</head>
<body>
    <div>
        <a href="listar.php">← Volver a Medicos</a>
        <a href="../../index.php">Inicio</a>
    </div>

    <h2>Editar Medico</h2>

    <hr>

    <?php if ($error): ?>
        <div>Error al actualizar el medico. Verifique los datos e intente nuevamente.</div>
    <?php endif; ?>

    <fieldset>
    <legend>Datos del Medico</legend>

    <form method="POST" action="../../controllers/MedicoController.php">
        <input type="hidden" name="action" value="actualizar">
        <input type="hidden" name="id" value="<?= $medico['usuario_id'] ?>">

        <div>
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" name="nombre_completo" id="nombre_completo" required value="<?= htmlspecialchars($medico['nombre_completo']) ?>">
        </div>

        <div>
            <label for="nombre_usuario">Usuario:</label>
            <input type="text" name="nombre_usuario" id="nombre_usuario" required maxlength="8" value="<?= htmlspecialchars($medico['nombre_usuario']) ?>">
            <small>Maximo 8 caracteres</small>
        </div>

        <div>
            <label for="correo_electronico">Correo Electronico:</label>
            <input type="email" name="correo_electronico" id="correo_electronico" required value="<?= htmlspecialchars($medico['correo_electronico']) ?>">
        </div>

        <div>
            <label for="password">Nueva Contraseña (opcional):</label>
            <input type="password" name="password" id="password">
        </div>

        <div>
            <label for="tarjeta_profesional">Tarjeta Profesional:</label>
            <input type="text" name="tarjeta_profesional" id="tarjeta_profesional" required value="<?= htmlspecialchars($medico['tarjeta_profesional'] ?? '') ?>">
        </div>

        <div>
            <label for="universidad">Universidad:</label>
            <input type="text" name="universidad" id="universidad" required value="<?= htmlspecialchars($medico['universidad'] ?? '') ?>">
        </div>

        <div>
            <label>Especialidades:</label>
            <div>
                <?php foreach ($especialidades as $especialidad): ?>
                    <?php $checked = in_array((string)$especialidad['especialidad_id'], $especialidadesMedicoIds, true); ?>
                    <div>
                        <input type="checkbox" name="especialidades[]"
                               value="<?= $especialidad['especialidad_id'] ?>"
                               id="esp_<?= $especialidad['especialidad_id'] ?>"
                               <?= $checked ? 'checked' : '' ?>>
                        <label for="esp_<?= $especialidad['especialidad_id'] ?>">
                            <?= $especialidad['descripcion'] ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit">Guardar Cambios</button>
    </form>

    </fieldset>
</body>
</html>
