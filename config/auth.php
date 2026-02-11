<?php
require_once __DIR__ . '/session.php';


function requireLogin(){
if (!isset ($_SESSION['usuario'])){
    header("Location: /clinica_citas/views/login.php");
    exit;
}

}

function requireRole($rolesPermitidos=[]){
requireLogin();

$rolUsuario=$_SESSION['usuario']['rol'];

if(!in_array($rolUsuario,$rolesPermitidos)){
    echo "Acceso denegado";
    exit;
}
}