<?php

require_once "../config/auth.php";

requireLogin();

$usuario=$_SESSION['usuario'];

?>

<h1>Bienvenido <?=$usuario['nombre_completo']?></h1>
<p>Rol: <?=$usuario['rol']?></p>

<h2>Opciones Disponibles</h2>

<?php if ($usuario['rol'] === 'Administrador'): ?>
    <ul>
        <li><a href="../usuarios/listar.php">Gestion de Usuarios</a></li>
        <li><a href="../pacientes/listar.php">Gestion de Pacientes</a></li>
        <li><a href="../medicos/listar.php">Gestion de Medicos</a></li>
        <li><a href="../citas/listar.php">Agenda de Citas</a></li>
        <li><a href="../reportes/admin.php">Reportes Generales</a></li>
    </ul>

<?php elseif ($usuario['rol'] === 'Recepcionista'): ?>
    <ul>
        <li><a href="../pacientes/listar.php">Gestion de Pacientes</a></li>
        <li><a href="../citas/listar.php">Agenda de Citas</a></li>
        <li><a href="../citas/crear.php">Nueva Cita</a></li>
    </ul>

<?php elseif ($usuario['rol'] === 'Medico'): ?>
    <ul>
        <li><a href="../citas/listar.php">Mis Citas</a></li>
        <li><a href="../citas/reportes.php">Mis Indicadores</a></li>
    </ul>

<?php endif; ?>

<br><br>
<a href="../logout.php">Cerrar sesion</a>