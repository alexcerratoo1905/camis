<?php

class Producto
{
    private $conexionDataBase;
    private $idColecion;
    private $idTipoProducto;
    private $nombreProducto;
    private $descripcionProducto;
    private $precioProducto;
    private $stock;
    private $colorProducto;
    private $destacado;
    private $fechaProducto;
    private $tallaProducto;
    private $esSegundaMano;
    private $idUsuarioVendedor;
    private $estadoRevision;

    public function __construct($db)
    {
        $this->conexionDataBase = $db;
    }

    /**
     * Get the value of idColecion
     */
    public function getIdColecion()
    {
        return $this->idColecion;
    }

    /**
     * Set the value of idColecion
     *
     * @return  self
     */
    public function setIdColecion($idColecion)
    {
        $this->idColecion = $idColecion;

        return $this;
    }

    /**
     * Get the value of idTipoProducto
     */
    public function getIdTipoProducto()
    {
        return $this->idTipoProducto;
    }

    /**
     * Set the value of idTipoProducto
     *
     * @return  self
     */
    public function setIdTipoProducto($idTipoProducto)
    {
        $this->idTipoProducto = $idTipoProducto;

        return $this;
    }

    /**
     * Get the value of nombreProducto
     */
    public function getNombreProducto()
    {
        return $this->nombreProducto;
    }

    /**
     * Set the value of nombreProducto
     *
     * @return  self
     */
    public function setNombreProducto($nombreProducto)
    {
        $this->nombreProducto = $nombreProducto;

        return $this;
    }

    /**
     * Get the value of descripcionProducto
     */
    public function getDescripcionProducto()
    {
        return $this->descripcionProducto;
    }

    /**
     * Set the value of descripcionProducto
     *
     * @return  self
     */
    public function setDescripcionProducto($descripcionProducto)
    {
        $this->descripcionProducto = $descripcionProducto;

        return $this;
    }

    /**
     * Get the value of precioProducto
     */
    public function getPrecioProducto()
    {
        return $this->precioProducto;
    }

    /**
     * Set the value of precioProducto
     *
     * @return  self
     */
    public function setPrecioProducto($precioProducto)
    {
        $this->precioProducto = $precioProducto;

        return $this;
    }

    /**
     * Get the value of stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set the value of stock
     *
     * @return  self
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get the value of colorProducto
     */
    public function getColorProducto()
    {
        return $this->colorProducto;
    }

    /**
     * Set the value of colorProducto
     *
     * @return  self
     */
    public function setColorProducto($colorProducto)
    {
        $this->colorProducto = $colorProducto;

        return $this;
    }

    /**
     * Get the value of fechaProducto
     */
    public function getFechaProducto()
    {
        return $this->fechaProducto;
    }

    /**
     * Set the value of fechaProducto
     *
     * @return  self
     */
    public function setFechaProducto($fechaProducto)
    {
        $this->fechaProducto = $fechaProducto;

        return $this;
    }

    /**
     * Get the value of destacado
     */
    public function getDestacado()
    {
        return $this->destacado;
    }

    /**
     * Set the value of destacado
     *
     * @return  self
     */
    public function setDestacado($destacado)
    {
        $this->destacado = $destacado;

        return $this;
    }

    /**
     * Get the value of tallaProducto
     */
    public function getTallaProducto()
    {
        return $this->tallaProducto;
    }

    /**
     * Set the value of tallaProducto
     *
     * @return  self
     */
    public function setTallaProducto($tallaProducto)
    {
        $this->tallaProducto = $tallaProducto;

        return $this;
    }

    /**
     * Get the value of esSegundaMano
     */
    public function getEsSegundaMano()
    {
        return $this->esSegundaMano;
    }

    /**
     * Set the value of esSegundaMano
     *
     * @return  self
     */
    public function setEsSegundaMano($esSegundaMano)
    {
        $this->esSegundaMano = $esSegundaMano;

        return $this;
    }

    /**
     * Get the value of estadoRevision
     */
    public function getEstadoRevision()
    {
        return $this->estadoRevision;
    }

    /**
     * Set the value of estadoRevision
     *
     * @return  self
     */
    public function setEstadoRevision($estadoRevision)
    {
        $this->estadoRevision = $estadoRevision;

        return $this;
    }

    /**
     * Get the value of idUsuarioVendedor
     */
    public function getIdUsuarioVendedor()
    {
        return $this->idUsuarioVendedor;
    }

    /**
     * Set the value of idUsuarioVendedor
     *
     * @return  self
     */
    public function setIdUsuarioVendedor($idUsuarioVendedor)
    {
        $this->idUsuarioVendedor = $idUsuarioVendedor;

        return $this;
    }

    public function obtenerTallas($idPrenda)
    {
        $sql = "SELECT color_id, talla, stock FROM producto_tallas WHERE producto_id = :idPrenda";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":idPrenda" => $idPrenda]);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarProductos($estadoActivo, $limite = null)
    {
        $sql = "SELECT p.*, c.id as color_id, c.nombre as color_nombre, MIN(i.url_imagen) as url_imagen
                FROM productos p
                INNER JOIN producto_colores pc ON p.id = pc.producto_id
                INNER JOIN colores c ON pc.color_id = c.id
                INNER JOIN colecciones col ON p.coleccion_id = col.id 
                LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.color_id = c.id AND i.es_principal = 1
                WHERE p.activo = :estadoActivo AND col.activa = 1
                GROUP BY p.id, c.id
                ORDER BY p.creado_en DESC";

        if ($limite != null) {
            if (filter_var($limite, FILTER_VALIDATE_INT)) {
                $sql .= " LIMIT " . $limite;
            } else {
                $sql .= " LIMIT 20"; 
            }
        }

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":estadoActivo" => $estadoActivo]);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarInventarioCompleto()
    {
        $sql = "SELECT p.id as prenda_id, p.nombre, p.precio, p.rebaja, 
                       c.id as color_id, c.nombre as nombre_color, 
                       t.talla, IFNULL(t.stock, 0) as stock 
                FROM productos p
                LEFT JOIN producto_colores pc ON p.id = pc.producto_id
                LEFT JOIN colores c ON pc.color_id = c.id
                LEFT JOIN producto_tallas t ON p.id = t.producto_id AND c.id = t.color_id
                ORDER BY p.id DESC, c.nombre ASC, t.talla ASC";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarProductosPorTipo($esSegundaMano)
    {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE es_segunda_mano = :esm";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([':esm' => $esSegundaMano ? 1 : 0]);
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function listarProductosPaginados($esSegundaMano, $limite, $offset)
    {
        $sqlIds = "SELECT id FROM productos WHERE es_segunda_mano = :esm ORDER BY id DESC LIMIT :limite OFFSET :offset";
        $stmtIds = $this->conexionDataBase->prepare($sqlIds);
        $stmtIds->bindValue(':esm', (int)$esSegundaMano, PDO::PARAM_INT);
        $stmtIds->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmtIds->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmtIds->execute();
        $ids = $stmtIds->fetchAll(PDO::FETCH_COLUMN);

        if (empty($ids)) return [];

        $inQuery = implode(',', array_fill(0, count($ids), '?'));
        
        $sql = "SELECT p.id as prenda_id, p.nombre, p.precio, p.rebaja, p.activo, p.coleccion_id, 
                       p.es_segunda_mano, p.estado_revision, p.id_usuario_vendedor, u.nombre as nombre_dueno,
                       c.id as color_id, c.nombre as nombre_color, 
                       t.talla, IFNULL(t.stock, 0) as stock 
                FROM productos p
                LEFT JOIN usuarios u ON p.id_usuario_vendedor = u.id
                LEFT JOIN producto_colores pc ON p.id = pc.producto_id
                LEFT JOIN colores c ON pc.color_id = c.id
                LEFT JOIN producto_tallas t ON p.id = t.producto_id AND pc.color_id = t.color_id
                WHERE p.id IN ($inQuery)
                ORDER BY p.id DESC, c.nombre ASC, t.talla ASC";

        $stmt = $this->conexionDataBase->prepare($sql);
        foreach ($ids as $k => $id) {
            $stmt->bindValue(($k+1), $id);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarDatosBasicosPrenda($id, $rebaja, $activo, $precio = null, $coleccionId = null) {
        if ($precio !== null) { 
            if ($coleccionId === "") {
                $coleccionId = null;
            }
            $sql = "UPDATE productos SET rebaja = :rebaja, activo = :activo, precio = :precio, coleccion_id = :coleccion_id WHERE id = :id";
            $params = [':rebaja' => $rebaja, ':activo' => $activo, ':precio' => $precio, ':coleccion_id' => $coleccionId, ':id' => $id];
        } else { 
            $sql = "UPDATE productos SET rebaja = :rebaja, activo = :activo WHERE id = :id";
            $params = [':rebaja' => $rebaja, ':activo' => $activo, ':id' => $id];
        }
        
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute($params);
    }

    public function actualizarStockEspecifico($idP, $idC, $talla, $stock) {
        $sql = "UPDATE producto_tallas SET stock = :stock 
                WHERE producto_id = :idP AND color_id = :idC AND talla = :talla";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([':stock' => $stock, ':idP' => $idP, ':idC' => $idC, ':talla' => $talla]);
    }

    public function listarTiposPrendas()
    {
        $sql = "SELECT id, nombre from tipos_producto";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNombreColeccion($idColeccion)
    {
        $sql = "SELECT nombre FROM colecciones WHERE id = :idColeccion";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":idColeccion" => $idColeccion]);
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerTipoPrenda($idTipoPrenda)
    {
        $sql = "SELECT nombre FROM tipos_producto WHERE id = :idTipoPrenda";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":idTipoPrenda" => $idTipoPrenda]);
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function listarColecciones($modoAdmin = false)
    {
        if ($modoAdmin) {
            $sql = "SELECT * FROM colecciones  ORDER BY nombre ASC";
        } else {
            $sql = "SELECT * FROM colecciones WHERE activa = 1 ORDER BY nombre ASC";
        }
        
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearColeccion($nombre, $descripcion)
    {
        $sql = "INSERT INTO colecciones (nombre, descripcion, activa) VALUES (:nombre, :descripcion, 2)";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion
        ]);
    }

    public function actualizarEstadoColeccion($id, $nombre, $descripcion, $estado)
    {
        $sql = "UPDATE colecciones 
                SET nombre = :nombre, descripcion = :descripcion, activa = :estado 
                WHERE id = :id";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':estado' => $estado,
            ':id' => $id
        ]);
    }

    public function obtenerProducto($id)
    {
        $sql = "SELECT * FROM productos WHERE id = :idPrenda";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":idPrenda" => $id]);
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    private function obtenerSqlOrden($accion)
    {
        $ordenSql = " ORDER BY p.creado_en DESC";
        switch ($accion) {
            case 'precioAsc':
                $ordenSql = " ORDER BY p.precio ASC";
                break;
            case 'precioDesc':
                $ordenSql = " ORDER BY p.precio DESC";
                break;
            case 'nombreAsc':
                $ordenSql = " ORDER BY p.nombre ASC";
                break;
            case 'nombreDesc':
                $ordenSql = " ORDER BY p.nombre DESC";
                break;
            case 'fechaDesc':
                $ordenSql = " ORDER BY p.creado_en DESC";
                break;
            case 'fechaAsc':
                $ordenSql = " ORDER BY p.creado_en ASC";
                break;
        }
        return $ordenSql;
    }

    public function listaColores()
    {
        $sql = "SELECT id, nombre, valor_hexadecimal from colores";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerColoresPorProducto($idProducto)
    {
        try {
            $sql = "SELECT c.id, c.nombre, c.valor_hexadecimal 
                    FROM colores c
                    INNER JOIN producto_colores pc ON c.id = pc.color_id
                    WHERE pc.producto_id = :idProducto";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([":idProducto" => $idProducto]);
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Producto->obtenerColoresPorProducto: " . $e->getMessage());
            return [];
        }
    }

    public function filtrarCombinado($parametros, $esModoSecreto = false) {
        $activa = $esModoSecreto ? 3 : 1;
        
        $sql = "SELECT p.*, c.id as color_id, c.nombre as color_nombre, MIN(i.url_imagen) as url_imagen
                FROM productos p
                INNER JOIN producto_colores pc ON p.id = pc.producto_id
                INNER JOIN colores c ON pc.color_id = c.id
                INNER JOIN colecciones col ON p.coleccion_id = col.id
                LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.color_id = c.id AND i.es_principal = 1
                WHERE p.activo = 1 AND col.activa = $activa";
                
        $valoresFiltrado = [];

        if (!empty($parametros['genero'])) {
            $sql .= " AND p.genero = :genero";
            $valoresFiltrado[':genero'] = (int)$parametros['genero'];
        }
        if (!empty($parametros['coleccion'])) {
            $sql .= " AND p.coleccion_id = :coleccion";
            $valoresFiltrado[':coleccion'] = (int)$parametros['coleccion'];
        }
        if (!empty($parametros['tipo'])) {
            $sql .= " AND p.tipo_id = :tipo";
            $valoresFiltrado[':tipo'] = (int)$parametros['tipo'];
        }
        if (!empty($parametros['color'])) {
            $sql .= " AND c.nombre = :color";
            $valoresFiltrado[':color'] = $parametros['color'];
        }
        
        // ELIMINADO EL FILTRO ESTRICTO DE STOCK PARA QUE DROPSHIPPING FUNCIONE
        // Si eligen talla, simplemente lo ignoramos en la query porque siempre hay talla

        if (!empty($parametros['rebajas'])) {
            $sql .= " AND p.rebaja > 0";
        }
        if (isset($parametros['precioMin']) && isset($parametros['precioMax'])) {
            $sql .= " AND p.precio BETWEEN :min AND :max";
            $valoresFiltrado[':min'] = (float)$parametros['precioMin'];
            $valoresFiltrado[':max'] = (float)$parametros['precioMax'];
        }

        $sql .= " GROUP BY p.id, c.id";
        
        if (!empty($parametros['orden'])) {
            $sql .= $this->obtenerSqlOrden($parametros['orden']);
        } else {
             $sql .= " ORDER BY p.creado_en DESC";
        }

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute($valoresFiltrado);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarStock($idPrenda, $idColor, $talla, $cantidad)
    {
        try {
            $sql = "UPDATE producto_tallas SET stock = stock - :cantidad WHERE producto_id = :idPrenda AND color_id = :idColor AND talla = :talla";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":cantidad" => $cantidad,
                ":idPrenda" => $idPrenda,
                ":idColor" => $idColor,
                ":talla" => $talla
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en Producto->obtenerColoresPorProducto: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPrecioMinMax($valor, $esModoSecreto = false) {
        $funcionSql = "MAX";
        if (strtoupper($valor) === "MIN") {
            $funcionSql = "MIN";
        }
        $activa = $esModoSecreto ? 3 : 1;
        try {
            $sql = "SELECT {$funcionSql}(p.precio) as precio_limite
                    FROM productos p
                    INNER JOIN colecciones col ON p.coleccion_id = col.id
                    WHERE p.activo = 1 AND col.activa = $activa";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
            $precio = isset($resultado['precio_limite']) ? $resultado['precio_limite'] : 0;
            return round((float)$precio, 2);
        } catch (PDOException $e) {
            error_log("Error en Producto->obtenerPrecioMinMax: " . $e->getMessage());
            return 0;
        }
    }

    public function buscarPorNombre($nombreABuscar)
    {
        $sql = "SELECT p.id, p.nombre, p.precio, c.id AS color_id, c.nombre AS color_nombre, MIN(i.url_imagen) AS url_imagen
                FROM productos p
                INNER JOIN producto_colores pc ON p.id = pc.producto_id
                INNER JOIN colores c ON pc.color_id = c.id
                INNER JOIN colecciones col ON p.coleccion_id = col.id 
                LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.color_id = c.id AND i.es_principal = 1
                WHERE p.activo = 1 AND col.activa = 1 AND p.nombre LIKE :nombreABuscar
                GROUP BY p.id, c.id
                LIMIT 6";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":nombreABuscar" => "%" . $nombreABuscar . "%"]);

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // ARREGLADO: El ChatBot tampoco buscará por stock para que encuentre las prendas nuevas
    public function buscarPorNombreChatBot($nombreABuscar)
    {
        $sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, c.nombre AS color_nombre, 
                       MIN(i.url_imagen) AS url_imagen,
                       'Todas las tallas (S a 4XL)' AS tallas_stock
                FROM productos p
                INNER JOIN producto_colores pc ON p.id = pc.producto_id
                INNER JOIN colores c ON pc.color_id = c.id
                INNER JOIN colecciones col ON p.coleccion_id = col.id 
                LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.color_id = c.id AND i.es_principal = 1
                WHERE p.activo = 1 AND col.activa = 1 AND p.nombre LIKE :nombreABuscar
                GROUP BY p.id, c.id
                LIMIT 6";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":nombreABuscar" => "%" . $nombreABuscar . "%"]);

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function subirPrendasSegundaMano($nombrePrenda, $precioPrenda, $idUsu, $imgPrenda, $idColor, $tallaPrenda, $idTipoPrenda)
    {
        try {
            $this->conexionDataBase->beginTransaction();

            $sql = "INSERT INTO productos (nombre, precio, tipo_id, activo, es_segunda_mano, id_usuario_vendedor, estado_revision) 
            VALUES (:nombre, :precio, :idTipoPrenda, 0, 1, :idUsuario, 'Pendiente')";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":nombre" => $nombrePrenda,
                ":precio" => $precioPrenda,
                ":idTipoPrenda" => $idTipoPrenda,
                ":idUsuario" => $idUsu
            ]);

            $idProductoInsertado = $this->conexionDataBase->lastInsertId();

            $sqlColor = "INSERT INTO producto_colores (producto_id, color_id) VALUES (:idProducto, :idColor)";
            $sentenciaColor = $this->conexionDataBase->prepare($sqlColor);
            $sentenciaColor->execute([
                ":idProducto" => $idProductoInsertado,
                ":idColor" => $idColor
            ]);

            $sqlImagen = "INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) 
                  VALUES (:idProducto, :idColor, :imagenUrl, 1)";
            $sentenciaImg = $this->conexionDataBase->prepare($sqlImagen);
            $sentenciaImg->execute([
                ":idProducto" => $idProductoInsertado,
                ":idColor" => $idColor,
                ":imagenUrl" => $imgPrenda
            ]);

            $sqlTalla =  "INSERT INTO producto_tallas (producto_id, color_id, talla, stock) 
                 VALUES (:idProducto, :idColor, :talla, 1)";
            $sentenciaTalla = $this->conexionDataBase->prepare($sqlTalla);
            $sentenciaTalla->execute([
                ":idProducto" => $idProductoInsertado,
                ":idColor" => $idColor,
                ":talla" => $tallaPrenda
            ]);

            $this->conexionDataBase->commit();
            return true;
        } catch (Exception $e) {
            error_log("Error en Producto->obtenerColoresPorProducto: " . $e->getMessage());
            header("Location: ../segundaMano.php");
            return false;
        }
    }

    public function listarTodasTallas()
    {
        $sql = "SELECT DISTINCT talla from producto_tallas ";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerMisPrendasSegundaMano($idUsuario)
    {
        $sql = "SELECT p.*, pc.color_id, pt.talla, MIN(i.url_imagen) as url_imagen 
                FROM productos p 
                LEFT JOIN producto_colores pc ON p.id = pc.producto_id
                LEFT JOIN producto_tallas pt ON p.id = pt.producto_id
                LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.es_principal = 1
                WHERE p.id_usuario_vendedor = :idUsuario 
                GROUP BY p.id, pc.color_id, pt.talla
                ORDER BY p.creado_en DESC";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([":idUsuario" => $idUsuario]);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerImagenesProducto($idProducto)
    {
        $sql = "SELECT id, url_imagen FROM imagenes_productos WHERE producto_id = :id";
        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute([':id' => $idProducto]);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarDatosPrendaSegundaMano($idPrenda, $nombrePrenda, $precioPrenda, $tipoPrenda, $idUsu, $idColor, $talla)
    {

        try {
            $this->conexionDataBase->beginTransaction();

            $sql = "UPDATE productos SET nombre = :nombrePrenda, precio = :precioPrenda, tipo_id = :idTipoPrenda, estado_revision = 'Pendiente' WHERE id = :idPrenda AND id_usuario_vendedor = :idUsu";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ":nombrePrenda" => $nombrePrenda,
                ":precioPrenda" => $precioPrenda,
                ":idTipoPrenda" => $tipoPrenda,
                ":idPrenda" => $idPrenda,
                ":idUsu" => $idUsu
            ]);

            $sqlColor = "UPDATE producto_colores SET color_id = :idColor WHERE producto_id = :idPrenda";
            $sentenciaColor = $this->conexionDataBase->prepare($sqlColor);
            $sentenciaColor->execute([
                ":idColor" => $idColor,
                ":idPrenda" => $idPrenda
            ]);

            $sqlTalla = "UPDATE producto_tallas SET talla = :talla, color_id = :idColor WHERE producto_id = :idPrenda";

            $sentenciaTalla = $this->conexionDataBase->prepare($sqlTalla);
            $sentenciaTalla->execute([
                ":talla" => $talla,
                ":idColor" => $idColor,
                ":idPrenda" => $idPrenda
            ]);

            $this->conexionDataBase->commit();

            return true;
        } catch (PDOException $e) {
            error_log("Error en Producto->obtenerColoresPorProducto: " . $e->getMessage());
            header("Location: perfil.php?seccion=prendas");
            return false;
        }
    }

    public function borrarImagenPrenda($idImagen)
    {
        $sql = "DELETE FROM imagenes_productos WHERE id = :idImagen";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([":idImagen" => $idImagen]);
    }

    public function anadirImagenPrenda($idPrenda, $idColor, $urlImagen)
    {
        $sqlCheck = "SELECT count(*) FROM imagenes_productos WHERE producto_id = :id AND es_principal = 1";
        $sentenciaCheck = $this->conexionDataBase->prepare($sqlCheck);
        $sentenciaCheck->execute([":id" => $idPrenda]);
        $esPrincipal = ($sentenciaCheck->fetchColumn() == 0) ? 1 : 0;

        $sql = "INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) VALUES (:idPrenda, :idColor, :urlImagen, :esPrincipal)";
        $sentencia = $this->conexionDataBase->prepare($sql);
        return $sentencia->execute([
            ":idPrenda" => $idPrenda,
            ":idColor" => $idColor,
            ":urlImagen" => $urlImagen,
            ":esPrincipal" => $esPrincipal
        ]);
    }

    public function obtenerCatalogoSegundaMano($idUsuarioActual = null)
    {
        $sql = "SELECT p.*, pc.color_id, c.nombre as color_nombre, pt.talla, MIN(i.url_imagen) as url_imagen 
                FROM productos p 
                LEFT JOIN producto_colores pc ON p.id = pc.producto_id
                LEFT JOIN colores c ON pc.color_id = c.id
                LEFT JOIN producto_tallas pt ON p.id = pt.producto_id
                LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.es_principal = 1
                WHERE p.es_segunda_mano = 1 AND p.estado_revision = 'Aprobado' AND pt.stock > 0";

        if ($idUsuarioActual !== null) {
            $sql .= " AND p.id_usuario_vendedor != :idUsuario ";
        }

        $sql .= " GROUP BY p.id, pc.color_id, pt.talla, c.nombre ORDER BY p.creado_en DESC";

        $sentencia = $this->conexionDataBase->prepare($sql);

        if ($idUsuarioActual !== null) {
            $sentencia->execute([":idUsuario" => $idUsuarioActual]);
        } else {
            $sentencia->execute();
        }

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarCodigoDescuento($codigo, $email)
    {
        try {
            $sql = "SELECT * FROM codigos_accesos WHERE codigo = :codigo AND email = :email AND tipo = 'descuento' AND usado = 0";
            $sentencia = $this->conexionDataBase->prepare($sql);
            $sentencia->execute([
                ':codigo' => $codigo,
                ':email' => $email
            ]);
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en verificarCodigoDescuento: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerColeccionSecreta()
    {
        $sql = "SELECT p.*, c.id as color_id, c.nombre as color_nombre, MIN(i.url_imagen) as url_imagen
                FROM productos p
                INNER JOIN producto_colores pc ON p.id = pc.producto_id
                INNER JOIN colores c ON pc.color_id = c.id
                INNER JOIN colecciones col ON p.coleccion_id = col.id 
                LEFT JOIN imagenes_productos i ON p.id = i.producto_id AND i.color_id = c.id AND i.es_principal = 1
                WHERE p.activo = 1 AND col.activa = 3
                GROUP BY p.id, c.id
                ORDER BY p.creado_en DESC";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerColoresColeccionSecreta()
    {
        $sql = "SELECT DISTINCT c.* FROM colores c 
            INNER JOIN producto_colores pc ON c.id = pc.color_id 
            INNER JOIN productos p ON pc.producto_id = p.id 
            INNER JOIN colecciones col ON p.coleccion_id = col.id 
            WHERE p.activo = 1 AND col.activa = 3";

        $sentencia = $this->conexionDataBase->prepare($sql);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarRevisionSegundaMano($id, $estado, $idVendedor) {
        try {
            $sql = "UPDATE productos SET estado_revision = :estado, id_usuario_vendedor = :idV WHERE id = :id";
            $stmt = $this->conexionDataBase->prepare($sql);
            return $stmt->execute([
                ':estado' => (string)$estado, 
                ':idV' => (int)$idVendedor, 
                ':id' => (int)$id
            ]);
        } catch (PDOException $e) {
            error_log("Error actualizando segunda mano: " . $e->getMessage());
            return false;
        }
    }

    public function crearPrendaNueva($nombre, $descripcion, $precio, $tipo_id, $coleccion_id, $genero, $color_id, $tallas_stock, $urls_imagenes) {
        try {
            $this->conexionDataBase->beginTransaction();

            $precio = (float)$precio;
            $tipo_id = $tipo_id ? (int)$tipo_id : null;
            $coleccion_id = $coleccion_id ? (int)$coleccion_id : null;
            $genero = (int)$genero;
            $color_id = (int)$color_id;

            $sqlProd = "INSERT INTO productos (nombre, descripcion, precio, tipo_id, coleccion_id, genero, activo, es_segunda_mano, rebaja, talla, stock) 
                        VALUES (:nombre, :descripcion, :precio, :tipo_id, :coleccion_id, :genero, 1, 0, 0, 'Varias', 0)";
            $sentenciaProd = $this->conexionDataBase->prepare($sqlProd);
            $sentenciaProd->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':tipo_id' => $tipo_id,
                ':coleccion_id' => $coleccion_id,
                ':genero' => $genero
            ]);
            $idProducto = $this->conexionDataBase->lastInsertId();

            $sqlColor = "INSERT INTO producto_colores (producto_id, color_id) VALUES (:id_prod, :id_color)";
            $sentenciaColor = $this->conexionDataBase->prepare($sqlColor);
            $sentenciaColor->execute([':id_prod' => $idProducto, ':id_color' => $color_id]);

            $sqlTalla = "INSERT INTO producto_tallas (producto_id, color_id, talla, stock) VALUES (:id_prod, :id_color, :talla, :stock)";
            $sentenciaTalla = $this->conexionDataBase->prepare($sqlTalla);
            foreach ($tallas_stock as $talla => $stockVal) {
                if ((int)$stockVal > 0) {
                    $sentenciaTalla->execute([':id_prod' => $idProducto, ':id_color' => $color_id, ':talla' => $talla, ':stock' => (int)$stockVal]);
                }
            }

            if (!empty($urls_imagenes) && is_array($urls_imagenes)) {
                $sqlImg = "INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) VALUES (:id_prod, :id_color, :url_img, :es_principal)";
                $sentenciaImg = $this->conexionDataBase->prepare($sqlImg);
                foreach ($urls_imagenes as $index => $url) {
                    $esPrincipal = ($index === 0) ? 1 : 0; 
                    $sentenciaImg->execute([':id_prod' => $idProducto, ':id_color' => $color_id, ':url_img' => $url, ':es_principal' => $esPrincipal]);
                }
            }

            $this->conexionDataBase->commit();
            return true;
        } catch (Exception $e) {
            $this->conexionDataBase->rollBack();
            return $e->getMessage(); 
        }
    }

    public function anadirVariantePrenda($idProducto, $color_id, $tallas_stock, $urls_imagenes) {
        try {
            $this->conexionDataBase->beginTransaction();

            $sqlColor = "INSERT IGNORE INTO producto_colores (producto_id, color_id) VALUES (:id_prod, :id_color)";
            $sentenciaColor = $this->conexionDataBase->prepare($sqlColor);
            $sentenciaColor->execute([':id_prod' => $idProducto, ':id_color' => $color_id]);

            $sqlTalla = "INSERT INTO producto_tallas (producto_id, color_id, talla, stock) VALUES (:id_prod, :id_color, :talla, :stock) ON DUPLICATE KEY UPDATE stock = stock + :stock";
            $sentenciaTalla = $this->conexionDataBase->prepare($sqlTalla);
            foreach ($tallas_stock as $talla => $stockVal) {
                if ((int)$stockVal > 0) {
                    $sentenciaTalla->execute([':id_prod' => $idProducto, ':id_color' => $color_id, ':talla' => $talla, ':stock' => (int)$stockVal]);
                }
            }

            if (!empty($urls_imagenes) && is_array($urls_imagenes)) {
                $checkSql = "SELECT COUNT(*) FROM imagenes_productos WHERE producto_id = ? AND color_id = ? AND es_principal = 1";
                $stmtCheck = $this->conexionDataBase->prepare($checkSql);
                $stmtCheck->execute([$idProducto, $color_id]);
                $hasPrincipal = $stmtCheck->fetchColumn() > 0;

                $sqlImg = "INSERT INTO imagenes_productos (producto_id, color_id, url_imagen, es_principal) VALUES (:id_prod, :id_color, :url_img, :es_principal)";
                $sentenciaImg = $this->conexionDataBase->prepare($sqlImg);
                foreach ($urls_imagenes as $index => $url) {
                    $esPrincipal = (!$hasPrincipal && $index === 0) ? 1 : 0;
                    $sentenciaImg->execute([':id_prod' => $idProducto, ':id_color' => $color_id, ':url_img' => $url, ':es_principal' => $esPrincipal]);
                }
            }
            $this->conexionDataBase->commit();
            return true;
        } catch (Exception $e) {
            $this->conexionDataBase->rollBack();
            return $e->getMessage();
        }
    }
}
?>