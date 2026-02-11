<?php
require_once "../../config/auth.php";
requireRole(['Administrador', 'Recepcionista']);

require_once "../../config/database.php";
require_once "../../controllers/CitaController.php";

$db = new Database();
$conn = $db->connect();
$controller = new CitaController($conn);

$pacientes = $controller->getPacientes();
$medicos = $controller->getMedicos();
$especialidades = $controller->getEspecialidades();

$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cita - Clinica</title>
</head>
<body>
    <div>
        <a href="listar.php">← Volver a Citas</a>
        <a href="../../index.php">Inicio</a>
    </div>

    <h2>Agendar Nueva Cita</h2>

    <hr>

    <?php if ($error == 1): ?>
        <div>Error al crear la cita. Verifique los datos e intente nuevamente.</div>
    <?php endif; ?>

    <fieldset>
    <legend>Datos de la Cita</legend>

    <form method="POST" action="../../controllers/CitaController.php">
        <input type="hidden" name="action" value="crear">
        
        <div>
            <label for="paciente">Paciente:</label>
            <select name="id_paciente" id="paciente" required>
                <option value="">Seleccione un paciente</option>
                <?php foreach ($pacientes as $paciente): ?>
                    <option value="<?= $paciente['tipo_documento'] ?>" 
                            data-documento="<?= $paciente['numero_documento'] ?>">
                        <?= $paciente['primer_nombre'] . ' ' . $paciente['primer_apellido'] ?> 
                        (<?= $paciente['tipo_documento'] . ' - ' . $paciente['numero_documento'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="documento_paciente" id="documento_paciente" required>
        </div>

        <div>
            <label for="id_profesional">Medico:</label>
            <select name="id_profesional" id="id_profesional" required>
                <option value="">Seleccione un medico</option>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?= $medico['usuario_id'] ?>">
                        <?= $medico['nombre_completo'] ?>
                        <?php if ($medico['especialidad']): ?>
                            - <?= $medico['especialidad'] ?>
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="especialidad_id">Especialidad:</label>
            <select name="especialidad_id" id="especialidad_id" required>
                <option value="">Seleccione una especialidad</option>
                <?php foreach ($especialidades as $especialidad): ?>
                    <option value="<?= $especialidad['especialidad_id'] ?>">
                        <?= $especialidad['descripcion'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="fecha_cita">Fecha de la Cita:</label>
            <input type="date" name="fecha_cita" id="fecha_cita" required 
                   min="<?= date('Y-m-d') ?>">
        </div>

        <div>
            <label for="hora_cita">Hora de la Cita:</label>
            <input type="time" name="hora_cita" id="hora_cita" required>
        </div>

        <div>
            <label for="motivo">Motivo de la Consulta:</label>
            <textarea name="motivo" id="motivo" rows="4" required></textarea>
        </div>

        <button type="submit">Agendar Cita</button>
    </form>

    </fieldset>

    <script>
        document.getElementById('paciente').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var documentoInput = document.getElementById('documento_paciente');
            
            if (selectedOption.value) {
                documentoInput.value = selectedOption.getAttribute('data-documento');
            } else {
                documentoInput.value = '';
            }
        });
    </script>
</body>
</html>
