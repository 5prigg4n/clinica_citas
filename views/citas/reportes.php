<?php
require_once "../../config/auth.php";
requireRole(['Medico']);

require_once "../../config/database.php";
require_once "../../controllers/CitaController.php";

$db = new Database();
$conn = $db->connect();
$controller = new CitaController($conn);

$medico_id = $_SESSION['usuario']['usuario_id'];

// Obtener fechas del formulario o usar defaults
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01'); // Primer dia del mes actual
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t'); // ultimo dia del mes actual

$indicadores = $controller->obtenerIndicadores($medico_id, $fecha_inicio, $fecha_fin);
$citas = $controller->listarPorMedico($medico_id, $fecha_inicio, $fecha_fin);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Indicadores - Clinica</title>
</head>
<body>
    <div>
        <a href="../../index.php">Inicio</a>
        <a href="listar.php">Mis Citas</a>
    </div>

    <hr>

    <div>
        <h2>Mis Indicadores de Cumplimiento</h2>
        <p>Periodo: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>

    <div>
        <form method="GET">
            <div>
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" 
                       value="<?= $fecha_inicio ?>" required>
            </div>

            <div>
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" 
                       value="<?= $fecha_fin ?>" required>
            </div>

            <button type="submit">Actualizar</button>
        </form>
    </div>

    <div>
        <div>Total Programadas: <?= $indicadores['total_programadas'] ?></div>
        <div>Total Atendidas: <?= $indicadores['total_atendidas'] ?></div>
        <div>Total No Asistidas: <?= $indicadores['total_no_asistidas'] ?></div>
        <div>% Cumplimiento: <?= $indicadores['porcentaje_cumplimiento'] ?>%</div>
    </div>

    <hr>

    <h3>Detalle de Citas del Periodo</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Especialidad</th>
                <th>Motivo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($citas)): ?>
                <tr>
                    <td colspan="6">No se encontraron citas en este periodo.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                        <td><?= date('H:i', strtotime($cita['hora_cita'])) ?></td>
                        <td><?= $cita['paciente_nombre'] ?></td>
                        <td><?= $cita['especialidad_desc'] ?></td>
                        <td><?= $cita['motivo'] ?></td>
                        <td><?= $cita['estado_desc'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div>
        <h4>Interpretacion de Indicadores</h4>
        <ul>
            <li><strong>% Cumplimiento ≥ 80%:</strong> Excelente desempeño</li>
            <li><strong>% Cumplimiento 60-79%:</strong> Desempeño aceptable</li>
            <li><strong>% Cumplimiento < 60%:</strong> Requiere mejora</li>
        </ul>
    </div>
</body>
</html>
