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
        
        // ====================================================
        // BORRAR VARIANTE (EQUIPACIÓN) COMPLETA
        // ====================================================
        case 'borrarVariante':
            $idProducto = (int)($_GET['p_id'] ?? 0);
            $idColor = (int)($_GET['c_id'] ?? 0);
            $paginaRetorno = (int)($_GET['pag'] ?? 1);
            $filtroColeccion = (int)($_GET['filtro'] ?? 0);

            if ($idProducto > 0 && $idColor > 0) {
                try {
                    $conexion->beginTransaction();

                    // 1. Borrar archivos físicos
                    $stmtFotos = $conexion->prepare("SELECT url_imagen FROM imagenes_productos WHERE producto_id = ? AND color_id = ?");
                    $stmtFotos->execute([$idProducto, $idColor]);
                    $fotos = $stmtFotos->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($fotos as $foto) {
                        $rutaFisica = __DIR__ . '/../' . $foto['url_imagen'];
                        if (file_exists($rutaFisica)) {
                            unlink($rutaFisica);
                        }
                    }

                    // 2. Borrar BD
                    $stmtDelFotos = $conexion->prepare("DELETE FROM imagenes_productos WHERE producto_id = ? AND color_id = ?");
                    $stmtDelFotos->execute([$idProducto, $idColor]);

                    $stmtDelTallas = $conexion->prepare("DELETE FROM producto_tallas WHERE producto_id = ? AND color_id = ?");
                    $stmtDelTallas->execute([$idProducto, $idColor]);

                    $stmtDelColor = $conexion->prepare("DELETE FROM producto_colores WHERE producto_id = ? AND color_id = ?");
                    $stmtDelColor->execute([$idProducto, $idColor]);

                    $conexion->commit();
                    
                    $urlDestino = "../admin/admin.php?seccion=productos&pagina=$paginaRetorno&mensaje=variante_borrada";
                    if ($filtroColeccion > 0) $urlDestino .= "&filtro_coleccion=$filtroColeccion";
                    header("Location: $urlDestino");
                    exit();

                } catch (Exception $e) {
                    $conexion->rollBack();
                    die("Error al eliminar la variante: " . $e->getMessage());
                }
            }
            $urlDestinoErr = "../admin/admin.php?seccion=productos&pagina=$paginaRetorno&error=1";
            if ($filtroColeccion > 0) $urlDestinoErr .= "&filtro_coleccion=$filtroColeccion";
            header("Location: $urlDestinoErr");
            exit();
            break;

        case 'borrarColeccion':
            $idCol = (int)($_GET['id'] ?? 0);
            if ($idCol > 0) {
                try {
                    $stmt = $conexion->prepare("DELETE FROM colecciones WHERE id = ?");
                    $stmt->execute([$idCol]);
                    header("Location: ../admin/admin.php?seccion=colecciones&mensaje=coleccion_borrada");
                    exit();
                } catch (Exception $e) {
                    die("Error crítico al borrar la categoría: " . $e->getMessage());
                }
            }
            header("Location: ../admin/admin.php?seccion=colecciones&error=1");
            exit();
            break;

        case 'anadirEquipacionExtra':
            $producto_id = (int)$_POST['producto_id'];
            $equipacion = $_POST['equipacion']; 
            $paginaRetorno = (int)($_POST['pagina_retorno'] ?? 1);
            $filtroColeccion = (int)($_POST['filtro_coleccion_retorno'] ?? 0);

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

                $stmtLink = $conexion->prepare("INSERT IGNORE INTO producto_colores (producto_id, color_id) VALUES (?, ?)");
                $stmtLink->execute([$producto_id, $color_id]);

                $tallas = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'];
                $stmtTalla = $conexion->prepare("INSERT IGNORE INTO producto_tallas (producto_id, color_id, talla, stock) VALUES (?, ?, ?, 0)");
                foreach($tallas as $t) {
                    $stmtTalla->execute([$producto_id, $color_id, $t]);
                }

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
                
                $urlDestino = "../admin/admin.php?seccion=productos&pagina=$paginaRetorno&mensaje=variante_anadida";
                if ($filtroColeccion > 0) $urlDestino .= "&filtro_coleccion=$filtroColeccion";
                header("Location: $urlDestino");
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
            $destacados = isset($_POST['destacado']) ? $_POST['destacado'] : []; 
            $precios = isset($_POST['precio']) ? $_POST['precio'] : []; 
            $colecciones = isset($_POST['coleccion']) ? $_POST['coleccion'] : []; 
            $pagRetorno = isset($_POST['pagina_retorno']) ? $_POST['pagina_retorno'] : 1;
            $filtroColeccion = isset($_POST['filtro_coleccion_retorno']) ? (int)$_POST['filtro_coleccion_retorno'] : 0;

            // Determinar si ya se migró a la tabla intermedia
            $hasPivot = false;
            try {
                $conexion->query("SELECT 1 FROM producto_colecciones LIMIT 1");
                $hasPivot = true;
            } catch(PDOException $e) {}

            try {
                $conexion->beginTransaction();
                
                foreach ($rebajas as $idPrenda => $valorRebaja) {
                    $nombreAct = trim($nombres[$idPrenda] ?? '');
                    $descAct = trim($descripciones[$idPrenda] ?? '');
                    $estadoActivo = $estados[$idPrenda];
                    $estadoDestacado = $destacados[$idPrenda] ?? 0;
                    $precioActualizado = $precios[$idPrenda] ?? 0.00;
                    
                    $coleccionesActualizadas = isset($colecciones[$idPrenda]) && is_array($colecciones[$idPrenda]) ? $colecciones[$idPrenda] : [];
                    $primeraColeccion = !empty($coleccionesActualizadas) ? $coleccionesActualizadas[0] : null;

                    // Seguimos guardando el primer ID en productos.coleccion_id para que no rompa el front-end actual
                    $sqlUp = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, rebaja = ?, activo = ?, destacado = ?, coleccion_id = ? WHERE id = ?";
                    $stmtUp = $conexion->prepare($sqlUp);
                    $stmtUp->execute([$nombreAct, $descAct, $precioActualizado, $valorRebaja, $estadoActivo, $estadoDestacado, $primeraColeccion, $idPrenda]);
                    
                    // Si ya existe la tabla intermedia, insertamos todas
                    if ($hasPivot) {
                        $stmtDel = $conexion->prepare("DELETE FROM producto_colecciones WHERE producto_id = ?");
                        $stmtDel->execute([$idPrenda]);
                        
                        $stmtPivot = $conexion->prepare("INSERT IGNORE INTO producto_colecciones (producto_id, coleccion_id) VALUES (?, ?)");
                        foreach($coleccionesActualizadas as $cid) {
                            $stmtPivot->execute([$idPrenda, $cid]);
                        }
                    }
                }
                
                $conexion->commit();
                
                $urlDestino = "../admin/admin.php?seccion=productos&pagina=$pagRetorno&mensaje=inventario_actualizado";
                if ($filtroColeccion > 0) $urlDestino .= "&filtro_coleccion=$filtroColeccion";
                header("Location: $urlDestino");
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
            $filtroColeccion = (int)($_GET['filtro'] ?? 0);

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

                    $urlDestino = "../admin/admin.php?seccion=productos&pagina=$paginaRetorno&mensaje=foto_eliminada";
                    if ($filtroColeccion > 0) $urlDestino .= "&filtro_coleccion=$filtroColeccion";
                    header("Location: $urlDestino");
                    exit();
                } catch (Exception $e) {
                    die("Error al eliminar la foto: " . $e->getMessage());
                }
            }
            
            $urlDestinoErr = "../admin/admin.php?seccion=productos&pagina=$paginaRetorno&error=1";
            if ($filtroColeccion > 0) $urlDestinoErr .= "&filtro_coleccion=$filtroColeccion";
            header("Location: $urlDestinoErr");
            exit();
            break;

        case 'anadirFotosGaleriaExistente':
            $idProducto = (int)($_POST['producto_id'] ?? 0);
            $color_id = (int)($_POST['color_id'] ?? 1); 
            $paginaRetorno = (int)($_POST['pagina_retorno'] ?? 1);
            $filtroColeccion = (int)($_POST['filtro_coleccion_retorno'] ?? 0);

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
                    
                    $urlDestino = "../admin/admin.php?seccion=productos&pagina=$paginaRetorno&mensaje=fotos_anadidas";
                    if ($filtroColeccion > 0) $urlDestino .= "&filtro_coleccion=$filtroColeccion";
                    header("Location: $urlDestino");
                    exit();
                } catch (Exception $e) {
                    $conexion->rollBack();
                    die("Error al añadir fotos: " . $e->getMessage());
                }
            }
            
            $urlDestinoErr = "../admin/admin.php?seccion=productos&pagina=$paginaRetorno&error=1";
            if ($filtroColeccion > 0) $urlDestinoErr .= "&filtro_coleccion=$filtroColeccion";
            header("Location: $urlDestinoErr");
            exit();
            break;

        case 'crearPrendaTienda':
            if ($_SERVER["REQUEST_METHOD"] !== "POST") exit();
            $nombre = trim($_POST['nombre']);
            $precio = $_POST['precio'];
            $colecciones_ids = $_POST['coleccion_id'] ?? [];
            $descripcion = $_POST['descripcion'] ?? '';
            $equipacion = $_POST['equipacion']; 

            // Para no romper el front-end antiguo, insertamos al menos el primero en la tabla vieja
            $primera_coleccion = !empty($colecciones_ids) ? $colecciones_ids[0] : null;

            $hasPivot = false;
            try {
                $conexion->query("SELECT 1 FROM producto_colecciones LIMIT 1");
                $hasPivot = true;
            } catch(PDOException $e) {}

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
                $stmtProd->execute([$nombre, $precio, $descripcion, $primera_coleccion]);
                $producto_id = $conexion->lastInsertId();
                
                // Si existe la nueva tabla, insertamos TODAS las categorías
                if ($hasPivot) {
                    $stmtPivot = $conexion->prepare("INSERT IGNORE INTO producto_colecciones (producto_id, coleccion_id) VALUES (?, ?)");
                    foreach($colecciones_ids as $cid) {
                        $stmtPivot->execute([$producto_id, $cid]);
                    }
                }

                $stmtProdColor = $conexion->prepare("INSERT INTO producto_colores (producto_id, color_id) VALUES (?, ?)");
                $stmtProdColor->execute([$producto_id, $color_id]);

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
                
                $urlDestino = "../admin/admin.php?seccion=productos&mensaje=prenda_subida&filtro_coleccion=$primera_coleccion";
                header("Location: $urlDestino");
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
            $idCol = isset($_POST['id_coleccion']) ? (int)$_POST['id_coleccion'] : 0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : "";
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : "";
            $nuevoEstado = isset($_POST['nuevo_estado']) ? (int)$_POST['nuevo_estado'] : 2;
            
            $descuentoMasivo = isset($_POST['descuento_masivo']) && $_POST['descuento_masivo'] !== "" ? (int)$_POST['descuento_masivo'] : null;
            $precioMasivo = isset($_POST['precio_masivo']) && $_POST['precio_masivo'] !== "" ? (float)$_POST['precio_masivo'] : null;

            $prodObj = new Producto($conexion);
            $prodObj->actualizarEstadoColeccion($idCol, $nombre, $descripcion, $nuevoEstado);
            
            $hasPivot = false;
            try {
                $conexion->query("SELECT 1 FROM producto_colecciones LIMIT 1");
                $hasPivot = true;
            } catch(PDOException $e) {}

            // Aplicar descuento masivo
            if ($descuentoMasivo !== null) {
                if ($hasPivot) {
                    $stmtRebaja = $conexion->prepare("UPDATE productos p INNER JOIN producto_colecciones pc ON p.id = pc.producto_id SET p.rebaja = ? WHERE pc.coleccion_id = ?");
                } else {
                    $stmtRebaja = $conexion->prepare("UPDATE productos SET rebaja = ? WHERE coleccion_id = ?");
                }
                $stmtRebaja->execute([$descuentoMasivo, $idCol]);
            }
            
            // Aplicar precio masivo
            if ($precioMasivo !== null && $precioMasivo >= 0) {
                if ($hasPivot) {
                    $stmtPrecio = $conexion->prepare("UPDATE productos p INNER JOIN producto_colecciones pc ON p.id = pc.producto_id SET p.precio = ? WHERE pc.coleccion_id = ?");
                } else {
                    $stmtPrecio = $conexion->prepare("UPDATE productos SET precio = ? WHERE coleccion_id = ?");
                }
                $stmtPrecio->execute([$precioMasivo, $idCol]);
            }

            header("Location: ../admin/admin.php?seccion=colecciones&mensaje=coleccion_actualizada");
            break;
    }
} else {
    header("Location: ../admin/admin.php");
}
exit();
?>