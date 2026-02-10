<?php
require_once "../config/database.php";

$db = new Database();
$conn = $db->connect();

echo "Conexión a PostgreSQL exitosa";