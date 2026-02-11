<?php

require_once "../config/database.php";
require_once "../config/session.php";

if($_SERVER ['REQUEST_METHOD']==='POST'){

$nombre_usuario=$_POST['nombre_usuario'];
$password=$_POST['password'];

$db= new Database();
$conn= $db->connect();


$sql= "SELECT u.*, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id=r.id
        WHERE u.nombre_usuario=:nombre_usuario";

$stmt=$conn->prepare($sql);
$stmt->bindParam(':nombre_usuario',$nombre_usuario);
$stmt->execute();

$usuario= $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && password_verify($password,$usuario['password'])){
$_SESSION['usuario']=[

    'usuario_id'=>$usuario['usuario_id'],
    'nombre_usuario'=>$usuario['nombre_usuario'],
    'correo_electronico'=>$usuario['correo_electronico'],
    'rol'=>$usuario['rol'],
    'nombre_completo'=>$usuario['nombre_completo']

];
header("Location: ../index.php");
exit;

}else{
    echo "La contraseña o el usuario son incorrectos";
}
}