<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// SI ES UN POST (El usuario le ha dado a un botón de guardar)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['accion']) && $_POST['accion'] == 'cambiarPass') {
        
        $passActual = isset($_POST["passActual"]) ? $_POST["passActual"] : "";
        $nuevaPass = isset($_POST["nuevaPass"]) ? $_POST["nuevaPass"] : "";
        $nuevaPassConfirmada = isset($_POST["confirmarCambioPass"]) ? $_POST["confirmarCambioPass"] : "";
        
        $datosUsu = $user->obtenerDatosUsu($idUsuarioSession);
        
        if(password_verify($passActual, $datosUsu["password"])){
            if($nuevaPass === $nuevaPassConfirmada){
                $user->cambiarPass( password_hash($nuevaPass, PASSWORD_DEFAULT), $idUsuarioSession);
                header("Location: ../perfil.php?seccion=datos&mensaje=passActualizada");
                exit;
            } else {
                header("Location: ../perfil.php?seccion=datos&error=passNoCoinciden");
                exit;    
            }
        } else {
            header("Location: ../perfil.php?seccion=datos&error=passActualFalsa");
            exit;
        }

    } 
    else {
        // ACTUALIZAR DATOS PERSONALES Y DIRECCIÓN DE ENVÍO
        $nombre = !empty($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
        $apellidos = !empty($_POST["apellidos"]) ? trim($_POST["apellidos"]) : "";
        $telefono = !empty($_POST["telefono"]) ? trim($_POST["telefono"]) : null;
        $direccion = !empty($_POST["direccion"]) ? trim($_POST["direccion"]) : null;
        $ciudad = !empty($_POST["ciudad"]) ? trim($_POST["ciudad"]) : null;
        $cp = !empty($_POST["codigoPostal"]) ? trim($_POST["codigoPostal"]) : null;
        
        // Los nuevos campos para el Checkout automático
        $provincia = !empty($_POST["provincia"]) ? trim($_POST["provincia"]) : null;
        $ca = !empty($_POST["comunidad_autonoma"]) ? trim($_POST["comunidad_autonoma"]) : null;
        $pais = !empty($_POST["pais"]) ? trim($_POST["pais"]) : 'España';

        // Usamos consulta PDO directa para actualizar todo de golpe y no tener que tocar Usuario.php
        $sql = "UPDATE usuarios SET nombre=?, apellidos=?, telefono=?, direccion=?, ciudad=?, codigo_postal=?, provincia=?, comunidad_autonoma=?, pais=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        
        if($stmt->execute([$nombre, $apellidos, $telefono, $direccion, $ciudad, $cp, $provincia, $ca, $pais, $idUsuarioSession])){
            $_SESSION["nombre"] = $nombre;
            header("Location: ../perfil.php?seccion=datos&mensaje=perfil_actualizado");
            exit;
        } else {
            header("Location: ../perfil.php?seccion=datos&error=perfil_fallo");
            exit;
        }
    }

} 
// SI ES UN GET (El usuario solo está navegando por la página)
else {
    // Solo cargamos los datos, SIN HACER REDIRECCIONES PARA EVITAR EL BUCLE
    $datosUsu = $user->obtenerDatosUsu($idUsuarioSession);
}

// Carga de todas las listas que necesita perfil.php para pintarse
$listaPedidos = $pedido->listarPedidos($_SESSION["usuario_id"]);
$listaFavoritos = $favoritoModel->listarFavoritos($_SESSION["usuario_id"]);
$listaCitas = $citaModel->obtenerCitasUsuario($_SESSION["usuario_id"]); 
$listaPrendasUsu = $producto->obtenerMisPrendasSegundaMano($_SESSION["usuario_id"]); 
$listaTallas = $producto->listarTodasTallas();
$listaColores = $producto->listaColores();
$listaTipoPrenda = $producto->listarTiposPrendas();
?>