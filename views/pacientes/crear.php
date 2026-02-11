<?php
require_once "../../config/database.php";
require_once "../../config/auth.php";
require_once "../../models/Paciente.php";

requireRole(['Administrador','Recepcionista']);

$db = new Database();
$conn = $db->connect();

$pacienteModel = new Paciente($conn);

$tiposDocumento = $pacienteModel->listarTiposDocumento();
$generos = $pacienteModel->listarGeneros();?>

<h2>Crear Paciente</h2>

<a href="../../index.php">Inicio</a>
<br><br>

<fieldset>
<legend>Datos del Paciente</legend>

<form method="POST" action="../../controllers/pacienteStore.php">
Tipo de documento:
<select name="tipo_documento" required>
    <option value="">Seleccione...</option>
    <?php foreach ($tiposDocumento as $tipo): ?>
        <option value="<?= $tipo['tipo_documento'] ?>">
            <?= $tipo['descripcion'] ?>
        </option>
    <?php endforeach; ?>
</select><br>
    Numero Documento:
    <input type="text" name="numero_documento" required><br>

    Primer Nombre:
    <input type="text" name="primer_nombre" required><br>

    Primer Apellido:
    <input type="text" name="primer_apellido" required><br>
   Genero:
<select name="genero_id" required>
    <option value="">Seleccione...</option>
    <?php foreach ($generos as $g): ?>
        <option value="<?= $g['genero_id'] ?>">
            <?= $g['descripcion'] ?>
        </option>
    <?php endforeach; ?>
</select><br>

    Fecha Nacimiento:
    <input type="date" name="fecha_nacimiento" required><br>

    Telefono:
    <input type="text" name="telefono"><br>

    Correo:
    <input type="email" name="correo"><br>

    Direccion:
    <textarea name="direccion"></textarea><br>

    <button type="submit">Guardar</button>

</form>

</fieldset>
