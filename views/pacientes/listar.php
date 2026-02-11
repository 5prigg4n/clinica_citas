<?php
require_once "../../config/auth.php";
requireRole(['Administrador','Recepcionista']);

require_once "../../config/database.php";
require_once "../../models/Paciente.php";

$db = new Database();
$conn = $db->connect();
$model = new Paciente($conn);
$tiposDocumento = $model->listarTiposDocumento();

$pacientes = [];
$mensaje = null;

// Si viene busqueda
if (!empty($_GET['tipo']) && !empty($_GET['numero'])) {

    $paciente = $model->buscarPorDocumento(
        $_GET['tipo'],
        $_GET['numero']
    );

    if ($paciente) {
        $pacientes = [$paciente]; // Solo ese paciente
    } else {
        $mensaje = "Paciente no encontrado.";
    }

} else {
    // Si no hay busqueda, listar todos
    $pacientes = $model->listar();
}
?>


<h2>Lista de Pacientes</h2>
<a href="../../index.php">Inicio</a>
<br><br>
<button><a href="crear.php">Nuevo Paciente</a></button>

<hr>

<form method="GET">
    Tipo Documento:
    <select name="tipo">
        <option value="">Seleccione</option>
        <?php foreach ($tiposDocumento as $tipo): ?>
            <option value="<?= $tipo['tipo_documento'] ?>">
                <?= $tipo['descripcion'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    Numero Documento:
    <input type="text" name="numero">

    <button type="submit">Buscar</button>
</form>
<hr>

<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>Nombre</th>
        <th>Documento</th>
        
        <th>Telefono</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($pacientes as $p): ?>
        <tr>
            <td>
                <?= $p['primer_nombre'] . " " . $p['primer_apellido'] ?>
            </td>
            <td>
                <?= $p['tipo_documento'] . " - " . $p['numero_documento'] ?>
            </td>
            <td><?= $p['telefono'] ?></td>
            <td>
                <a href="editar.php?tipo=<?= $p['tipo_documento'] ?>&numero=<?= $p['numero_documento'] ?>">Editar</a>
                <a href="../../controllers/EliminarPaciente.php?tipo=<?= $p['tipo_documento'] ?>&numero=<?= $p['numero_documento'] ?>"
                   onclick="return confirm('¿Seguro que deseas eliminar este paciente?')">
                   Eliminar
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
