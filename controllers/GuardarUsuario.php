<?php
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../models/Usuario.php";

requireRole(['Administrador']);

$db = new Database();
$conn = $db->connect();

$model = new Usuario($conn);

$data = [
    'nombre_usuario' => $_POST['nombre_usuario'],
    'correo_electronico' => $_POST['email'],
    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
    'rol_id' => $_POST['rol_id'],
    'nombre_completo' => $_POST['nombre']
];

$model->crear($data);
$usuario_id = $conn->lastInsertId();

// Si el rol es Medico 
if ($_POST['rol_id'] == 3) {

    require_once "../models/Profesional.php";
    $profModel = new Profesional($conn);

    $profModel->crear([
        'usuario_id' => $usuario_id,
        'tarjeta_profesional' => $_POST['tarjeta_profesional'] ?? '',
        'universidad' => $_POST['universidad'] ?? ''
    ]);

    // Asignar especialidades si se proporcionaron
    if (isset($_POST['especialidades']) && is_array($_POST['especialidades'])) {
        foreach ($_POST['especialidades'] as $especialidad_id) {
            $sql = "INSERT INTO profesionales_especialidades (usuario_id, especialidad_id) 
                    VALUES (:usuario_id, :especialidad_id)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':especialidad_id' => $especialidad_id
            ]);
        }
    }
}

header("Location: ../views/usuarios/listar.php");
exit;
