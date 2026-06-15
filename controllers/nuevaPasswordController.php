<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/usuario.php";

$db = new Database();
$conexion = $db->conectar();
$user = new Usuario($conexion);

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && $_POST["accion"] === "actualizarPassword"){
    
    $token = isset($_POST["token"]) ? $_POST["token"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";

    if(empty($token) || empty($password) || $password !== $confirm_password){
        header("Location: ../nuevaPassword.php?token=" . $token . "&error=datosInvalidos");
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $resultado = $user->actualizarPasswordPorToken($token, $passwordHash);

    if($resultado === "exito"){
        header("Location: ../index.php?mensaje=passwordActualizada");
        exit;
    } else if ($resultado === "caducado") {
        header("Location: ../nuevaPassword.php?token=" . $token . "&error=tokenCaducado");
        exit;
    } else {
        header("Location: ../nuevaPassword.php?token=" . $token . "&error=tokenInvalido");
        exit;
    }
}
?>