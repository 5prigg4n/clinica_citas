<?php
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../models/Paciente.php";

requireRole(['Administrador', 'Recepcionista']);

$db = new Database();
$conn = $db->connect();
$model = new Paciente($conn);

$data = [
    ':tipo_documento' => $_POST['tipo_documento'],
    ':numero_documento' => $_POST['numero_documento'],
    ':primer_nombre' => $_POST['primer_nombre'],
    ':segundo_nombre' => $_POST['segundo_nombre'],
    ':primer_apellido' => $_POST['primer_apellido'],
    ':segundo_apellido' => $_POST['segundo_apellido'],
    ':genero_id' => $_POST['genero_id'],
    ':fecha_nacimiento' => $_POST['fecha_nacimiento'],
    ':telefono' => $_POST['telefono'],
    ':correo' => $_POST['correo'],
    ':direccion' => $_POST['direccion']
];

$tipo_original = $_POST['tipo_documento_original'];
$numero_original = $_POST['numero_documento_original'];

if ($model->actualizar($data, $tipo_original, $numero_original)) {
    header("Location: ../views/pacientes/listar.php?success=updated");
} else {
    header("Location: ../views/pacientes/editar.php?tipo=$tipo_original&numero=$numero_original&error=update_failed");
}

exit;
?>
