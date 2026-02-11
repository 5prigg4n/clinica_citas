<?php
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../models/Paciente.php";

requireRole(['Administrador','Recepcionista']);

if (isset($_GET['tipo']) && isset($_GET['numero'])) {

    $db = new Database();
    $conn = $db->connect();

    $paciente = new Paciente($conn);

    $paciente->eliminar($_GET['tipo'], $_GET['numero']);

    header("Location: ../views/pacientes/listar.php");
}
