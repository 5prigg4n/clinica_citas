<?php
require_once "../../config/auth.php";
requireRole(['Administrador', 'Recepcionista']);

require_once "../../config/database.php";
require_once "../../models/Paciente.php";

$db = new Database();
$conn = $db->connect();
$model = new Paciente($conn);

$tiposDocumento = $model->listarTiposDocumento();
$generos = $model->listarGeneros();

// Obtener paciente por documento
$tipo_documento = $_GET['tipo'] ?? null;
$numero_documento = $_GET['numero'] ?? null;

if (!$tipo_documento || !$numero_documento) {
    header("Location: listar.php");
    exit;
}

$paciente = $model->buscarPorDocumento($tipo_documento, $numero_documento);

if (!$paciente) {
    header("Location: listar.php?error=not_found");
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : null;
$success = isset($_GET['success']) ? $_GET['success'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente - Clinica</title>
</head>
<body>
    <div>
        <a href="listar.php">← Volver a Pacientes</a>
        <a href="../../index.php">Inicio</a>
    </div>

    <h2>Editar Paciente</h2>

    <hr>

    <?php if ($success): ?>
        <div>Paciente actualizado exitosamente.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div>Error al actualizar el paciente. Intente nuevamente.</div>
    <?php endif; ?>

    <fieldset>
        <legend>Datos del Paciente</legend>

        <form method="POST" action="../../controllers/ActualizarPaciente.php">
            <input type="hidden" name="tipo_documento_original" value="<?= $paciente['tipo_documento'] ?>">
            <input type="hidden" name="numero_documento_original" value="<?= $paciente['numero_documento'] ?>">
            
            <div>
                <label for="tipo_documento">Tipo Documento:</label>
                <select name="tipo_documento" id="tipo_documento" required>
                    <?php foreach ($tiposDocumento as $tipo): ?>
                        <option value="<?= $tipo['tipo_documento'] ?>" 
                                <?= $paciente['tipo_documento'] === $tipo['tipo_documento'] ? 'selected' : '' ?>>
                            <?= $tipo['descripcion'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="numero_documento">Numero Documento:</label>
                <input type="text" name="numero_documento" id="numero_documento" 
                       value="<?= $paciente['numero_documento'] ?>" required>
            </div>

            <div>
                <label for="primer_nombre">Primer Nombre:</label>
                <input type="text" name="primer_nombre" id="primer_nombre" 
                       value="<?= $paciente['primer_nombre'] ?>" required>
            </div>

            <div>
                <label for="segundo_nombre">Segundo Nombre:</label>
                <input type="text" name="segundo_nombre" id="segundo_nombre" 
                       value="<?= $paciente['segundo_nombre'] ?>">
            </div>

            <div>
                <label for="primer_apellido">Primer Apellido:</label>
                <input type="text" name="primer_apellido" id="primer_apellido" 
                       value="<?= $paciente['primer_apellido'] ?>" required>
            </div>

            <div>
                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" name="segundo_apellido" id="segundo_apellido" 
                       value="<?= $paciente['segundo_apellido'] ?>">
            </div>

            <div>
                <label for="genero_id">Genero:</label>
                <select name="genero_id" id="genero_id" required>
                    <?php foreach ($generos as $genero): ?>
                        <option value="<?= $genero['genero_id'] ?>" 
                                <?= $paciente['genero_id'] === $genero['genero_id'] ? 'selected' : '' ?>>
                            <?= $genero['descripcion'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" 
                       value="<?= $paciente['fecha_nacimiento'] ?>" required>
            </div>

            <div>
                <label for="telefono">Telefono:</label>
                <input type="text" name="telefono" id="telefono" 
                       value="<?= $paciente['telefono'] ?>">
            </div>

            <div>
                <label for="correo">Correo:</label>
                <input type="email" name="correo" id="correo" 
                       value="<?= $paciente['correo'] ?>">
            </div>

            <div>
                <label for="direccion">Direccion:</label>
                <textarea name="direccion" id="direccion" rows="3"><?= $paciente['direccion'] ?></textarea>
            </div>

            <button type="submit">Actualizar Paciente</button>
        </form>
    </fieldset>
</body>
</html>