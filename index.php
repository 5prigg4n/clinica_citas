<?php
require_once __DIR__ . "/config/session.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Clinica</title>
</head>
<body>
    <h1>
        Sistema de Gestion de Clinica
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="views/logout.php">Cerrar sesion</a>
        <?php endif; ?>
    </h1>

    <hr>

    <?php if (!isset($_SESSION['usuario'])): ?>
        <fieldset>
            <legend>Acceso</legend>
            <a href="views/login.php">Login</a>
        </fieldset>
        
    <?php else: ?>

    <fieldset>
        <legend>Acceso Rapido</legend>
        <ul>
            <li><a href="views/usuarios/listar.php">Usuarios</a></li>
            <li><a href="views/usuarios/crear.php">Nuevo Usuario</a></li>
            <li><a href="views/pacientes/listar.php">Pacientes</a></li>
            <li><a href="views/medicos/listar.php">Medicos</a></li>
            <li><a href="views/citas/listar.php">Citas</a></li>
        </ul>
    </fieldset>

    <br>

    <fieldset>
        <legend>Test de Conexion</legend>
        <a href="public/index.php">Probar Base de Datos</a>
    </fieldset>

    <?php endif; ?>
</body>
</html>
