<?php
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "../models/Usuario.php";

requireRole(['Administrador']);

$db = new Database();
$conn = $db->connect();

$model = new Usuario($conn);
$model->eliminar($_GET['id']);

header("Location: ../views/usuarios/listar.php");
exit;
