<?php
require_once "../../config/auth.php";
requireRole(['Administrador']);

require_once "../../config/database.php";
require_once "../../controllers/MedicoController.php";

$db = new Database();
$conn = $db->connect();
$controller = new MedicoController($conn);

$medicos = $controller->listar();

$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Medicos - Clinica</title>
</head>
<body>
    <div>
        <a href="../../index.php">Inicio</a>
        <a href="crear.php">Nuevo Medico</a>
    </div>

    <hr>

    <div>
        <h2>Lista de Medicos</h2>
        
        <?php if ($success == 1): ?>
            <div>Medico creado exitosamente.</div>
        <?php elseif ($success == 2): ?>
            <div>Medico eliminado.</div>
        <?php elseif ($success == 3): ?>
            <div>Medico actualizado exitosamente.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div>Ocurrio un error al procesar la solicitud.</div>
        <?php endif; ?>
    </div>

    <div>
        <form method="GET">
            <input type="text" name="busqueda" placeholder="Buscar por nombre o correo..." 
                   value="<?= $_GET['busqueda'] ?? '' ?>">
            <button type="submit">Buscar</button>
            <a href="listar.php">Limpiar</a>
        </form>
    </div>

    <hr>

    <?php
    if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
        $medicos = $controller->buscar($_GET['busqueda']);
    }
    ?>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Tarjeta Profesional</th>
                <th>Universidad</th>
                <th>Especialidades</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($medicos)): ?>
                <tr>
                    <td colspan="8">No se encontraron medicos.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($medicos as $medico): ?>
                    <tr>
                        <td><?= $medico['nombre_completo'] ?></td>
                        <td><?= $medico['nombre_usuario'] ?></td>
                        <td><?= $medico['correo_electronico'] ?></td>
                        <td><?= $medico['tarjeta_profesional'] ?></td>
                        <td><?= $medico['universidad'] ?></td>
                        <td><?= $medico['especialidades'] ?></td>
                        <td><?= date('d/m/Y', strtotime($medico['fecha_registro'])) ?></td>
                        <td>
                            <a href="editar.php?id=<?= $medico['usuario_id'] ?>">Editar</a>
                            <form method="POST" action="../../controllers/MedicoController.php" 
                                  style="display: inline;" 
                                  onsubmit="return confirm('¿Eliminar este medico?')">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="id" value="<?= $medico['usuario_id'] ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
