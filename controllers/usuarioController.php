<?php

session_start();



require_once '../config/db.php';
require_once '../models/usuario.php';

$db = new Database();
$usuario = new Usuario($db->conectar());

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['accion'] == 'registro') {

        $nombreUsu = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
        $apellidosUsu = isset($_POST["apellidos"]) ? $_POST["apellidos"] : "";
        $correoUsu = isset($_POST["email"]) ? $_POST["email"] : "";
        $pasUsu = isset($_POST["password"]) ? $_POST["password"] : "";

        if (strlen($pasUsu) < 8 || !preg_match("#[0-9]+#", $pasUsu) || !preg_match("#[A-Z]+#", $pasUsu) || !preg_match("#[a-z]+#", $pasUsu)) {
            header("Location: ../index.php?error=password_debil");
            exit;
        }

        $usuario->setNombre($nombreUsu);
        $usuario->setApellidos($apellidosUsu);
        $usuario->setEmail($correoUsu);
        $usuario->setPassword($pasUsu);

        $usuario->setIdRol(2);

        if ($usuario->registrar()) {
            header("Location: ../index.php?mensaje=registro_exito");
            exit;
        } else {
            header("Location: ../index.php?error=registro_fallo");
            exit;
        }
    } elseif ($_POST['accion'] == 'login') {

        $email = isset($_POST["email"]) ? $_POST["email"] : "";
        $passwordUsu = isset($_POST["password"]) ? $_POST["password"] : 0;

        $datosUsu = $usuario->login($email, $passwordUsu);

        if ($datosUsu) {

            $_SESSION["usuario_id"] = $datosUsu["id"];
            $_SESSION["nombre"] = $datosUsu["nombre"];
            $_SESSION["rol_id"] = $datosUsu["rol_id"];

            header("Location:../index.php?bienvenido=true");
            exit;
        } else {
            header("Location:../index.php?bienvenido=false");
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['accion']) && $_GET['accion'] == 'logout') {

    session_unset();

    session_destroy();
    
    header("Location: ../index.php?sesionCerrada=true");
    exit;
}
