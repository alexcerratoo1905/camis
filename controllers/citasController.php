<?php
require_once '../config/db.php';
require_once '../includes/auth.php'; 
require_once '../models/usuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $idUsu = $_SESSION['usuario_id'];
    $db = new Database();
    $coexion = $db->conectar();
    $usu = new Usuario($coexion);
    $datosUsu = $usu->obtenerDatosUsu($idUsu);
    $fecha = $_POST['fecha']; 
    $hora = $_POST['hora']; 
    $motivoForm = $_POST['motivo'];
    $comentarios = isset($_POST['comentarios']) ? trim($_POST['comentarios']) : '';

    $motivoFinal = $motivoForm;
    if (!empty($comentarios)) {
        $motivoFinal .= " | Notas: " . $comentarios;
    }

    $fechaCita = $fecha . ' ' . $hora . ':00';



    $sql = "SELECT COUNT(*) as total FROM citas WHERE fecha_cita = :fechaCita AND estado != 'cancelada'";
    $sentencia = $coexion->prepare($sql);
    $sentencia->execute(["fechaCita" => $fechaCita]);
    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($resultado['total'] >= 10) {
        header("Location: ../citas.php?error=aforo_completo");
        exit();
    }

    $sql = "INSERT INTO citas (usuario_id, fecha_cita, motivo, estado) VALUES (:idUsu, :fechaCita, :motivo, 'confirmada')";
    $sentencia = $coexion->prepare($sql);


    if ($sentencia->execute([
        ":idUsu" => $idUsu,
        ":fechaCita" => $fechaCita,
        ":motivo" => $motivoFinal
    ])) {

        $datosCita = [
            "nombreUsu" => $datosUsu["nombre"],
            "email" => $datosUsu["email"],
            "fecha" => $fecha,
            "hora" => $hora,
            "motivo" => $motivoFinal
            ];

        $config = parse_ini_file(__DIR__ . '/../config/config.ini');
        $urlWebhook = $config['base_url'] . $config['citaNueva'];

        $curl = curl_init($urlWebhook);
        
        curl_setopt($curl, CURLOPT_POST, true); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datosCita)); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        
        curl_setopt($curl, CURLOPT_TIMEOUT, 2); 
        
        curl_exec($curl);

        header("Location: ../reservaConfirmada.php");
        exit();
    } else {
        header("Location: ../citas.php?error=error_bd");
        exit();
    }
}
?>