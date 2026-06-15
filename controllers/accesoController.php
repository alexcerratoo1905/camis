<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php?error=debes_iniciar_sesion");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo']) && !empty($_POST['codigo'])) {

    require_once __DIR__ . "/../config/db.php";
    require_once __DIR__ . "/../models/usuario.php";

    $db = new Database();
    $conexion = $db->conectar();
    
    $user = new Usuario($conexion);
    $idUsuarioSession = $_SESSION["usuario_id"];
    $datosUsu = $user->obtenerDatosUsu($idUsuarioSession);
    $emailUsuario = $datosUsu['email'];

    $codigo = trim($_POST["codigo"]);
    
    $sql = "SELECT * FROM codigos_accesos WHERE codigo = :codigo AND email = :email AND usado = 0 AND tipo = 'acceso' ";
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute([
        ":codigo" => $codigo,
        ":email" => $emailUsuario 
    ]);

    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $sql =  "UPDATE codigos_accesos SET usado = 1 WHERE codigo = :codigo";
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute(["codigo" => $codigo]);

        $_SESSION["acceso"] = true;

        header("Location: ../catalogo.php?especial=herror");
        exit();

    } else {
        header("Location: ../index.php?error=codigo_invalido");
        exit();
    }

} else {
    header("Location: ../index.php");
    exit();
}//
?>