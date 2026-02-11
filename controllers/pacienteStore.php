<?php
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../models/Paciente.php";

requireRole(['Administrador','Recepcionista']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['fecha_nacimiento'] > date('Y-m-d')) {
        die("La fecha de nacimiento no puede ser futura.");
    }

    $db = new Database();
    $conn = $db->connect();

    $paciente = new Paciente($conn);

    $data = [
        ':tipo_documento' => $_POST['tipo_documento'],
        ':numero_documento' => $_POST['numero_documento'],
        ':primer_nombre' => $_POST['primer_nombre'],
        ':segundo_nombre' => $_POST['segundo_nombre'] ?? null,
        ':primer_apellido' => $_POST['primer_apellido'],
        ':segundo_apellido' => $_POST['segundo_apellido'] ?? null,
        ':genero_id' => $_POST['genero_id'] ?? null,
        ':fecha_nacimiento' => $_POST['fecha_nacimiento'],
        ':telefono' => $_POST['telefono'],
        ':correo' => $_POST['correo'],
        ':direccion' => $_POST['direccion']
    ];

    $paciente->crear($data);

    header("Location: ../views/pacientes/listar.php");
}
