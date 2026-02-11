<?php
require_once "../../config/auth.php";
requireRole(['Administrador', 'Recepcionista', 'Medico']);

require_once "../../config/database.php";
require_once "../../controllers/CitaController.php";

$db = new Database();
$conn = $db->connect();
$controller = new CitaController($conn);

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;
$profesional_id = $_GET['profesional_id'] ?? null;

// Si es medico, solo puede ver sus citas
if ($_SESSION['usuario']['rol'] === 'Medico') {
    $profesional_id = $_SESSION['usuario']['usuario_id'];
}

$citas = $controller->index($fecha_inicio, $fecha_fin, $profesional_id);
$medicos = $controller->getMedicos();
$estados = $controller->getEstados();

$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Citas - Clinica</title>
</head>
<body>
    <div>
        <a href="../../index.php">Inicio</a>
        <?php if (in_array($_SESSION['usuario']['rol'], ['Administrador', 'Recepcionista'])): ?>
            <a href="crear.php">Nueva Cita</a>
        <?php endif; ?>
        <?php if ($_SESSION['usuario']['rol'] === 'Medico'): ?>
            <a href="reportes.php">Mis Indicadores</a>
        <?php endif; ?>
    </div>

    <hr>

    <div>
        <h2>Lista de Citas</h2>
        
        <?php if ($success == 1): ?>
            <div>Cita creada exitosamente.</div>
        <?php elseif ($success == 2): ?>
            <div>Estado de cita actualizado.</div>
        <?php elseif ($success == 3): ?>
            <div>Cita eliminada.</div>
        <?php elseif ($success == 4): ?>
            <div>Cita actualizada exitosamente.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div>Ocurrio un error al procesar la solicitud.</div>
        <?php endif; ?>
    </div>

    <div>
        <form method="GET">
            <div>
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" 
                       value="<?= $fecha_inicio ?>">
            </div>

            <div>
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" 
                       value="<?= $fecha_fin ?>">
            </div>

            <?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
                <div>
                    <label for="profesional_id">Medico:</label>
                    <select name="profesional_id" id="profesional_id">
                        <option value="">Todos los medicos</option>
                        <?php foreach ($medicos as $medico): ?>
                            <option value="<?= $medico['usuario_id'] ?>" 
                                    <?= $profesional_id == $medico['usuario_id'] ? 'selected' : '' ?>>
                                <?= $medico['nombre_completo'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit">Filtrar</button>
            <a href="listar.php">Limpiar</a>
        </form>
    </div>

    <hr>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Medico</th>
                <th>Especialidad</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($citas)): ?>
                <tr>
                    <td colspan="8">No se encontraron citas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                        <td><?= date('H:i', strtotime($cita['hora_cita'])) ?></td>
                        <td><?= $cita['paciente_nombre'] ?></td>
                        <td><?= $cita['profesional_nombre'] ?></td>
                        <td><?= $cita['especialidad_desc'] ?></td>
                        <td><?= $cita['motivo'] ?></td>
                        <td><?= $cita['estado_desc'] ?></td>
                        <td>
                            <?php if ($_SESSION['usuario']['rol'] === 'Medico' && 
                                     $cita['sw_estado'] == 1): ?>
                                <form method="POST" action="../../controllers/CitaController.php" 
                                      style="display: inline;">
                                    <input type="hidden" name="action" value="actualizar_estado">
                                    <input type="hidden" name="cita_id" value="<?= $cita['cita_id'] ?>">
                                    <select name="estado_id" required>
                                        <option value="">Cambiar estado</option>
                                        <option value="2">Atendida</option>
                                        <option value="3">No asiste</option>
                                    </select>
                                    <button type="submit">Actualizar</button>
                                </form>
                            <?php endif; ?>

                            <?php if (in_array($_SESSION['usuario']['rol'], ['Administrador', 'Recepcionista'])): ?>
                                <a href="editar.php?id=<?= $cita['cita_id'] ?>">Editar</a>
                                <form method="POST" action="../../controllers/CitaController.php" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('¿Eliminar esta cita?')">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="cita_id" value="<?= $cita['cita_id'] ?>">
                                    <button type="submit">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
