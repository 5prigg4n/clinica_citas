<?php
require_once "../../config/auth.php";
requireRole(['Administrador']);

require_once "../../config/database.php";
require_once "../../models/Usuario.php";

$db = new Database();
$conn = $db->connect();
$model = new Usuario($conn);

$roles = $model->obtenerRoles();

// Cargar especialidades para medicos
require_once "../../models/Profesional.php";
$profModel = new Profesional($conn);
$especialidades = $profModel->obtenerEspecialidades();
?>

<h2>Crear Usuario</h2>

<a href="listar.php">← Volver a Usuarios</a> | 
<a href="../../index.php">Inicio</a>

<br><br>

<hr>

<fieldset>
<legend>Datos del Usuario</legend>

<form action="../../controllers/GuardarUsuario.php" method="POST">
    Nombre completo:
    <input type="text" name="nombre" required><br><br>

    Nombre de usuario:
    <input type="text" name="nombre_usuario" required maxlength="8">
    <small>Maximo 8 caracteres</small><br><br>

    Correo:
    <input type="email" name="email" required><br><br>

    Password:
    <input type="password" name="password" required><br><br>

    Rol:
    <select name="rol_id" required onchange="mostrarCamposMedico(this.value)">
        <option value="">Seleccione</option>
        <?php foreach ($roles as $rol): ?>
            <option value="<?= $rol['id'] ?>">
                <?= $rol['nombre'] ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <div id="camposMedico" style="display:none; border:1px solid #ccc; padding:10px; margin:10px 0;">
        <h4>Informacion Profesional</h4>
        
        Tarjeta Profesional:
        <input type="text" name="tarjeta_profesional"><br><br>

        Universidad:
        <input type="text" name="universidad"><br><br>

        Especialidades:<br>
        <?php foreach ($especialidades as $especialidad): ?>
            <input type="checkbox" name="especialidades[]" 
                   value="<?= $especialidad['especialidad_id'] ?>" 
                   id="esp_<?= $especialidad['especialidad_id'] ?>">
            <label for="esp_<?= $especialidad['especialidad_id'] ?>">
                <?= $especialidad['descripcion'] ?>
            </label><br>
        <?php endforeach; ?>
    </div>

    <br>
    <button type="submit">Guardar Usuario</button>
</form>

</fieldset>

<script>
function mostrarCamposMedico(rol) {
    var camposMedico = document.getElementById('camposMedico');
    
    if (rol == 3) {
        camposMedico.style.display = 'block';
    } else {
        camposMedico.style.display = 'none';
    }
}
</script>

