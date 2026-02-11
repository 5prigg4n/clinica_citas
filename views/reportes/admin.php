<?php
require_once "../../config/auth.php";
requireRole(['Administrador']);

require_once "../../config/database.php";
require_once "../../controllers/CitaController.php";

$db = new Database();
$conn = $db->connect();
$controller = new CitaController($conn);

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
$profesional_id = $_GET['profesional_id'] ?? null;

$medicos = $controller->getMedicos();
$estadisticas_generales = [];
$estadisticas_medicos = [];

// Estadisticas generales
$sql_general = "SELECT 
                    COUNT(*) as total_citas,
                    SUM(CASE WHEN sw_estado = 1 THEN 1 ELSE 0 END) as programadas,
                    SUM(CASE WHEN sw_estado = 2 THEN 1 ELSE 0 END) as atendidas,
                    SUM(CASE WHEN sw_estado = 3 THEN 1 ELSE 0 END) as no_asistidas
                FROM citas 
                WHERE fecha_cita BETWEEN :fecha_inicio AND :fecha_fin";

$stmt_general = $conn->prepare($sql_general);
$stmt_general->execute([
    ':fecha_inicio' => $fecha_inicio,
    ':fecha_fin' => $fecha_fin
]);
$estadisticas_generales = $stmt_general->fetch(PDO::FETCH_ASSOC);

$estadisticas_generales['porcentaje_cumplimiento'] = $estadisticas_generales['total_citas'] > 0 
    ? round(($estadisticas_generales['atendidas'] / $estadisticas_generales['total_citas']) * 100, 2)
    : 0;

// Estadisticas por medico
if ($profesional_id) {
    $estadisticas_medicos[] = $controller->obtenerIndicadores($profesional_id, $fecha_inicio, $fecha_fin);
} else {
    foreach ($medicos as $medico) {
        $indicadores = $controller->obtenerIndicadores($medico['usuario_id'], $fecha_inicio, $fecha_fin);
        $indicadores['nombre_medico'] = $medico['nombre_completo'];
        $indicadores['especialidad'] = $medico['especialidad'];
        $estadisticas_medicos[] = $indicadores;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Generales - Clinica</title>
</head>
<body>
    <div>
        <a href="../../index.php">Inicio</a>
        <a href="../citas/listar.php">Citas</a>
    </div>

    <hr>

    <div class="header">
        <h2>Reportes Generales de la Clinica</h2>
        <p>Periodo: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
    </div>

    <div class="filters">
        <form method="GET">
            <div class="filter-group">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" 
                       value="<?= $fecha_inicio ?>" required>
            </div>

            <div class="filter-group">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" 
                       value="<?= $fecha_fin ?>" required>
            </div>

            <div class="filter-group">
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

            <button type="submit">Filtrar</button>
            <a href="admin.php">Limpiar</a>
        </form>
    </div>

    <h3>Resumen General</h3>
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-number"><?= $estadisticas_generales['total_citas'] ?></div>
            <div class="summary-label">Total Citas</div>
        </div>

        <div class="summary-card">
            <div class="summary-number"><?= $estadisticas_generales['programadas'] ?></div>
            <div class="summary-label">Programadas</div>
        </div>

        <div class="summary-card">
            <div class="summary-number"><?= $estadisticas_generales['atendidas'] ?></div>
            <div class="summary-label">Atendidas</div>
        </div>

        <div class="summary-card">
            <div class="summary-number"><?= $estadisticas_generales['no_asistidas'] ?></div>
            <div class="summary-label">No Asistidas</div>
        </div>

        <div class="summary-card">
            <div class="percentage <?= 
                $estadisticas_generales['porcentaje_cumplimiento'] >= 80 ? 'high' : 
                ($estadisticas_generales['porcentaje_cumplimiento'] >= 60 ? 'medium' : 'low') ?>">
                <?= $estadisticas_generales['porcentaje_cumplimiento'] ?>%
            </div>
            <div class="summary-label">% Cumplimiento</div>
        </div>
    </div>

    <h3>Indicadores por Medico</h3>
    <table>
        <thead>
            <tr>
                <th>Medico</th>
                <th>Especialidad</th>
                <th>Total Programadas</th>
                <th>Total Atendidas</th>
                <th>Total No Asistidas</th>
                <th>% Cumplimiento</th>
                <th>Rendimiento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estadisticas_medicos as $stats): ?>
                <tr>
                    <td><?= $stats['nombre_medico'] ?></td>
                    <td><?= $stats['especialidad'] ?? 'N/A' ?></td>
                    <td><?= $stats['total_programadas'] ?></td>
                    <td><?= $stats['total_atendidas'] ?></td>
                    <td><?= $stats['total_no_asistidas'] ?></td>
                    <td><?= $stats['porcentaje_cumplimiento'] ?>%</td>
                    <td>
                        <?php
                        $rendimiento = $stats['porcentaje_cumplimiento'] >= 80 ? 'Excelente' : 
                                     ($stats['porcentaje_cumplimiento'] >= 60 ? 'Bueno' : 'Mejorable');
                        $color = $stats['porcentaje_cumplimiento'] >= 80 ? '#28a745' : 
                                ($stats['porcentaje_cumplimiento'] >= 60 ? '#ffc107' : '#dc3545');
                        ?>
                        <span style="color: <?= $color ?>; font-weight: bold;">
                            <?= $rendimiento ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <h4>Leyenda de Rendimiento</h4>
        <ul>
            <li><strong>Excelente (≥80%):</strong> Medico con alto cumplimiento de citas</li>
            <li><strong>Bueno (60-79%):</strong> Medico con cumplimiento aceptable</li>
            <li><strong>Mejorable (<60%):</strong> Medico que requiere atencion especial</li>
        </ul>
    </div>
</body>
</html>
