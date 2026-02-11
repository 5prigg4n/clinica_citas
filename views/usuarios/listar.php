<?php
require_once "../../config/auth.php";
requireRole(['Administrador']);

require_once "../../config/database.php";
require_once "../../models/Usuario.php";

$db = new Database();
$conn = $db->connect();
$model = new Usuario($conn);

$usuarios = $model->listar();
?>

<h2>Usuarios</h2>
<a href="../../index.php">Inicio</a>
<br><br>
<a href="crear.php">Nuevo Usuario</a>

<hr>

<table border="1" cellpadding="6" cellspacing="0">
<tr>
    <th>Nombre</th>
    <th>Email</th>
    <th>Rol</th>
    <th>Acciones</th>
</tr>

<?php foreach ($usuarios as $u): ?>
<tr>
    <td><?= $u['nombre_completo'] ?></td>
    <td><?= $u['correo_electronico'] ?></td>
    <td><?= $u['rol_nombre'] ?></td>
    <td>
        <a href="../../controllers/EliminarUsuario.php?id=<?= $u['usuario_id'] ?>"
           onclick="return confirm('¿Eliminar usuario?')">
           Eliminar
        </a>
    </td>
</tr>
<?php endforeach; ?>
</table>
