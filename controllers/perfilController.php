<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/usuario.php";
require_once __DIR__ . "/../models/pedido.php";
require_once __DIR__ . "/../models/favorito.php";
require_once __DIR__ . "/../models/cita.php"; 
require_once __DIR__ . "/../models/producto.php"; 

$db = new Database();
$conexion = $db->conectar();

$user = new Usuario($conexion);
$pedido = new Pedido($conexion);
$favoritoModel = new Favorito($conexion);
$citaModel = new Cita($conexion); 
$producto = new Producto($conexion); 

$idUsuarioSession = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['accion']) && $_POST['accion'] == 'cambiarPass') {
        
        $passActual = isset($_POST["passActual"]) ? $_POST["passActual"] : "";
        $nuevaPass = isset($_POST["nuevaPass"]) ? $_POST["nuevaPass"] : "";
        $nuevaPassConfirmada = isset($_POST["confirmarCambioPass"]) ? $_POST["confirmarCambioPass"] : "";
        
        $datosUsu = $user->obtenerDatosUsu($idUsuarioSession);
        
        if(password_verify($passActual, $datosUsu["password"])){
            if($nuevaPass === $nuevaPassConfirmada){
                $user->cambiarPass( password_hash($nuevaPass, PASSWORD_DEFAULT), $idUsuarioSession);
                
                header("Location: ../perfil.php?mensaje=passActualizada");
                exit;
            } else {
                header("Location: ../perfil.php?error=passNoCoinciden");
                exit;    
            }
        } else {
            header("Location: ../perfil.php?error=passActualFalsa");
            exit;
        }

    } 
    else {
        
        $user->setIdUsuario($idUsuarioSession);
        $user->setNombre(!empty($_POST["nombre"]) ? trim($_POST["nombre"]) : "");
        $user->setApellidos(!empty($_POST["apellidos"]) ? trim($_POST["apellidos"]) : "");
        $user->setTelefono(!empty($_POST["telefono"]) ? trim($_POST["telefono"]) : null);
        $user->setCiudad(!empty($_POST["ciudad"]) ? trim($_POST["ciudad"]) : null);
        $user->setCodigoPostal(!empty($_POST["codigoPostal"]) ? trim($_POST["codigoPostal"]) : null);
        $user->setDireccion(!empty($_POST["direccion"]) ? trim($_POST["direccion"]) : null);

        if($user->actualizarDatosUsu()){
            $_SESSION["nombre"] = $_POST["nombre"];
            header("Location: ../perfil.php?mensaje=perfil_actualizado");
            exit;
        } else {
            header("Location: ../perfil.php?error=perfil_fallo");
            exit;
        }
    }

} 
else {
    $datosUsu = $user->obtenerDatosUsu($idUsuarioSession);
}

$listaPedidos = $pedido->listarPedidos($_SESSION["usuario_id"]);
$listaFavoritos = $favoritoModel->listarFavoritos($_SESSION["usuario_id"]);
$listaCitas = $citaModel->obtenerCitasUsuario($_SESSION["usuario_id"]); 
$listaPrendasUsu = $producto->obtenerMisPrendasSegundaMano($_SESSION["usuario_id"]); 
$listaTallas = $producto->listarTodasTallas();
$listaColores = $producto->listaColores();
$listaTipoPrenda = $producto->listarTiposPrendas();
?>