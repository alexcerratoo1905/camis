<?php
session_start();

require_once '../includes/auth.php';
redirigirSiNoLogueado("../index.php");
restringirAccesoA($_SESSION["rol_id"], [1, 3], "../index.php");

$esSuperAdmin = ($_SESSION["rol_id"] == 1);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/pedido.php";
require_once __DIR__ . "/../models/producto.php";
require_once __DIR__ . "/../models/usuario.php";
require_once __DIR__ . "/../models/look.php";

$db = new Database();
$conexion = $db->conectar();
$pedido = new Pedido($conexion);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idPedido = isset($_POST["idPedido"]) ? $_POST["idPedido"] : 0;
    $nuevoEstado = isset($_POST["nuevoEstado"]) ? trim($_POST["nuevoEstado"]) : "";
    $accion = isset($_POST["accion"]) ? $_POST["accion"] : "";

    switch ($accion) {
        
        // ==============================================
        // NUEVA SUBIDA DE CAMISETAS CON FOTOS FÍSICAS MÚLTIPLES
        // ==============================================
        case 'crearPrendaTienda':
            $nombre = trim($_POST['nombre']);
            $precio = $_POST['precio'];
            $coleccion_id = $_POST['coleccion_id']; // Liga
            $descripcion = $_POST['descripcion'] ?? '';
            $equipacion = $_POST['equipacion']; // Local, Visitante...

            try {
                $conexion->beginTransaction();

                // 1. Gestionar Color/Equipación
                $stmtColor = $conexion->prepare("SELECT id FROM colores WHERE nombre = ?");
                $stmtColor->execute([$equipacion]);
                $colorRow = $stmtColor->fetch(PDO::FETCH_ASSOC);

                if ($colorRow) {
                    $color_id = $colorRow['id'];
                } else {
                    $stmtNuevoColor = $conexion->prepare("INSERT INTO colores (nombre, valor_hexadecimal) VALUES (?, '#000000')");
                    $stmtNuevoColor->execute([$equipacion]);
                    $color_id = $conexion->lastInsertId();
                }

                // 2. Insertar Producto Base
                $sqlInsertProd = "INSERT INTO productos (nombre, precio, descripcion, coleccion_id, genero, tipo_id, activo) 
                                  VALUES (?, ?, ?, ?, 3, 1, 1)";
                $stmtProd = $conexion->prepare($sqlInsertProd);
                $stmtProd->execute([$nombre, $precio, $descripcion, $coleccion_id]);
                
                $producto_id = $conexion->lastInsertId();

                // 3. Vincular Producto y Color
                $stmtProdColor = $conexion->prepare("INSERT INTO producto_colores (producto_id, color_id) VALUES (?, ?)");
                $stmtProdColor->execute([$producto_id, $color_id]);

                // 4. SUBIDA MÚLTIPLE DE IMÁGENES FÍSICAS AL SERVIDOR
                if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                    $totalImagenes = count($_FILES['imagenes']['name']);
                    $rutaDirectorio = __DIR__ . '/../public/img/';
                    
                    for ($i = 0; $i < $totalImagenes; $i++) {
                        $errorSubida = $_FILES['imagenes']['error'][$i];
                        
                        if ($errorSubida === UPLOAD_ERR_OK) {
                            $nombreOriginal = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['imagenes']['name'][$i]));
                            $nombreArchivo = time() . '_' . $i . '_' . $nombreOriginal;
                            
                            // Movemos la foto al disco duro
                            if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $rutaDirectorio . $nombreArchivo)) {
                                $urlBD = 'public/img/' . $nombreArchivo;
                                $es_principal = ($i === 0) ? 1 : 0; // La primera es la principal
                                
                                $stmtImg = $conexion->prepare("INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) VALUES (?, ?, ?, ?)");
                                $stmtImg->execute([$producto_id, $color_id, $urlBD, $es_principal]);
                            }
                        } elseif ($errorSubida === UPLOAD_ERR_INI_SIZE || $errorSubida === UPLOAD_ERR_FORM_SIZE) {
                            throw new Exception("La imagen " . htmlspecialchars($_FILES['imagenes']['name'][$i]) . " pesa demasiado. Reduce su tamaño o sube el límite de PHP.");
                        }
                    }
                } else {
                    throw new Exception("Debes subir al menos una imagen del producto.");
                }

                $conexion->commit();
                header("Location: ../admin/admin.php?seccion=productos&mensaje=prenda_subida");
                exit();

            } catch (Exception $e) {
                $conexion->rollBack();
                die("<div style='padding:30px; border:2px solid red; background:#ffeeee;'><h2>Error</h2><p>" . $e->getMessage() . "</p><button onclick='window.history.back()'>Volver Atrás</button></div>");
            }
            break;

        // --- ZONA BLOQUEADA SOLO PARA TI (SUPERADMIN) ---
        case "cambiarEstadoPedido":
            if (!$esSuperAdmin) die("No tienes permisos para tocar pedidos.");
            $pedido->actualizarEstadoPedido($idPedido, $nuevoEstado);
            header("Location: ../admin/admin.php?seccion=pedidos&mensaje=estado_actualizado");
            break;

        case "actualizarRol":
            if (!$esSuperAdmin) die("No tienes permisos para gestionar roles.");
            $idUsuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
            $nuevoRol = isset($_POST['nuevo_rol']) ? (int)$_POST['nuevo_rol'] : 2; 
            if ($idUsuario > 0) {
                $userObj = new Usuario($conexion);
                $userObj->actualizarRolUsuario($idUsuario, $nuevoRol);
                header("Location: ../admin/admin.php?seccion=usuarios&mensaje=rol_actualizado");
            } else {
                header("Location: ../admin/admin.php?seccion=usuarios&error=usuario_invalido");
            }
            break;

        // --- ZONA ABIERTA PARA TI Y TU AMIGO (CATÁLOGO) ---
        case "actualizarInventarioMasivo":
            $stocks = isset($_POST['stock']) ? $_POST['stock'] : [];
            $rebajas = isset($_POST['rebaja']) ? $_POST['rebaja'] : [];
            $estados = isset($_POST['activo']) ? $_POST['activo'] : [];
            $precios = isset($_POST['precio']) ? $_POST['precio'] : []; 
            $colecciones = isset($_POST['coleccion']) ? $_POST['coleccion'] : []; 
            $pagRetorno = isset($_POST['pagina_retorno']) ? $_POST['pagina_retorno'] : 1;

            $prodObj = new Producto($conexion);

            foreach ($rebajas as $idPrenda => $valorRebaja) {
                $estadoActivo = $estados[$idPrenda];
                $precioActualizado = isset($precios[$idPrenda]) ? $precios[$idPrenda] : null;
                $coleccionActualizada = isset($colecciones[$idPrenda]) ? $colecciones[$idPrenda] : null; 
                
                $prodObj->actualizarDatosBasicosPrenda($idPrenda, $valorRebaja, $estadoActivo, $precioActualizado, $coleccionActualizada);
            }

            header("Location: ../admin/admin.php?seccion=productos&pagina=$pagRetorno&mensaje=inventario_actualizado");
            break;

        case "actualizarSegundaMano":
            if (isset($_POST['revision']) && isset($_POST['vendedor'])) {
                $producto = new Producto($conexion); 
                foreach ($_POST['revision'] as $idPrenda => $estado) {
                    $idVendedor = $_POST['vendedor'][$idPrenda];
                    $producto->actualizarRevisionSegundaMano($idPrenda, $estado, $idVendedor);
                }
            }
            header("Location: ../admin/admin.php?seccion=segundaMano&mensaje=estado_actualizado");
            break;
            
        case "crearColeccion":
            $nombre = isset($_POST['nombre_coleccion']) ? trim($_POST['nombre_coleccion']) : "";
            $descripcion = isset($_POST['descripcion_coleccion']) ? trim($_POST['descripcion_coleccion']) : "";
            
            if (!empty($nombre)) {
                $prodObj = new Producto($conexion);
                $prodObj->crearColeccion($nombre, $descripcion);
                header("Location: ../admin/admin.php?seccion=colecciones&mensaje=coleccion_creada");
            } else {
                header("Location: ../admin/admin.php?seccion=colecciones&error=nombre_vacio");
            }
            break;
            
        case 'actualizarColeccion':
            $idCol = isset($_POST['id_coleccion']) ? $_POST['id_coleccion'] : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : "";
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : "";
            $nuevoEstado = isset($_POST['nuevo_estado']) ? $_POST['nuevo_estado'] : 2;
            
            $prodObj = new Producto($conexion);
            $prodObj->actualizarEstadoColeccion($idCol, $nombre, $descripcion, $nuevoEstado);
            header("Location: ../admin/admin.php?seccion=colecciones&mensaje=coleccion_actualizada");
            break;

        // --- LOOKS (Bloqueados para el amigo, solo SuperAdmin los ve en el menú) ---
        case 'crearLook':
            $prendasRaw = $_POST['prendas'] ?? [];
            $prendasLimpias = [];
            foreach ($prendasRaw as $combo) {
                if (!empty($combo) && strpos($combo, '_') !== false) {
                    list($pId, $cId) = explode('_', $combo);
                    $prendasLimpias[] = ['producto_id' => $pId, 'color_id' => $cId];
                }
            }
            $lookObj = new Look($conexion);
            if ($lookObj->crearLook($prendasLimpias)) {
                header("Location: ../admin/admin.php?seccion=looks&mensaje=look_creado");
            } else {
                header("Location: ../admin/admin.php?seccion=looks&error=error_creacion");
            }
            break;

        case 'editarLook':
            $idLook = $_POST['id_look'] ?? 0;
            $activo = $_POST['activo'] ?? 1;
            $prendasRaw = $_POST['prendas'] ?? [];
            $prendasLimpias = [];
            foreach ($prendasRaw as $combo) {
                if (!empty($combo) && strpos($combo, '_') !== false) {
                    list($pId, $cId) = explode('_', $combo);
                    $prendasLimpias[] = ['producto_id' => $pId, 'color_id' => $cId];
                }
            }
            $lookObj = new Look($conexion);
            $lookObj->editarLook($idLook, $activo, $prendasLimpias);
            header("Location: ../admin/admin.php?seccion=looks&mensaje=look_actualizado");
            break;

        case 'eliminarLook':
            $id = $_POST['id_look'] ?? 0;
            $lookObj = new Look($conexion);
            $lookObj->eliminarLook($id);
            header("Location: ../admin/admin.php?seccion=looks&mensaje=look_eliminado");
            break;

        default:
            header("Location: ../admin/admin.php");
            break;
    }
} else {
    header("Location: ../admin/admin.php");
}
exit();
?>