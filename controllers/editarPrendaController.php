<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Para editar SEGUNDA MANO desde el perfil, basta con estar logueado (Roles 1, 2 y 3).
redirigirSiNoLogueado("../index.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $db = new Database();
    $conexion = $db->conectar();
    require_once '../models/producto.php'; // Aseguramos importar el modelo
    $producto = new Producto($conexion);

    $idUsu = $_SESSION["usuario_id"];
    $idPrenda = $_POST['idPrenda'];
    $nombrePrenda = trim($_POST['nombrePrenda']);
    $precioPrenda = $_POST['precioPrenda'];
    $tallaPrenda = $_POST['tallaPrenda'];
    $colorPrenda = $_POST['colorPrenda']; // Aquí Color
    $tipoPrenda = $_POST['tipoPrenda'];

    // Actualiza usando el ID del usuario, así nadie puede hackear y editar ropa que no es suya
    $actualizado = $producto->actualizarDatosPrendaSegundaMano($idPrenda, $nombrePrenda, $precioPrenda, $tipoPrenda, $idUsu, $colorPrenda, $tallaPrenda);

    if ($actualizado) {
        
        // Borrar fotos antiguas si el cliente las seleccionó
        if (isset($_POST['fotosABorrar']) && is_array($_POST['fotosABorrar'])) {
            foreach ($_POST['fotosABorrar'] as $idFotoBorrar) {
                $producto->borrarImagenPrenda($idFotoBorrar); 
            }
        }

        // Subir las fotos nuevas (Aquí SÍ guardamos en el servidor porque son clientes desde su móvil)
        if (isset($_FILES['fotosNuevas']) && !empty($_FILES['fotosNuevas']['name'][0])) {
            
            $totalFotos = count($_FILES['fotosNuevas']['name']);
            
            for ($i = 0; $i < $totalFotos; $i++) {
                if ($_FILES['fotosNuevas']['error'][$i] === 0) {
                    
                    // Limpiamos el nombre original para evitar problemas de espacios o caracteres raros
                    $nombreOriginalLimpio = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['fotosNuevas']['name'][$i]));
                    $nombreFoto = time() . "-" . $i . "-" . $nombreOriginalLimpio;
                    $rutaMover = "../public/img/" . $nombreFoto;
                    $rutaBaseDatos = "public/img/" . $nombreFoto;

                    if (move_uploaded_file($_FILES['fotosNuevas']['tmp_name'][$i], $rutaMover)) {
                        $producto->anadirImagenPrenda($idPrenda, $colorPrenda, $rutaBaseDatos);
                    }
                }
            }
        }

        header("Location: ../perfil.php?seccion=prendas&mensaje=prenda_actualizada");
        exit;

    } else {
        header("Location: ../perfil.php?seccion=prendas&error=error_actualizar");
        exit;
    }

} else {
    header("Location: ../index.php?error=acceso_denegado");
    exit;
}
?>