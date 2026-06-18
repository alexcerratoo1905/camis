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

$accion = $_REQUEST["accion"] ?? "";

if (!empty($accion)) {

    switch ($accion) {
        
        // ==============================================
        // AÑADIR UNA NUEVA EQUIPACIÓN (VARIANTE)
        // ==============================================
        case 'anadirEquipacionExtra':
            $producto_id = (int)$_POST['producto_id'];
            $equipacion = $_POST['equipacion']; 
            $paginaRetorno = (int)($_POST['pagina_retorno'] ?? 1);

            try {
                $conexion->beginTransaction();

                // 1. Buscamos o creamos el Color
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

                // 2. Vinculamos color al producto
                $stmtLink = $conexion->prepare("INSERT IGNORE INTO producto_colores (producto_id, color_id) VALUES (?, ?)");
                $stmtLink->execute([$producto_id, $color_id]);

                // 3. Inyectar las tallas en la BBDD para que el producto no se oculte
                $tallas = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'];
                $stmtTalla = $conexion->prepare("INSERT IGNORE INTO producto_tallas (producto_id, color_id, talla, stock) VALUES (?, ?, ?, 0)");
                foreach($tallas as $t) {
                    $stmtTalla->execute([$producto_id, $color_id, $t]);
                }

                // 4. Subimos las fotos
                if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                    $totalImagenes = count($_FILES['imagenes']['name']);
                    $rutaDirectorio = __DIR__ . '/../public/img/';
                    
                    for ($i = 0; $i < $totalImagenes; $i++) {
                        if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                            $nombreOriginal = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['imagenes']['name'][$i]));
                            $nombreArchivo = time() . '_var_' . $i . '_' . $nombreOriginal;
                            
                            if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $rutaDirectorio . $nombreArchivo)) {
                                $urlBD = 'public/img/' . $nombreArchivo;
                                $es_principal = ($i === 0) ? 1 : 0;
                                
                                $stmtImg = $conexion->prepare("INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) VALUES (?, ?, ?, ?)");
                                $stmtImg->execute([$producto_id, $color_id, $urlBD, $es_principal]);
                            }
                        }
                    }
                }

                $conexion->commit();
                header("Location: ../admin/admin.php?seccion=productos&pagina=$paginaRetorno&mensaje=variante_anadida");
                exit();

            } catch (Exception $e) {
                $conexion->rollBack();
                die("Error SQL: " . $e->getMessage());
            }
            break;

        case "actualizarInventarioMasivo":
            if ($_SERVER["REQUEST_METHOD"] !== "POST") exit();
            
            $nombres = isset($_POST['nombre']) ? $_POST['nombre'] : [];
            $descripciones = isset($_POST['descripcion']) ? $_POST['descripcion'] : [];
            $rebajas = isset($_POST['rebaja']) ? $_POST['rebaja'] : [];
            $estados = isset($_POST['activo']) ? $_POST['activo'] : [];
            $precios = isset($_POST['precio']) ? $_POST['precio'] : []; 
            $colecciones = isset($_POST['coleccion']) ? $_POST['coleccion'] : []; 
            $pagRetorno = isset($_POST['pagina_retorno']) ? $_POST['pagina_retorno'] : 1;

            try {
                $conexion->beginTransaction();
                
                foreach ($rebajas as $idPrenda => $valorRebaja) {
                    $nombreAct = trim($nombres[$idPrenda] ?? '');
                    $descAct = trim($descripciones[$idPrenda] ?? '');
                    $estadoActivo = $estados[$idPrenda];
                    $precioActualizado = $precios[$idPrenda] ?? 0.00;
                    $coleccionActualizada = !empty($colecciones[$idPrenda]) ? $colecciones[$idPrenda] : null; 

                    $sqlUp = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, rebaja = ?, activo = ?, coleccion_id = ? WHERE id = ?";
                    $stmtUp = $conexion->prepare($sqlUp);
                    $stmtUp->execute([$nombreAct, $descAct, $precioActualizado, $valorRebaja, $estadoActivo, $coleccionActualizada, $idPrenda]);
                }
                
                $conexion->commit();
                header("Location: ../admin/admin.php?seccion=productos&pagina=$pagRetorno&mensaje=inventario_actualizado");
                exit();
            } catch (Exception $e) {
                $conexion->rollBack();
                die("Error crítico al procesar inventario: " . $e->getMessage());
            }
            break;

        case 'borrarFotoEspecifica':
            $idFoto = (int)($_GET['id_foto'] ?? 0);
            $idProducto = (int)($_GET['p_id'] ?? 0);
            $paginaRetorno = (int)($_GET['pag'] ?? 1);

            if ($idFoto > 0) {
                try {
                    $stmtSearch = $conexion->prepare("SELECT url_imagen FROM imagenes_productos WHERE id = ?");
                    $stmtSearch->execute([$idFoto]);
                    $fotoData = $stmtSearch->fetch(PDO::FETCH_ASSOC);

                    if ($fotoData) {
                        $rutaFisica = __DIR__ . '/../' . $fotoData['url_imagen'];
                        if (file_exists($rutaFisica)) unlink($rutaFisica);
                    }

                    $stmtDel = $conexion->prepare("DELETE FROM imagenes_productos WHERE id = ?");
                    $stmtDel->execute([$idFoto]);

                    header("Location: ../admin/admin.php?seccion=productos&pagina=$paginaRetorno&mensaje=foto_eliminada");
                    exit();
                } catch (Exception $e) {
                    die("Error al eliminar la foto: " . $e->getMessage());
                }
            }
            header("Location: ../admin/admin.php?seccion=productos&pagina=$paginaRetorno&error=1");
            exit();
            break;

        case 'anadirFotosGaleriaExistente':
            $idProducto = (int)($_POST['producto_id'] ?? 0);
            $color_id = (int)($_POST['color_id'] ?? 1); 
            $paginaRetorno = (int)($_POST['pagina_retorno'] ?? 1);

            if ($idProducto > 0 && isset($_FILES['imagenes'])) {
                try {
                    $conexion->beginTransaction();
                    
                    $stmtCheck = $conexion->prepare("SELECT id FROM imagenes_productos WHERE producto_id = ? AND color_id = ? AND es_principal = 1");
                    $stmtCheck->execute([$idProducto, $color_id]);
                    $tienePrincipal = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                    $es_principal = $tienePrincipal ? 0 : 1;

                    $totalImagenes = count($_FILES['imagenes']['name']);
                    $rutaDirectorio = __DIR__ . '/../public/img/';
                    
                    for ($i = 0; $i < $totalImagenes; $i++) {
                        if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                            $nombreOriginal = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['imagenes']['name'][$i]));
                            $nombreArchivo = time() . '_add_' . $i . '_' . $nombreOriginal;
                            
                            if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $rutaDirectorio . $nombreArchivo)) {
                                $urlBD = 'public/img/' . $nombreArchivo;
                                $stmtImg = $conexion->prepare("INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) VALUES (?, ?, ?, ?)");
                                $stmtImg->execute([$idProducto, $color_id, $urlBD, $es_principal]);
                                $es_principal = 0;
                            }
                        }
                    }
                    $conexion->commit();
                    header("Location: ../admin/admin.php?seccion=productos&pagina=$paginaRetorno&mensaje=fotos_anadidas");
                    exit();
                } catch (Exception $e) {
                    $conexion->rollBack();
                    die("Error al añadir fotos: " . $e->getMessage());
                }
            }
            header("Location: ../admin/admin.php?seccion=productos&pagina=$paginaRetorno&error=1");
            exit();
            break;

        case 'crearPrendaTienda':
            if ($_SERVER["REQUEST_METHOD"] !== "POST") exit();
            $nombre = trim($_POST['nombre']);
            $precio = $_POST['precio'];
            $coleccion_id = $_POST['coleccion_id']; 
            $descripcion = $_POST['descripcion'] ?? '';
            $equipacion = $_POST['equipacion']; 

            try {
                $conexion->beginTransaction();

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

                $sqlInsertProd = "INSERT INTO productos (nombre, precio, descripcion, coleccion_id, genero, tipo_id, activo) 
                                  VALUES (?, ?, ?, ?, 3, 1, 1)";
                $stmtProd = $conexion->prepare($sqlInsertProd);
                $stmtProd->execute([$nombre, $precio, $descripcion, $coleccion_id]);
                $producto_id = $conexion->lastInsertId();

                $stmtProdColor = $conexion->prepare("INSERT INTO producto_colores (producto_id, color_id) VALUES (?, ?)");
                $stmtProdColor->execute([$producto_id, $color_id]);

                // Inyectar las tallas en la BBDD
                $tallas = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'];
                $stmtTalla = $conexion->prepare("INSERT IGNORE INTO producto_tallas (producto_id, color_id, talla, stock) VALUES (?, ?, ?, 0)");
                foreach($tallas as $t) {
                    $stmtTalla->execute([$producto_id, $color_id, $t]);
                }

                if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                    $totalImagenes = count($_FILES['imagenes']['name']);
                    $rutaDirectorio = __DIR__ . '/../public/img/';
                    
                    for ($i = 0; $i < $totalImagenes; $i++) {
                        $errorSubida = $_FILES['imagenes']['error'][$i];
                        if ($errorSubida === UPLOAD_ERR_OK) {
                            $nombreOriginal = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES['imagenes']['name'][$i]));
                            $nombreArchivo = time() . '_' . $i . '_' . $nombreOriginal;
                            
                            if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $rutaDirectorio . $nombreArchivo)) {
                                $urlBD = 'public/img/' . $nombreArchivo;
                                $es_principal = ($i === 0) ? 1 : 0; 
                                
                                $stmtImg = $conexion->prepare("INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) VALUES (?, ?, ?, ?)");
                                $stmtImg->execute([$producto_id, $color_id, $urlBD, $es_principal]);
                            }
                        }
                    }
                }

                $conexion->commit();
                header("Location: ../admin/admin.php?seccion=productos&mensaje=prenda_subida");
                exit();

            } catch (Exception $e) {
                $conexion->rollBack();
                die("Error SQL: " . $e->getMessage());
            }
            break;

        case "cambiarEstadoPedido":
            if ($_SERVER["REQUEST_METHOD"] !== "POST") exit();
            $idPedido = isset($_POST["idPedido"]) ? $_POST["idPedido"] : 0;
            $nuevoEstado = isset($_POST["nuevoEstado"]) ? trim($_POST["nuevoEstado"]) : "";
            $pedido->actualizarEstadoPedido($idPedido, $nuevoEstado);
            header("Location: ../admin/admin.php?seccion=pedidos&mensaje=estado_actualizado");
            break;
            
        case "crearColeccion":
            if ($_SERVER["REQUEST_METHOD"] !== "POST") exit();
            $nombre = isset($_POST['nombre_coleccion']) ? trim($_POST['nombre_coleccion']) : "";
            $descripcion = isset($_POST['descripcion_coleccion']) ? trim($_POST['descripcion_coleccion']) : "";
            if (!empty($nombre)) {
                $prodObj = new Producto($conexion);
                $prodObj->crearColeccion($nombre, $descripcion);
                header("Location: ../admin/admin.php?seccion=colecciones&mensaje=coleccion_creada");
            }
            break;
            
        case 'actualizarColeccion':
            if ($_SERVER["REQUEST_METHOD"] !== "POST") exit();
            $idCol = isset($_POST['id_coleccion']) ? $_POST['id_coleccion'] : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : "";
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : "";
            $nuevoEstado = isset($_POST['nuevo_estado']) ? $_POST['nuevo_estado'] : 2;
            
            // Recogemos la rebaja masiva (Si está vacío, no se cambia nada)
            $descuentoMasivo = isset($_POST['descuento_masivo']) && $_POST['descuento_masivo'] !== "" ? (int)$_POST['descuento_masivo'] : null;

            $prodObj = new Producto($conexion);
            $prodObj->actualizarEstadoColeccion($idCol, $nombre, $descripcion, $nuevoEstado);
            
            // LA MAGIA: Si has escrito un número de descuento, se lo aplica a todas las prendas de esa colección
            if (is_numeric($descuentoMasivo)) {
                $stmtRebaja = $conexion->prepare("UPDATE productos SET rebaja = ? WHERE coleccion_id = ?");
                $stmtRebaja->execute([$descuentoMasivo, $idCol]);
            }

            header("Location: ../admin/admin.php?seccion=colecciones&mensaje=coleccion_actualizada");
            break;
    }
} else {
    header("Location: ../admin/admin.php");
}
exit();
?>